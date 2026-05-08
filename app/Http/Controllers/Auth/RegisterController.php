<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


// تسجيل أهل جديد مع طفل واحد على الأقل

class RegisterController extends Controller
{
    // GET /register
    public function showForm()
    {
        return view('auth.register');
    }

    // POST /register
    public function register(Request $request)
    {
        $data = $request->validate([
            // بيانات الأهل
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',

            // بيانات الطفل الأول (اختياري — يمكن إضافته بعدين)
            'child_name'    => 'nullable|string|max:100',
            'child_age_group' => 'nullable|in:6-8,9-11,12-14',
        ]);

        DB::transaction(function () use ($data) {
            // إنشاء حساب الأهل
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // تعيين دور "parent"
            $user->assignRole('parent');

            // إنشاء الطفل الأول إذا أُدخلت بياناته
            if (!empty($data['child_name'])) {
                $username = $this->generateUsername($data['child_name']);
                $child = Child::create([
                    'name'       => $data['child_name'],
                    'username'   => $username,
                    'password'   => Hash::make('1234'), // كلمة مرور بسيطة للطفل
                    'age_group'  => $data['child_age_group'] ?? '9-11',
                ]);

                // ربط الطفل بالأهل
                $user->children()->attach($child->id, [
                    'relation'   => 'parent',
                    'is_primary' => true,
                ]);
            }

            auth()->login($user);
        });

        return redirect()->route('dashboard.home');
    }

    protected function generateUsername(string $name): string
    {
        $base = strtolower(str_replace(' ', '_', $name));
        $slug = $base;
        $i    = 1;
        while (Child::where('username', $slug)->exists()) {
            $slug = $base . '_' . $i++;
        }
        return $slug;
    }
}
