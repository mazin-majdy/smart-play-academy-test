<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('children');
        return view('dashboard.settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);
        $user->update($data);
        return back()->with('success', 'تم تحديث بياناتك بنجاح');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'تم تغيير كلمة المرور');
    }
}
