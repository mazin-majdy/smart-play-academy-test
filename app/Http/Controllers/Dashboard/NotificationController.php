<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ParentNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->with('child')
            ->latest()
            ->paginate(20);

        // نعلّم كلهم مقروءين عند الفتح
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('dashboard.notifications', compact('notifications'));
    }

    public function markRead(ParentNotification $n)
    {
        abort_unless($n->user_id === auth()->id(), 403);
        $n->markRead();
        return back();
    }

    public function markAllRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return back()->with('success', 'تم تعليم كل الإشعارات كمقروءة');
    }

    public function destroy(ParentNotification $n)
    {
        abort_unless($n->user_id === auth()->id(), 403);
        $n->delete();
        return back();
    }
}
