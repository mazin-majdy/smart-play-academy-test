<?php

namespace App\Http\Controllers;

use App\Models\{Child, Game, GameSession, GameSessionAnswer, Question, ChildProgress};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PlaySessionController extends Controller
{
    /**
     * ✅ بدء جلسة لعب جديدة
     */
    public function start(Request $request, Child $child, Game $game)
    {
        // 1️⃣ التأكد أن الطفل تابع للمستخدم الحالي
        abort_unless(Auth::user()->children()->where('children.id', $child->id)->exists(), 403);

        // 2️⃣ التحقق من الحد اليومي
        $todayMinutes = $child->getTodayPlayMinutes();
        abort_if($todayMinutes >= $child->daily_limit_minutes, 429, 'لقد وصلت للحد اليومي المسموح');

        // 3️ إنشاء الجلسة
        $session = DB::transaction(function () use ($child, $game) {
            return $child->gameSessions()->create([
                'game_id' => $game->id,
                'started_at' => now(),
                'status' => 'active',
            ]);
        });

        // 4️ جلب الأسئلة المرتبطة باللعبة (مرتبة عشوائياً أو حسب الصعوبة)
        $questions = $game->questions()
            ->inRandomOrder()
            ->limit(10) // يمكن جعله ديناميكياً حسب إعدادات اللعبة
            ->get();

        return response()->json([
            'session_id' => $session->id,
            'questions' => $questions->map(fn($q) => [
                'id' => $q->id,
                'text' => $q->text,
                'options' => $q->options, // array
                'difficulty' => $q->difficulty,
            ]),
            'daily_limit_minutes' => $child->daily_limit_minutes,
            'today_played_minutes' => $todayMinutes,
        ]);
    }

    /**
     * ✅ تسجيل إجابة أثناء الجلسة
     */
    public function submitAnswer(Request $request, GameSession $session)
    {
        $session->load('child');

        // 1️⃣ صلاحيات + حالة الجلسة
        abort_unless(Auth::user()->children->contains($session->child), 403);
        abort_unless($session->status === 'active', 400, 'الجلسة غير نشطة');

        // 2️⃣ تحقق من البيانات
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'given_answer' => 'required|string|max:500',
            'time_taken' => 'required|integer|min:0',
        ]);

        // 3️ منع الإجابة المكررة لنفس السؤال
        $alreadyAnswered = GameSessionAnswer::where('game_session_id', $session->id)
            ->where('question_id', $validated['question_id'])
            ->exists();
        abort_if($alreadyAnswered, 409, 'تمت الإجابة على هذا السؤال مسبقاً');

        // 4️⃣ حساب النتيجة
        $question = Question::findOrFail($validated['question_id']);
        $isCorrect = trim(strtolower($validated['given_answer'])) === trim(strtolower($question->correct_answer));

        // معادلة النقاط: أساسي + مكافأة صعوبة + مكافأة سرعة
        $basePoints = $isCorrect ? 10 : 0;
        $difficultyBonus = match ($question->difficulty) {
            'hard' => 5,
            'medium' => 3,
            default => 0
        };
        $speedBonus = $isCorrect && $validated['time_taken'] < 10 ? 2 : 0;
        $pointsEarned = $basePoints + $difficultyBonus + $speedBonus;

        // 5️⃣ حفظ الإجابة
        $answer = DB::transaction(function () use ($session, $validated, $question, $isCorrect, $pointsEarned) {
            return GameSessionAnswer::create([
                'game_session_id' => $session->id,
                'question_id' => $validated['question_id'],
                'child_id' => $session->child_id,
                'given_answer' => $validated['given_answer'],
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'time_taken_seconds' => $validated['time_taken'],
                'points_earned' => $pointsEarned,
                'difficulty_level' => $question->difficulty,
                'answered_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
            'next_question_available' => $session->answers()->count() < 10, // حسب عدد الأسئلة
        ]);
    }

    /**
     * ✅ إنهاء الجلسة وتحديث إحصائيات الطفل
     */
    public function finish(GameSession $session)
    {
        $session->load(['child', 'answers']);

        abort_unless(Auth::user()->children->contains($session->child), 403);
        abort_unless($session->status === 'active', 400, 'الجلسة منتهية بالفعل');

        DB::transaction(function () use ($session) {
            // 1️⃣ إنهاء الجلسة
            $session->update([
                'finished_at' => now(),
                'status' => 'completed',
                'total_minutes' => now()->diffInMinutes($session->started_at),
            ]);

            $child = $session->child;
            $answers = $session->answers;
            $correctCount = $answers->where('is_correct', true)->count();
            $totalPoints = $answers->sum('points_earned');
            $accuracy = $answers->count() > 0 ? round(($correctCount / $answers->count()) * 100) : 0;

            // 2️⃣ تحديث الطفل (نجوم، XP، مستوى، Streak)
            $starsEarned = match (true) {
                $accuracy >= 90 => 3,
                $accuracy >= 70 => 2,
                $accuracy >= 50 => 1,
                default => 0
            };

            $xpGained = $totalPoints + ($starsEarned * 5);
            $newXp = $child->xp + $xpGained;
            $newLevel = floor($newXp / 100) + 1; // كل 100 XP مستوى جديد

            $child->update([
                'total_stars' => $child->total_stars + $starsEarned,
                'xp' => $newXp,
                'current_level' => $newLevel,
                'last_played_at' => now(),
            ]);

            // 3️⃣ تحديث الـ Streak
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();

            if ($child->last_played_at?->toDateString() === $yesterday) {
                $child->increment('streak_days');
            } elseif ($child->last_played_at?->toDateString() !== $today) {
                $child->update(['streak_days' => 1]); // كسر الـ Streak وبداية جديد
            }

            // 4️⃣ تحديث تقدم الموضوع (Mastery Score)
            $game = $session->game;
            $topic = $game->topic;
            if ($topic) {
                $progress = ChildProgress::firstOrCreate(
                    ['child_id' => $child->id, 'topic_id' => $topic->id],
                    ['mastery_score' => 0, 'attempts' => 0]
                );

                // تحديث المتوسط المرجح للدقة
                $newAttempts = $progress->attempts + 1;
                $newMastery = round((($progress->mastery_score * $progress->attempts) + $accuracy) / $newAttempts);

                $progress->update([
                    'mastery_score' => $newMastery,
                    'attempts' => $newAttempts,
                    'last_played_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'accuracy' => $session->answers->count() > 0
                ? round(($session->answers->where('is_correct', true)->count() / $session->answers->count()) * 100)
                : 0,
            'total_points' => $session->answers->sum('points_earned'),
            'redirect_url' => route('dashboard.reports.show', $session->child_id)
        ]);
    }
}
