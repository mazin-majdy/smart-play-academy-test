<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Child, Game, GameSession, Subject, Topic, Question};
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $period = request()->integer('days', 30);
        $from   = now()->subDays($period);

        // ── Platform-wide stats ──────────────────────────────────
        $stats = [
            'total_users'      => User::count(),
            'total_children'   => Child::count(),
            'total_sessions'   => GameSession::where('created_at', '>=', $from)->count(),
            'total_minutes'    => (int)(GameSession::where('created_at', '>=', $from)
                ->where('status', 'completed')
                ->sum('duration_seconds') / 60),
            'avg_accuracy'     => round(
                GameSession::where('created_at', '>=', $from)
                    ->where('status', 'completed')
                    ->selectRaw('AVG(correct_count/(correct_count+wrong_count+0.001)*100) as a')
                    ->value('a') ?? 0,
                1
            ),
            'active_children'  => Child::whereHas('gameSessions', fn($q) =>
            $q->where('created_at', '>=', $from))->count(),
        ];

        // ── Daily sessions last 30 days ──────────────────────────
        $dailySessions = collect(range($period - 1, 0))->map(function ($i) {
            $date = now()->subDays($i)->toDateString();
            return [
                'date'     => $date,
                'label'    => now()->subDays($i)->format('d/m'),
                'sessions' => GameSession::whereDate('created_at', $date)->count(),
                'minutes'  => (int)(GameSession::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('duration_seconds') / 60),
            ];
        });

        // ── Popular games ────────────────────────────────────────
        $popularGames = Game::withCount(['sessions' => fn($q) =>
        $q->where('created_at', '>=', $from)])
            ->orderByDesc('sessions_count')
            ->take(8)
            ->with('topic.subject')
            ->get();

        // ── Subject distribution ─────────────────────────────────
        $subjectDist = Subject::with(['topics.games' => function ($q) use ($from) {
            $q->withCount(['sessions' => fn($sq) => $sq->where('created_at', '>=', $from)]);
        }])->get()->map(fn($s) => [
            'name'     => $s->name,
            'icon'     => $s->icon,
            'color'    => $s->color,
            'sessions' => $s->topics->flatMap->games->sum('sessions_count'),
        ])->sortByDesc('sessions')->values();

        // ── Age group breakdown ──────────────────────────────────
        $ageGroups = Child::selectRaw('age_group, COUNT(*) as count')
            ->groupBy('age_group')
            ->pluck('count', 'age_group');

        // ── Top performing children ──────────────────────────────
        $topChildren = Child::orderByDesc('total_stars')
            ->take(10)
            ->get(['id', 'name', 'total_stars', 'current_level', 'streak_days', 'avatar_color', 'age_group']);

        // ── AI usage ────────────────────────────────────────────
        $aiStats = [
            'ai_questions'  => Question::where('ai_generated', true)->count(),
            'tutor_chats'   => \App\Models\AiTutorChat::where('created_at', '>=', $from)->count(),
            'tokens_used'   => \App\Models\AiTutorChat::where('created_at', '>=', $from)->sum('tokens_used'),
            'weekly_reports' => \App\Models\WeeklyReport::where('created_at', '>=', $from)->count(),
        ];

        return view('admin.analytics', compact(
            'stats',
            'dailySessions',
            'popularGames',
            'subjectDist',
            'ageGroups',
            'topChildren',
            'aiStats',
            'period'
        ));
    }
}
