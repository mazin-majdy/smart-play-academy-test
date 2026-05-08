<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) return redirect()->route('dashboard.home');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'])
                ->withInput();
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard.home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
