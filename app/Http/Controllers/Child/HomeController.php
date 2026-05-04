<?php

// الصفحة الرئيسية للطفل — يشوف مواده وتقدمه
// ══════════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\ChildProgress;
use App\Models\Achievement;
use App\Services\AdaptiveEngine;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(private AdaptiveEngine $engine) {}

    public function index(Request $request)
    {
        $child = $request->_child;

        // المواد مع نسبة تقدم الطفل فيها
        $subjects = Subject::active()
            ->with(['topics' => function ($q) use ($child) {
                $q->where('age_group', $child->age_group)
                    ->where('is_active', true);
            }])
            ->get()
            ->map(function ($subject) use ($child) {
                $topicIds = $subject->topics->pluck('id');

                // تقدم الطفل في هذه المادة
                $progress = ChildProgress::where('child_id', $child->id)
                    ->whereIn('topic_id', $topicIds)
                    ->get();

                $subject->progress_percent = $topicIds->count() > 0
                    ? round($progress->avg('mastery_score') ?? 0)
                    : 0;

                $subject->topics_mastered = $progress->where('mastery_score', '>=', 80)->count();
                $subject->topics_total    = $topicIds->count();

                return $subject;
            });

        // الموضوع المقترح من Adaptive Engine
        $suggestedTopic = $this->engine->suggestNextTopic($child);

        // إحصائيات سريعة
        $stats = [
            'today_minutes'  => $child->getTodayPlayMinutes(),
            'limit_minutes'  => $child->daily_limit_minutes,
            'total_stars'    => $child->total_stars,
            'streak_days'    => $child->streak_days,
            'current_level'  => $child->current_level,
        ];

        // آخر إنجاز
        $lastAchievement = $child->achievements()
            ->orderByPivot('earned_at', 'desc')
            ->first();

        return view('child.home', compact(
            'child',
            'subjects',
            'suggestedTopic',
            'stats',
            'lastAchievement'
        ));
    }

    public function achievements(Request $request)
    {
        $child = $request->_child;

        $earned = $child->achievements()->withPivot('earned_at')->get();
        $all    = Achievement::where('is_active', true)->get();

        return view('child.achievements', compact('child', 'earned', 'all'));
    }
}
