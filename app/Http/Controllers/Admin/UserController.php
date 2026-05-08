<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->withCount('children')->latest();

        if ($request->filled('search')) {
            $query->where(
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
            );
        }
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load(['roles', 'children.progress']);
        $recentSessions = \App\Models\GameSession::whereIn(
            'child_id',
            $user->children->pluck('id')
        )
            ->with(['game', 'child'])
            ->latest()->take(10)->get();

        return view('admin.users.show', compact('user', 'recentSessions'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|exists:roles,name']);
        $user->syncRoles([$request->role]);
        return back()->with('success', "تم تغيير دور {$user->name} إلى {$request->role}");
    }

    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);
        return back()->with('success', "تم تعليق حساب {$user->name}");
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "تم تفعيل حساب {$user->name}");
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin')) {
            return back()->with('error', 'لا يمكن حذف حساب أدمن');
        }
        $user->delete();
        return back()->with('success', 'تم حذف الحساب');
    }
}
