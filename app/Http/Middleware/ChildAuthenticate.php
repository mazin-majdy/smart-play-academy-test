<?php
// Middleware خاص بتحقق من جلسة الطفل
// ══════════════════════════════════════════════════════════════════
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChildAuthenticate
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!session()->has('child_id')) {
            return redirect()->route('child.login')
                ->with('error', 'سجّل دخولك أولاً');
        }

        $child = \App\Models\Child::find(session('child_id'));

        if (!$child || !$child->is_active) {
            session()->forget(['child_id', 'child_name']);
            return redirect()->route('child.login')
                ->with('error', 'انتهت جلستك، سجّل دخولك مجدداً');
        }

        // فحص الحد اليومي
        if (
            $child->hasReachedDailyLimit() &&
            !$request->routeIs('child.home') &&
            !$request->routeIs('child.achievements')
        ) {
            return redirect()->route('child.home')
                ->with('limit_reached', true);
        }

        // نشارك الطفل مع كل الـ views
        view()->share('currentChild', $child);
        $request->merge(['_child' => $child]);

        return $next($request);
    }
}
