<?php
// قلب المشروع — يكيّف الصعوبة تلقائياً بناءً على أداء الطفل
// ══════════════════════════════════════════════════════════════════
namespace App\Services;

use App\Models\Child;
use App\Models\ChildProgress;
use App\Models\GameSession;
use App\Models\Topic;
use Illuminate\Support\Facades\Log;

class AdaptiveEngine
{
    // ── الثوابت ────────────────────────────────────────────────────
    const DIFFICULTY_UP_THRESHOLD   = 80; // نرفع الصعوبة إذا دقة > 80%
    const DIFFICULTY_DOWN_THRESHOLD = 50; // نخفّض الصعوبة إذا دقة < 50%
    const MIN_SESSIONS_TO_ADAPT     = 2;  // نحتاج جلستين على الأقل لنقرر
    const MAX_DIFFICULTY            = 5;
    const MIN_DIFFICULTY            = 1;

    // ── MAIN: بعد كل جلسة، نحدّث تقدم الطفل وندرس هل نغيّر الصعوبة
    public function processSession(GameSession $session): array
    {
        $child   = $session->child;
        $topicId = $session->topic_id;

        // 1. نجلب أو ننشئ سجل التقدم
        $progress = ChildProgress::firstOrCreate(
            ['child_id' => $child->id, 'topic_id' => $topicId],
            ['current_difficulty' => $session->difficulty_used]
        );

        // 2. نحدّث الإحصائيات
        $progress->increment('sessions_count');
        $progress->increment('correct_answers', $session->correct_count);
        $progress->increment('wrong_answers',   $session->wrong_count);
        $progress->increment('total_time_seconds', $session->duration_seconds);
        $progress->increment('hints_used',      $session->hints_used);
        $progress->update(['last_played_at'     => now()]);

        // 3. نحسب نقطة الإتقان الجديدة
        $newMastery = $this->calculateMastery($progress);
        $progress->update(['mastery_score' => $newMastery]);

        // 4. هل نغيّر الصعوبة؟
        $recommendation = $this->recommendDifficulty($progress, $session);

        if ($recommendation['should_change']) {
            $progress->update([
                'current_difficulty' => $recommendation['new_difficulty'],
            ]);

            // نسجّل في الـ session إنه تم تعديل الصعوبة
            $session->update(['difficulty_adjusted' => true]);

            Log::info("AdaptiveEngine: Child #{$child->id} topic #{$topicId} "
                . "difficulty {$recommendation['old_difficulty']} → "
                . "{$recommendation['new_difficulty']}");
        }

        // 5. نحدّث بيانات الأداء التفصيلية
        $this->updatePerformanceData($progress, $session);

        // 6. هل أتقن الموضوع؟
        if ($newMastery >= 80 && !$progress->completed_at) {
            $progress->update(['completed_at' => now()]);
            return ['mastered' => true, ...$recommendation];
        }

        return ['mastered' => false, ...$recommendation];
    }

    // ── حساب نقطة الإتقان (weighted average) ──────────────────────
    protected function calculateMastery(ChildProgress $progress): int
    {
        $total = $progress->correct_answers + $progress->wrong_answers;
        if ($total === 0) return 0;

        $accuracy = $progress->correct_answers / $total * 100;

        // نعطي وزناً أكبر للجلسات الأخيرة (لو عندنا بيانات)
        // للبساطة الآن: متوسط الدقة + عامل الاستمرارية
        $continuityBonus = min($progress->sessions_count * 2, 20); // max 20 نقطة إضافية

        return (int) min(100, $accuracy * 0.8 + $continuityBonus);
    }

    // ── هل نغيّر الصعوبة؟ ─────────────────────────────────────────
    protected function recommendDifficulty(ChildProgress $progress, GameSession $session): array
    {
        $current = $progress->current_difficulty;

        // نحتاج جلستين على الأقل قبل أي قرار
        if ($progress->sessions_count < self::MIN_SESSIONS_TO_ADAPT) {
            return [
                'should_change'    => false,
                'old_difficulty'   => $current,
                'new_difficulty'   => $current,
                'reason'           => 'not_enough_data',
            ];
        }

        // نحسب دقة آخر جلسة فقط
        $sessionTotal    = $session->correct_count + $session->wrong_count;
        $sessionAccuracy = $sessionTotal > 0
            ? ($session->correct_count / $sessionTotal * 100)
            : 0;

        // دقة عالية → ارفع الصعوبة
        if (
            $sessionAccuracy >= self::DIFFICULTY_UP_THRESHOLD
            && $current < self::MAX_DIFFICULTY
        ) {
            return [
                'should_change'  => true,
                'old_difficulty' => $current,
                'new_difficulty' => $current + 1,
                'reason'         => 'high_accuracy',
                'message'        => 'أحسنت! جاهز لتحدٍّ أصعب 🚀',
            ];
        }

        // دقة منخفضة → خفّض الصعوبة
        if (
            $sessionAccuracy <= self::DIFFICULTY_DOWN_THRESHOLD
            && $current > self::MIN_DIFFICULTY
        ) {
            return [
                'should_change'  => true,
                'old_difficulty' => $current,
                'new_difficulty' => $current - 1,
                'reason'         => 'low_accuracy',
                'message'        => 'لنراجع معاً خطوة خطوة 💪',
            ];
        }

        return [
            'should_change'  => false,
            'old_difficulty' => $current,
            'new_difficulty' => $current,
            'reason'         => 'on_track',
        ];
    }

    // ── تحديث بيانات الأداء التفصيلية (JSON) ─────────────────────
    protected function updatePerformanceData(ChildProgress $progress, GameSession $session): void
    {
        $existing = $progress->performance_data ?? [
            'strengths'        => [],
            'weaknesses'       => [],
            'avg_think_time'   => 0,
            'frustration_rate' => 0,
        ];

        // نحلل الإجابات للكشف عن patterns
        $answersLog = $session->answers_log ?? [];
        if (!empty($answersLog)) {
            $wrongAnswers = collect($answersLog)->where('correct', false);

            // إذا تكرر نفس الخطأ أكثر من مرة — نُضيفه كنقطة ضعف
            if ($wrongAnswers->count() >= 2) {
                $existing['weaknesses'][] = [
                    'topic_id'   => $session->topic_id,
                    'session_id' => $session->id,
                    'count'      => $wrongAnswers->count(),
                    'at'         => now()->toDateString(),
                ];
                // نبقي آخر 10 نقاط ضعف فقط
                $existing['weaknesses'] = array_slice($existing['weaknesses'], -10);
            }
        }

        // متوسط وقت التفكير من engagement_data
        $engData = $session->engagement_data ?? [];
        if (!empty($engData['avg_think_time'])) {
            $existing['avg_think_time'] = round(
                ($existing['avg_think_time'] + $engData['avg_think_time']) / 2,
                1
            );
        }

        $progress->update(['performance_data' => $existing]);
    }

    // ── الصعوبة المناسبة للجلسة القادمة ──────────────────────────
    public function getNextDifficulty(Child $child, int $topicId): int
    {
        $progress = ChildProgress::where('child_id', $child->id)
            ->where('topic_id', $topicId)
            ->first();

        return $progress?->current_difficulty ?? 1;
    }

    // ── اقتراح الموضوع التالي ─────────────────────────────────────
    public function suggestNextTopic(Child $child): ?Topic
    {
        // الموضوعات التي لم يُتقنها بعد مرتبة حسب الأولوية
        $inProgress = ChildProgress::where('child_id', $child->id)
            ->where('mastery_score', '<', 80)
            ->where('mastery_score', '>', 0)
            ->orderBy('mastery_score', 'desc') // الأقرب للإتقان أولاً
            ->first();

        if ($inProgress) {
            return Topic::find($inProgress->topic_id);
        }

        // لم يبدأ بعد — أعطه أسهل موضوع في فئته العمرية
        return Topic::where('age_group', $child->age_group)
            ->where('difficulty_level', 1)
            ->whereNotIn('id', function ($q) use ($child) {
                $q->select('topic_id')
                    ->from('child_progress')
                    ->where('child_id', $child->id);
            })
            ->where('is_active', true)
            ->first();
    }
}
