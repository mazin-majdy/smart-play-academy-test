{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أكاديمية اللعب الذكية — دخول الأهل</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        :root {
            --indigo: #4f46e5;
            --indigo2: #6366f1;
            --lime: #a3e635;
            --lime2: #d9f99d;
            --ink: #0f172a;
            --ink2: #1e293b;
            --muted: #64748b;
            --light: #f8fafc;
        }

        html,
        body {
            height: 100%
        }

        body {
            font-family: 'Tajawal', sans-serif;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            background: var(--light);
        }

        /* ── LEFT PANEL — visual ── */
        .visual {
            background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .visual-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
        }

        .orb1 {
            width: 300px;
            height: 300px;
            background: rgba(79, 70, 229, .4);
            top: -80px;
            right: -80px
        }

        .orb2 {
            width: 200px;
            height: 200px;
            background: rgba(163, 230, 53, .2);
            bottom: -50px;
            left: -50px
        }

        .orb3 {
            width: 150px;
            height: 150px;
            background: rgba(236, 72, 153, .2);
            bottom: 30%;
            right: -30px
        }

        .visual-inner {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff
        }

        .visual-logo {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: block;
            animation: spinSlow 8s linear infinite
        }

        @keyframes spinSlow {
            to {
                transform: rotate(360deg)
            }
        }

        .visual-title {
            font-size: 2.4rem;
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 1rem;
            letter-spacing: -.03em;
        }

        .visual-title span {
            color: var(--lime)
        }

        .visual-desc {
            font-size: 1rem;
            color: rgba(255, 255, 255, .6);
            line-height: 1.8;
            max-width: 320px;
        }

        .visual-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2.5rem;
            text-align: center;
        }

        .v-stat {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 16px;
            padding: 1rem;
        }

        .v-stat-num {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--lime);
            font-family: 'DM Mono', monospace
        }

        .v-stat-label {
            font-size: .75rem;
            color: rgba(255, 255, 255, .5);
            margin-top: .25rem
        }

        /* ── RIGHT PANEL — form ── */
        .form-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 3rem;
        }

        .form-wrap {
            width: 100%;
            max-width: 400px
        }

        .form-header {
            margin-bottom: 2.5rem
        }

        .form-header h1 {
            font-size: 1.9rem;
            font-weight: 900;
            color: var(--ink);
            letter-spacing: -.03em;
            margin-bottom: .4rem;
        }

        .form-header p {
            font-size: .9rem;
            color: var(--muted)
        }

        .field {
            margin-bottom: 1.2rem
        }

        .field label {
            display: block;
            font-size: .82rem;
            font-weight: 700;
            color: var(--ink2);
            margin-bottom: .45rem;
            letter-spacing: .02em;
        }

        .field input {
            width: 100%;
            padding: .85rem 1.1rem;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-size: .97rem;
            font-weight: 500;
            color: var(--ink);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .field input:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .12)
        }

        .field input::placeholder {
            color: #94a3b8
        }

        .btn-submit {
            width: 100%;
            padding: .95rem;
            background: var(--indigo);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: .01em;
            transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 6px 20px rgba(79, 70, 229, .3);
        }

        .btn-submit:hover {
            background: var(--indigo2);
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(79, 70, 229, .4)
        }

        .divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.5rem 0;
            color: #cbd5e1;
            font-size: .8rem
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0
        }

        .btn-child {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #fde68a, #fbbf24);
            color: #78350f;
            border: none;
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-size: .97rem;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s, box-shadow .2s;
            box-shadow: 0 4px 16px rgba(251, 191, 36, .3);
        }

        .btn-child:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(251, 191, 36, .4)
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .87rem;
            color: var(--muted);
        }

        .register-link a {
            color: var(--indigo);
            font-weight: 700;
            text-decoration: none
        }

        .register-link a:hover {
            text-decoration: underline
        }

        .error-box {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .87rem;
            color: #dc2626;
            margin-bottom: 1.25rem;
        }

        @media(max-width:760px) {
            body {
                grid-template-columns: 1fr
            }

            .visual {
                display: none
            }

            .form-side {
                padding: 2rem 1.5rem
            }
        }
    </style>
</head>

<body>

    <!-- Visual Panel -->
    <div class="visual">
        <div class="visual-orb orb1"></div>
        <div class="visual-orb orb2"></div>
        <div class="visual-orb orb3"></div>
        <div class="visual-inner">
            <span class="visual-logo">🎓</span>
            <h2 class="visual-title">أكاديمية<br><span>اللعب الذكية</span></h2>
            <p class="visual-desc">منصة تعليمية تحوّل التعلم إلى مغامرة ممتعة تتكيّف مع مستوى طفلك تلقائياً</p>
            <div class="visual-stats">
                <div class="v-stat">
                    <div class="v-stat-num">4</div>
                    <div class="v-stat-label">مواد دراسية</div>
                </div>
                <div class="v-stat">
                    <div class="v-stat-num">50+</div>
                    <div class="v-stat-label">لعبة تفاعلية</div>
                </div>
                <div class="v-stat">
                    <div class="v-stat-num">AI</div>
                    <div class="v-stat-label">تكيّف ذكي</div>
                </div>
                <div class="v-stat">
                    <div class="v-stat-num">6-14</div>
                    <div class="v-stat-label">سنة عمر</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="form-side">
        <div class="form-wrap">
            <div class="form-header">
                <h1>مرحباً بك 👋</h1>
                <p>سجّل دخولك لمتابعة أطفالك وتقدمهم</p>
            </div>

            @if ($errors->any())
                <div class="error-box">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="field">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com"
                        required>
                </div>
                <div class="field">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-submit">دخول ←</button>
            </form>

            <div class="divider">أو</div>

            <a href="{{ route('child.login') }}" class="btn-child">
                🎮 <span>دخول الأطفال</span>
            </a>

            <div class="register-link">
                ليس لديك حساب؟ <a href="{{ route('register') }}">سجّل الآن مجاناً</a>
            </div>
        </div>
    </div>
</body>

</html>
