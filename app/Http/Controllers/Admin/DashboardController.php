<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Child, Game, GameSession, Subject };
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_children' => Child::count(),
            'total_games'    => Game::count(),
            'sessions_today' => GameSession::whereDate('created_at', today())->count(),
            'active_now'     => GameSession::where('status', 'in_progress')
                ->where('started_at', '>=', now()->subMinutes(30))
                ->count(),
        ];

        // مخطط الجلسات آخر 7 أيام
        $sessionsChart = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i)->toDateString();
            return [
                'date'  => $date,
                'label' => now()->subDays($i)->format('D'),
                'count' => GameSession::whereDate('created_at', $date)->count(),
            ];
        });

        // أكثر الألعاب شعبية
        $popularGames = Game::withCount('sessions')
            ->orderByDesc('sessions_count')
            ->take(5)
            ->with('topic.subject')
            ->get();

        // أحدث المستخدمين
        $recentUsers = User::with('children')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'sessionsChart', 'popularGames', 'recentUsers'));
    }
}
