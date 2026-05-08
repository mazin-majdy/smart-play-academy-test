<?php

// ══════════════════════════════════════════════════════════════════
// app/Jobs/GenerateWeeklyReportJob.php
// يولّد التقرير الأسبوعي بالـ AI كل أحد
// ══════════════════════════════════════════════════════════════════
namespace App\Jobs;

use App\Models\{Child, WeeklyReport, GameSession, ChildProgress};
use App\Models\ParentNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateWeeklyReportJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $timeout = 120;

    public function __construct(public int $childId) {}

    public function handle(): void
    {
        $child     = Child::with('progress.topic')->find($this->childId);
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        // منع التكرار
        if (WeeklyReport::where('child_id', $child->id)
            ->where('week_start', $weekStart->toDateString())
            ->exists()
        ) {
            return;
        }

        // ── جمع بيانات الأسبوع ──────────────────────────────────
        $sessions = GameSession::where('child_id', $child->id)
            ->whereBetween('started_at', [$weekStart, $weekEnd])
            ->where('status', 'completed')
            ->get();

        $totalSessions = $sessions->count();
        $totalMinutes  = (int) ($sessions->sum('duration_seconds') / 60);
        $totalStars    = $sessions->sum('stars_earned');

        // الموضوعات المُمارَسة
        $topicIds = $sessions->pluck('topic_id')->unique();

        // الفجوات: موضوعات mastery < 60%
        $gaps = ChildProgress::where('child_id', $child->id)
            ->where('mastery_score', '<', 60)
            ->where('mastery_score', '>', 0)
            ->with('topic')
            ->get()
            ->map(fn($p) => [
                'topic'      => $p->topic?->name,
                'mastery'    => $p->mastery_score,
                'difficulty' => $p->current_difficulty,
            ])
            ->toArray();

        // ── توليد الملخص بالـ AI ─────────────────────────────────
        $summary = $this->generateSummary($child, [
            'total_sessions' => $totalSessions,
            'total_minutes'  => $totalMinutes,
            'total_stars'    => $totalStars,
            'gaps'           => $gaps,
            'streak'         => $child->streak_days,
        ]);

        // ── الأنشطة الواقعية المقترحة ────────────────────────────
        $realActivities = $this->suggestRealActivities($child, $gaps);

        // ── حفظ التقرير ─────────────────────────────────────────
        WeeklyReport::create([
            'child_id'                  => $child->id,
            'week_start'                => $weekStart->toDateString(),
            'week_end'                  => $weekEnd->toDateString(),
            'total_sessions'            => $totalSessions,
            'total_minutes'             => $totalMinutes,
            'stars_earned'              => $totalStars,
            'topics_practiced'          => $topicIds->count(),
            'ai_summary'                => $summary,
            'gaps_detected'             => $gaps,
            'real_activities'           => $realActivities,
            'recommended_daily_minutes' => $this->calcRecommendedTime($child, $totalMinutes),
            'generated_at'              => now(),
        ]);

        // إشعار الأهل
        foreach ($child->parents as $parent) {
            ParentNotification::create([
                'user_id'  => $parent->id,
                'child_id' => $child->id,
                'type'     => 'weekly_report',
                'title'    => "📊 التقرير الأسبوعي لـ {$child->name} جاهز",
                'body'     => "جلسات: {$totalSessions} · دقائق: {$totalMinutes} · نجوم: {$totalStars} ⭐",
            ]);
        }
    }

    // protected function generateSummary(Child $child, array $data): string
    // {
    //     try {
    //         $prompt = <<<PROMPT
    //         اكتب ملخصاً أسبوعياً موجهاً للأهل عن أداء الطفل {$child->name} (عمر {$child->age_group} سنة).
    //         البيانات:
    //         - جلسات اللعب: {$data['total_sessions']}
    //         - إجمالي الوقت: {$data['total_minutes']} دقيقة
    //         - النجوم المكتسبة: {$data['total_stars']}
    //         - الأيام المتتالية: {$data['streak']}
    //         - الفجوات: " . json_encode($data['gaps'], JSON_UNESCAPED_UNICODE) . "

    //         الملخص يجب أن يكون:
    //         - إيجابي وتشجيعي للأهل
    //         - يذكر نقاط القوة أولاً
    //         - يقترح طريقة للمساعدة في نقاط الضعف
    //         - 3-4 جمل فقط باللغة العربية
    //         PROMPT;

    //         $response = OpenAI::chat()->create([
    //             'model'      => 'gpt-4o-mini',
    //             'max_tokens' => 200,
    //             'messages'   => [
    //                 ['role' => 'user', 'content' => $prompt],
    //             ],
    //         ]);

    //         return $response->choices[0]->message->content;

    //     } catch (\Exception $e) {
    //         return "أكمل {$child->name} {$data['total_sessions']} جلسات هذا الأسبوع وحصل على {$data['total_stars']} نجمة. استمر في التشجيع!";
    //     }
    // }


    protected function generateSummary(Child $child, array $data): string
    {
        try {
            // 1️⃣ تجهيز البيانات بشكل آمن (منع حقن الأوامر في الـ Prompt)
            $childName = htmlspecialchars($child->name, ENT_NOQUOTES, 'UTF-8');
            $ageGroup = (int) $child->age_group;
            $gapsJson = json_encode($data['gaps'] ?? [], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

            // 2️⃣ بناء الـ Prompt باستخدام متغيرات مُجهّزة مسبقاً
            $prompt = <<<PROMPT
اكتب ملخصاً أسبوعياً موجهاً للأهل عن أداء الطفل {$childName} (عمر {$ageGroup} سنة).

البيانات:
- جلسات اللعب: {$data['total_sessions']}
- إجمالي الوقت: {$data['total_minutes']} دقيقة
- النجوم المكتسبة: {$data['total_stars']}
- الأيام المتتالية: {$data['streak']}
- الفجوات: {$gapsJson}

الملخص يجب أن يكون:
- إيجابي وتشجيعي للأهل
- يذكر نقاط القوة أولاً
- يقترح طريقة بسيطة للمساعدة في نقاط التحسين
- 3-4 جمل فقط باللغة العربية الفصحى المبسطة
- لا تستخدم رموز أو تنسيق خاص، فقط نص عادي
PROMPT;

            // 3️⃣ استدعاء OpenAI API مع معالجة الأخطاء
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'max_tokens' => 200,
                'temperature' => 0.7, // لجعل الردود أكثر إبداعاً وثباتاً
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد تعليمي خبير في تحليل أداء الأطفال وكتابة تقارير للأهل.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            // 4️⃣ التحقق من وجود الرد قبل استخدامه
            if (empty($response->choices) || !isset($response->choices[0]->message->content)) {
                throw new \RuntimeException('OpenAI response is empty or malformed');
            }

            $summary = trim($response->choices[0]->message->content);

            // 5️⃣ تنظيف الرد النهائي (إزالة علامات الاقتباس الزائدة إن وُجدت)
            $summary = trim($summary, '"\'');

            return $summary;
        } catch (\Throwable $e) {
            // تسجيل الخطأ للـ Logs (مهم للـ Debug لاحقاً)
            Log::warning('OpenAI Summary Generation Failed', [
                'child_id' => $child->id,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ]);

            // 6️⃣ Fallback: جملة احتياطية احترافية عند الفشل
            $fallbacks = [
                "أداء رائع لـ {$child->name} هذا الأسبوع! أكمل {$data['total_sessions']} جلسات وحصل على {$data['total_stars']} نجوم. استمروا في التشجيع! 🌟",
                "مبارك لـ {$child->name} إنجاز {$data['total_sessions']} جلسة هذا الأسبوع. تقدم ملحوظ يستحق الفخر! 💪",
                "{$child->name} يبذل جهوداً ممتازة! {$data['total_minutes']} دقيقة من التعلم واللعب تستحق التقدير. 🎉",
            ];

            // اختيار جملة عشوائية لتجنب التكرار
            return $fallbacks[array_rand($fallbacks)];
        }
    }
    protected function suggestRealActivities(Child $child, array $gaps): array
    {
        if (empty($gaps)) {
            return [['activity' => 'استمر في اللعب اليومي للحفاظ على التقدم 🎯']];
        }

        // أنشطة بسيطة بدون AI
        $activities = [];
        foreach (array_slice($gaps, 0, 2) as $gap) {
            $activities[] = [
                'topic'    => $gap['topic'],
                'activity' => "تمرينات يدوية على موضوع '{$gap['topic']}' لمدة 10 دقائق يومياً",
            ];
        }
        return $activities;
    }

    protected function calcRecommendedTime(Child $child, int $weekMinutes): int
    {
        $dailyAvg = $weekMinutes / 7;
        // إذا متوسط اليوم أقل من 20 دقيقة، ارفعه
        if ($dailyAvg < 20) return 30;
        // إذا أكثر من 60 دقيقة، اقترح تقليله
        if ($dailyAvg > 60) return 45;
        return (int) $dailyAvg;
    }
}
