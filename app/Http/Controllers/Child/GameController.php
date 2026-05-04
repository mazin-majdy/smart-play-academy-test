<?php

// التحكم بالألعاب — بدء الجلسة، إرسال الإجابات، إنهاء الجلسة
// ══════════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\{Subject, Topic, Game, GameSession, Question};
use App\Services\{AdaptiveEngine, EngagementTracker};
use App\Jobs\CheckAchievementsJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    public function __construct(
        private AdaptiveEngine   $engine,
        private EngagementTracker $tracker
    ) {}

    // GET /play/subject/{subject}
    // قائمة الألعاب في هذه المادة
    public function subject(Request $request, Subject $subject)
    {
        $child  = $request->_child;
        $topics = Topic::where('subject_id', $subject->id)
            ->where('age_group', $child->age_group)
            ->where('is_active', true)
            ->with('games')
            ->orderBy('difficulty_level')
            ->get()
            ->map(function ($topic) use ($child) {
                $progress = $child->progress()
                    ->where('topic_id', $topic->id)
                    ->first();
                $topic->mastery      = $progress?->mastery_score ?? 0;
                $topic->is_locked    = false; // يمكن تفعيل القفل لاحقاً
                return $topic;
            });

        return view('child.subject', compact('child', 'subject', 'topics'));
    }

    // GET /play/game/{game}
    // صفحة اللعبة — تحمّل الـ JS component المناسب
    public function play(Request $request, Game $game)
    {
        $child      = $request->_child;
        $difficulty = $this->engine->getNextDifficulty($child, $game->topic_id);

        // نحضّر الأسئلة مسبقاً (10 أسئلة)
        $questions = $game->questionsForDifficulty($difficulty)
            ->take(10)
            ->get()
            ->map(fn($q) => [
                'id'          => $q->id,
                'content'     => $q->content,
                'content_type' => $q->content_type,
                'media_path'  => $q->media_path,
                'answers'     => collect($q->answers)->map(fn($a) => [
                    'id'   => $a['id'],
                    'text' => $a['text'],
                    // لا نرسل is_correct للـ frontend
                ])->shuffle()->values(),
                'answer_type' => $q->answer_type,
                'hint'        => $q->hint,
            ]);

        return view('child.game', compact('child', 'game', 'questions', 'difficulty'));
    }

    // POST /play/session/start
    public function startSession(Request $request): JsonResponse
    {
        $data = $request->validate([
            'game_id'    => 'required|exists:games,id',
            'difficulty' => 'required|integer|between:1,5',
        ]);

        $child   = $request->_child;
        $game    = Game::findOrFail($data['game_id']);

        $session = GameSession::create([
            'child_id'        => $child->id,
            'game_id'         => $game->id,
            'topic_id'        => $game->topic_id,
            'difficulty_used' => $data['difficulty'],
            'started_at'      => now(),
            'status'          => 'in_progress',
        ]);

        return response()->json([
            'session_id' => $session->id,
            'message'    => 'بالتوفيق! 🌟',
        ]);
    }

    // POST /play/session/{session}/answer
    // الطفل يرسل إجابة — نتحقق فوراً ونرد
    public function submitAnswer(Request $request, GameSession $session): JsonResponse
    {
        // تأكد إن هاي جلسة الطفل الحالي
        abort_if($session->child_id !== session('child_id'), 403);

        $data = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_id'   => 'required',
            'think_time'  => 'nullable|numeric', // كم ثانية فكّر
        ]);

        $question  = Question::findOrFail($data['question_id']);
        $answers   = collect($question->answers);
        $correct   = $answers->firstWhere('is_correct', true);
        $isCorrect = (string) $data['answer_id'] === (string) $correct['id'];

        // تحديث عداد الجلسة
        if ($isCorrect) {
            $session->increment('correct_count');
        } else {
            $session->increment('wrong_count');
        }

        // نضيف الإجابة لـ answers_log
        $log   = $session->answers_log ?? [];
        $log[] = [
            'q_id'      => $question->id,
            'answer_id' => $data['answer_id'],
            'correct'   => $isCorrect,
            'time'      => $data['think_time'] ?? 0,
        ];
        $session->update(['answers_log' => $log]);

        // تحديث إحصائية السؤال
        $question->increment('times_used');
        // نحدّث success_rate تدريجياً
        $newRate = (($question->success_rate * ($question->times_used - 1))
            + ($isCorrect ? 100 : 0)) / $question->times_used;
        $question->update(['success_rate' => round($newRate, 2)]);

        return response()->json([
            'correct'     => $isCorrect,
            'explanation' => $isCorrect ? null : $question->explanation,
            'correct_answer' => $isCorrect ? null : $correct['text'],
        ]);
    }

    // POST /play/session/{session}/end
    // إنهاء الجلسة وحساب المكافآت
    public function endSession(Request $request, GameSession $session): JsonResponse
    {
        abort_if($session->child_id !== session('child_id'), 403);

        $data = $request->validate([
            'engagement_data' => 'nullable|array',
            'hints_used'      => 'nullable|integer',
        ]);

        $child     = $request->_child;
        $game      = $session->game;
        $total     = $session->correct_count + $session->wrong_count;
        $accuracy  = $total > 0 ? $session->correct_count / $total : 0;

        // حساب النجوم (0-3 نجوم)
        $stars = match (true) {
            $accuracy >= 0.9 => 3,
            $accuracy >= 0.7 => 2,
            $accuracy >= 0.5 => 1,
            default          => 0,
        };

        // حساب الـ XP
        $xp = (int) ($game->xp_reward * $accuracy
            + ($data['hints_used'] ?? 0) * -5); // خصم على الـ hints
        $xp = max(0, $xp);

        // إنهاء الجلسة
        $session->complete([
            'stars_earned'   => $stars,
            'xp_earned'      => $xp,
            'hints_used'     => $data['hints_used'] ?? 0,
            'engagement_data' => $data['engagement_data'] ?? [],
        ]);

        // Adaptive Engine يعالج النتائج
        $adaptResult = $this->engine->processSession($session);

        // فحص الإنجازات في الـ background
        CheckAchievementsJob::dispatch($child->id);

        return response()->json([
            'stars'      => $stars,
            'xp'         => $xp,
            'accuracy'   => round($accuracy * 100),
            'mastered'   => $adaptResult['mastered'] ?? false,
            'next_difficulty' => $adaptResult['new_difficulty'],
            'message'    => $adaptResult['message'] ?? $this->getEndMessage($stars, $child->name),
            'total_stars' => $child->fresh()->total_stars,
        ]);
    }

    // POST /play/session/{session}/track
    // Engagement tracking — يُرسل كل 30 ثانية من الـ frontend
    public function trackEngagement(Request $request, GameSession $session): JsonResponse
    {
        abort_if($session->child_id !== session('child_id'), 403);

        $data   = $request->validate([
            'avg_think_time'      => 'nullable|numeric',
            'frustration_signals' => 'nullable|integer',
            'pauses'              => 'nullable|integer',
            'hints_requested'     => 'nullable|integer',
        ]);

        $result = $this->tracker->track($session, $data);

        return response()->json($result);
    }

    protected function getEndMessage(int $stars, string $name): string
    {
        return match ($stars) {
            3 => "رائع يا {$name}! أداء مثالي! 🌟🌟🌟",
            2 => "أحسنت يا {$name}! تقدّم ممتاز! 🌟🌟",
            1 => "جيد يا {$name}! ستتحسن في المرة القادمة! 🌟",
            0 => "لا تستسلم يا {$name}! حاول مجدداً! 💪",
        };
    }
}
