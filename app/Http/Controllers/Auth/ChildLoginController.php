<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// تسجيل دخول الطفل — واجهة مختلفة وبسيطة

class ChildLoginController extends Controller
{
    // GET /child/login
    public function showForm()
    {
        return view('auth.child-login');
    }

    // POST /child/login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $child = Child::where('username', $request->username)
            ->where('is_active', true)
            ->first();

        if (!$child || !Hash::check($request->password, $child->password)) {
            return back()->withErrors(['username' => 'اسم المستخدم أو كلمة المرور غير صحيحة']);
        }

        // نحفظ الطفل في الـ session
        session(['child_id' => $child->id, 'child_name' => $child->name]);

        return redirect()->route('child.home');
    }

    // POST /child/logout
    public function logout()
    {
        session()->forget(['child_id', 'child_name']);
        return redirect()->route('child.login');
    }
}
