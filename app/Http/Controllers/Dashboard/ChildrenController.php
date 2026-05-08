<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChildrenController extends Controller
{
    public function index()
    {
        $children = auth()->user()->children()->get();
        $notifications = auth()->user()->unreadNotifications()->latest()->take(10)->get();
        return view('dashboard.home', compact('children', 'notifications'));
    }

    public function create()
    {
        return view('dashboard.children.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'age_group'     => 'required|in:6-8,9-11,12-14',
            'learning_style' => 'required|in:visual,auditory,mixed',
            'daily_limit_minutes' => 'integer|min:15|max:180',
        ]);

        // نولّد username تلقائياً
        $base     = strtolower(str_replace(' ', '_', $data['name']));
        $username = $base;
        $i = 1;
        while (Child::where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }

        $child = Child::create([
            ...$data,
            'username'     => $username,
            'password'     => Hash::make('1234'),
            'avatar_color' => collect(['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'])->random(),
        ]);

        auth()->user()->children()->attach($child->id, [
            'relation'   => 'parent',
            'is_primary' => true,
        ]);

        return redirect()->route('dashboard.home')
            ->with('success', "تمت إضافة {$child->name}! اسم المستخدم: {$username} وكلمة المرور: 1234");
    }

    public function update(Request $request, Child $child)
    {
        abort_unless(auth()->user()->children->contains($child->id), 403);

        $data = $request->validate([
            'daily_limit_minutes' => 'required|integer|min:15|max:180',
        ]);

        $child->update($data);
        return back()->with('success', 'تم تحديث إعدادات الطفل');
    }
}
