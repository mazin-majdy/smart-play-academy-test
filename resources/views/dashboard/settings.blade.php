@extends('layouts.app')
@section('title', 'الإعدادات')
@section('page-title', 'إعدادات الحساب')

@section('content')
    <div class="max-w-2xl space-y-5">

        {{-- Profile --}}
        <div class="card p-6">
            <h2 class="font-black text-slate-700 mb-5 flex items-center gap-2">👤 معلوماتي الشخصية</h2>
            <form action="{{ route('dashboard.settings.update') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label>الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label>البريد الإلكتروني</label>
                    <input type="email" value="{{ $user->email }}" disabled class="opacity-60">
                    <p class="text-xs text-slate-400 mt-1">لا يمكن تغيير البريد الإلكتروني</p>
                </div>
                <div>
                    <label>رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        placeholder="+970 5x xxx xxxx">
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-black text-white"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-sm text-slate-700">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $user->getRoleNames()->first() }}</p>
                    </div>
                </div>
                <button class="btn-primary">حفظ التغييرات</button>
            </form>
        </div>

        {{-- Password --}}
        <div class="card p-6">
            <h2 class="font-black text-slate-700 mb-5 flex items-center gap-2">🔒 تغيير كلمة المرور</h2>
            <form action="{{ route('dashboard.settings.password') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label>كلمة المرور الحالية</label>
                    <input type="password" name="current_password" required>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label>كلمة المرور الجديدة</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                    <div>
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" required>
                    </div>
                </div>
                <button class="btn-primary">تغيير كلمة المرور</button>
            </form>
        </div>

        {{-- Children summary --}}
        <div class="card p-6">
            <h2 class="font-black text-slate-700 mb-4 flex items-center gap-2">👦 أطفالي ({{ $user->children->count() }})
            </h2>
            <div class="space-y-2">
                @foreach ($user->children as $child)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black text-white"
                                style="background:{{ $child->avatar_color }}">
                                {{ mb_substr($child->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-slate-700">{{ $child->name }}</p>
                                <p class="text-xs text-slate-400">@{{ $child - > username }} · {{ $child->age_group }} سنة</p>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.children.edit', $child) }}"
                            class="btn-ghost text-xs py-1.5 px-3">تعديل</a>
                    </div>
                @endforeach
                <a href="{{ route('dashboard.children.create') }}" class="btn-ghost w-full justify-center mt-2 text-sm">+
                    إضافة طفل جديد</a>
            </div>
        </div>

    </div>
@endsection
