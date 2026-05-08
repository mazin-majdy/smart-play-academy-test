<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أكاديمية اللعب الذكية — إنشاء حساب</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Tajawal', sans-serif
        }

        input,
        select {
            width: 100%;
            padding: .7rem .9rem;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Tajawal', sans-serif;
            font-size: .9rem;
            color: #1e293b;
            outline: none;
            transition: border-color .2s, box-shadow .2s
        }

        input:focus,
        select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .12)
        }

        label {
            display: block;
            font-size: .82rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: .4rem
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">

    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-0 rounded-2xl overflow-hidden shadow-2xl">

        {{-- Left: Visual --}}
        <div class="relative flex flex-col items-center justify-center p-10 text-white overflow-hidden"
            style="background:linear-gradient(145deg,#0f172a,#1e1b4b,#312e81)">
            <div class="absolute w-72 h-72 rounded-full opacity-10" style="background:#6366f1;top:-80px;right:-80px">
            </div>
            <div class="absolute w-44 h-44 rounded-full opacity-10" style="background:#a3e635;bottom:-40px;left:-40px">
            </div>
            <div class="relative text-center">
                <div class="text-6xl mb-4">🎓</div>
                <h2 class="text-3xl font-black mb-3" style="letter-spacing:-.03em">انضم إلينا<br><span
                        style="color:#a3e635">مجاناً</span></h2>
                <p class="text-sm opacity-60 leading-relaxed mb-6">منصة تعليمية تحوّل التعلم إلى مغامرة<br>تتكيّف مع
                    مستوى طفلك تلقائياً</p>
                <div class="grid grid-cols-2 gap-3 text-center">
                    @foreach ([['🤖', 'AI ذكي'], ['🎮', '50+ لعبة'], ['📊', 'تقارير دورية'], ['🔒', 'آمن 100%']] as [$icon, $label])
                        <div class="rounded-xl p-3"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                            <div class="text-xl mb-1">{{ $icon }}</div>
                            <div class="text-xs font-bold opacity-80">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Form --}}
        <div class="bg-white p-8 flex flex-col justify-center">
            <h1 class="text-2xl font-black text-slate-800 mb-1" style="letter-spacing:-.03em">إنشاء حساب جديد</h1>
            <p class="text-sm text-slate-400 mb-6">سجّل كأهل أو معلم وابدأ رحلة التعلم</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label>الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="محمد أحمد" required>
                </div>

                {{-- Email --}}
                <div>
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com"
                        required>
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label>كلمة المرور</label>
                        <input type="password" name="password" placeholder="8 أحرف على الأقل" required>
                    </div>
                    <div>
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••" required>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-sm font-bold text-slate-600 mb-3">إضافة أول طفل (اختياري)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label>اسم الطفل</label>
                            <input type="text" name="child_name" value="{{ old('child_name') }}" placeholder="أحمد">
                        </div>
                        <div>
                            <label>الفئة العمرية</label>
                            <select name="child_age_group">
                                <option value="">اختر...</option>
                                <option value="6-8" {{ old('child_age_group') === '6-8' ? 'selected' : '' }}>6 – 8 سنوات
                                </option>
                                <option value="9-11" {{ old('child_age_group') === '9-11' ? 'selected' : '' }}>9 – 11 سنة
                                </option>
                                <option value="12-14"{{ old('child_age_group') === '12-14' ? 'selected' : '' }}>12 – 14 سنة
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl font-black text-white text-sm transition"
                    style="background:linear-gradient(135deg,#4f46e5,#6366f1);box-shadow:0 6px 20px rgba(79,70,229,.3)">
                    إنشاء الحساب ←
                </button>

                <p class="text-center text-sm text-slate-400">
                    لديك حساب؟
                    <a href="{{ route('login') }}" class="font-bold" style="color:#4f46e5">سجّل دخولك</a>
                </p>
            </form>
        </div>
    </div>
</body>

</html>
