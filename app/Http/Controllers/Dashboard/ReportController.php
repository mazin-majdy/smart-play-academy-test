<?php

// تقارير الأهل — تقدم الطفل والتقارير الأسبوعية
// ══════════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Child, ChildProgress, GameSession, WeeklyReport};
use App\Models\Subject;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // GET /dashboard/reports/{child}
    public function show(Request $request, Child $child)
    {
        // تأكد إن هاد طفل الأهل الحالي
        abort_unless(
            auth()->user()->children->contains($child->id),
            403
        );

        // تقدم الطفل في كل مادة
        $subjects = Subject::with(['topics' => function ($q) use ($child) {
            $q->where('age_group', $child->age_group);
        }])->get()->map(function ($subject) use ($child) {

            $topicIds = $subject->topics->pluck('id');
            $progress = ChildProgress::where('child_id', $child->id)
                ->whereIn('topic_id', $topicIds)
                ->with('topic')
                ->get();

            return [
                'subject'          => $subject,
                'progress'         => $progress,
                'avg_mastery'      => round($progress->avg('mastery_score') ?? 0),
                'mastered_count'   => $progress->where('mastery_score', '>=', 80)->count(),
                'total_topics'     => $topicIds->count(),
                'weakest_topic'    => $progress->sortBy('mastery_score')->first()?->topic?->name,
            ];
        });

        // آخر 10 جلسات
        $recentSessions = GameSession::where('child_id', $child->id)
            ->with(['game', 'topic'])
            ->where('status', 'completed')
            ->latest()
            ->take(10)
            ->get();

        // مخطط النشاط آخر 30 يوم
        $activityChart = GameSession::where('child_id', $child->id)
            ->where('status', 'completed')
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(started_at) as date, COUNT(*) as sessions, SUM(stars_earned) as stars')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('sessions', 'date')
            ->toArray();

        // آخر تقرير أسبوعي
        $lastReport = WeeklyReport::where('child_id', $child->id)
            ->latest('week_start')
            ->first();

        return view('dashboard.reports.show', compact(
            'child',
            'subjects',
            'recentSessions',
            'activityChart',
            'lastReport'
        ));
    }

    // GET /dashboard/reports/{child}/weekly
    public function weekly(Request $request, Child $child)
    {
        abort_unless(auth()->user()->children->contains($child->id), 403);

        $reports = WeeklyReport::where('child_id', $child->id)
            ->orderByDesc('week_start')
            ->paginate(10);

        return view('dashboard.reports.weekly', compact('child', 'reports'));
    }
}
