<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $children = auth()->user()
            ->children() // تأكد إن العلاقة هنا معرفة صح
            ->with(['progress', 'gameSessions' => fn($q) =>
            $q->whereDate('created_at', today())])
            ->get();

        $notifications = auth()->user()
            ->notifications()
            ->with('child')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.home', compact('children', 'notifications'));
    }
}
