{{-- resources/views/auth/child-login.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أكاديمية اللعب الذكية — دخول اللاعب</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        :root {
            --sky: #0ea5e9;
            --sky2: #38bdf8;
            --sun: #fbbf24;
            --sun2: #fde68a;
            --grass: #22c55e;
            --grass2: #86efac;
            --purple: #8b5cf6;
            --purple2: #c4b5fd;
            --pink: #ec4899;
            --card: #fff;
            --ink: #1e1b4b;
        }

        html {
            font-size: 16px
        }

        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100svh;
            overflow: hidden;
            background: linear-gradient(160deg, #0c1445 0%, #1a237e 40%, #283593 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* ── Stars background ── */
        .stars {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0
        }

        .star {
            position: absolute;
            background: #fff;
            border-radius: 50%;
            animation: twinkle var(--d) ease-in-out infinite var(--delay);
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: .2;
                transform: scale(1)
            }

            50% {
                opacity: 1;
                transform: scale(1.4)
            }
        }

        /* ── Floating planets ── */
        .planet {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            animation: float var(--fd) ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg)
            }

            50% {
                transform: translateY(-20px) rotate(10deg)
            }
        }

        /* ── Rocket ── */
        .rocket {
            position: fixed;
            font-size: 3rem;
            pointer-events: none;
            animation: rocketFly 8s linear infinite;
            top: 20%;
            left: -80px;
        }

        @keyframes rocketFly {
            0% {
                left: -80px;
                top: 20%
            }

            100% {
                left: 110%;
                top: 10%
            }
        }

        /* ── Card ── */
        .card {
            position: relative;
            z-index: 10;
            width: min(420px, 92vw);
            background: rgba(255, 255, 255, .08);
            backdrop-filter: blur(20px);
            border: 1.5px solid rgba(255, 255, 255, .15);
            border-radius: 28px;
            padding: 2.5rem 2rem;
            box-shadow: 0 32px 80px rgba(0, 0, 0, .5), inset 0 1px 0 rgba(255, 255, 255, .2);
        }

        /* ── Logo/mascot ── */
        .mascot {
            text-align: center;
            margin-bottom: 1.5rem;
            animation: bounce .8s ease infinite alternate;
        }

        @keyframes bounce {
            from {
                transform: translateY(0)
            }

            to {
                transform: translateY(-8px)
            }
        }

        .mascot-emoji {
            font-size: 4.5rem;
            display: block;
            line-height: 1
        }

        .mascot-glow {
            width: 90px;
            height: 12px;
            background: radial-gradient(ellipse, rgba(251, 191, 36, .4), transparent);
            margin: 0.5rem auto 0;
            border-radius: 50%;
            animation: glowPulse 1.2s ease infinite alternate;
        }

        @keyframes glowPulse {
            from {
                opacity: .4;
                width: 70px
            }

            to {
                opacity: 1;
                width: 90px
            }
        }

        /* ── Title ── */
        .title {
            text-align: center;
            margin-bottom: .25rem;
            font-size: 1.7rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fde68a, #fbbf24, #fb923c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -.02em;
        }

        .subtitle {
            text-align: center;
            font-size: .9rem;
            color: rgba(255, 255, 255, .6);
            margin-bottom: 2rem;
        }

        /* ── Form ── */
        .field {
            margin-bottom: 1.1rem
        }

        .field label {
            display: block;
            font-size: .82rem;
            font-weight: 700;
            color: rgba(255, 255, 255, .75);
            margin-bottom: .45rem;
            letter-spacing: .03em;
        }

        .field input {
            width: 100%;
            padding: .85rem 1.1rem;
            background: rgba(255, 255, 255, .1);
            border: 1.5px solid rgba(255, 255, 255, .15);
            border-radius: 14px;
            font-family: 'Tajawal', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }

        .field input::placeholder {
            color: rgba(255, 255, 255, .35);
            font-weight: 400
        }

        .field input:focus {
            border-color: var(--sun);
            background: rgba(255, 255, 255, .15);
            box-shadow: 0 0 0 3px rgba(251, 191, 36, .2);
        }

        /* ── Error ── */
        .error-box {
            background: rgba(239, 68, 68, .15);
            border: 1px solid rgba(239, 68, 68, .3);
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .85rem;
            color: #fca5a5;
            margin-bottom: 1rem;
            text-align: center;
        }

        /* ── Submit btn ── */
        .btn-play {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border: none;
            border-radius: 16px;
            font-family: 'Tajawal', sans-serif;
            font-size: 1.1rem;
            font-weight: 900;
            color: #1e1b4b;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(251, 191, 36, .4);
            transition: transform .15s, box-shadow .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            letter-spacing: -.01em;
        }

        .btn-play:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(251, 191, 36, .5)
        }

        .btn-play:active {
            transform: translateY(0)
        }

        /* ── Parent link ── */
        .parent-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .82rem;
            color: rgba(255, 255, 255, .5);
        }

        .parent-link a {
            color: var(--purple2);
            text-decoration: none;
            font-weight: 700;
            transition: color .2s;
        }

        .parent-link a:hover {
            color: #fff
        }

        /* ── Characters row ── */
        .chars {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .char-btn {
            font-size: 2rem;
            background: rgba(255, 255, 255, .08);
            border: 2px solid transparent;
            border-radius: 16px;
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s;
            animation: charFloat calc(2s + var(--i)*0.3s) ease-in-out infinite;
        }

        .char-btn:hover {
            border-color: var(--sun);
            background: rgba(251, 191, 36, .15);
            transform: scale(1.15)
        }

        .char-btn.selected {
            border-color: var(--sun);
            background: rgba(251, 191, 36, .2);
            transform: scale(1.1)
        }

        @keyframes charFloat {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-5px)
            }
        }
    </style>
</head>

<body>

    <!-- Stars -->
    <div class="stars" id="stars"></div>

    <!-- Planets -->
    <div class="planet"
        style="width:60px;height:60px;background:radial-gradient(circle at 35% 35%,#8b5cf6,#4c1d95);top:10%;right:8%;--fd:6s;opacity:.6">
    </div>
    <div class="planet"
        style="width:35px;height:35px;background:radial-gradient(circle at 35% 35%,#f97316,#7c2d12);bottom:15%;left:6%;--fd:8s;opacity:.5">
    </div>
    <div class="planet"
        style="width:20px;height:20px;background:radial-gradient(circle at 35% 35%,#06b6d4,#164e63);top:40%;left:5%;--fd:5s;opacity:.4">
    </div>

    <!-- Rocket -->
    <div class="rocket">🚀</div>

    <div class="card">
        <!-- Mascot -->
        <div class="mascot">
            <span class="mascot-emoji" id="mascot-emoji">🦁</span>
            <div class="mascot-glow"></div>
        </div>

        <!-- Characters -->
        <div class="chars">
            <button class="char-btn selected" style="--i:0" data-emoji="🦁" onclick="selectChar(this,'🦁')">🦁</button>
            <button class="char-btn" style="--i:1" data-emoji="🐼" onclick="selectChar(this,'🐼')">🐼</button>
            <button class="char-btn" style="--i:2" data-emoji="🦊" onclick="selectChar(this,'🦊')">🦊</button>
            <button class="char-btn" style="--i:3" data-emoji="🐬" onclick="selectChar(this,'🐬')">🐬</button>
            <button class="char-btn" style="--i:4" data-emoji="🦋" onclick="selectChar(this,'🦋')">🦋</button>
        </div>

        <h1 class="title">أهلاً باللاعب! 🎮</h1>
        <p class="subtitle">سجّل دخولك وابدأ المغامرة</p>

        @if ($errors->any())
            <div class="error-box">❌ {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('child.login') }}" method="POST">
            @csrf
            <div class="field">
                <label>اسم اللاعب</label>
                <input type="text" name="username" placeholder="اكتب اسمك هنا..." value="{{ old('username') }}"
                    required autocomplete="off">
            </div>
            <div class="field">
                <label>الرمز السري</label>
                <input type="password" name="password" placeholder="••••••" required>
            </div>
            <button type="submit" class="btn-play">
                <span>🎮</span>
                <span>ابدأ اللعب!</span>
            </button>
        </form>

        <div class="parent-link">
            أنت أهل أو معلم؟
            <a href="{{ route('login') }}">ادخل من هنا ←</a>
        </div>
    </div>

    <script>
        // Generate stars
        const starsEl = document.getElementById('stars');
        for (let i = 0; i < 120; i++) {
            const s = document.createElement('div');
            s.className = 'star';
            const size = Math.random() * 3 + 1;
            s.style.cssText = `
    width:${size}px;height:${size}px;
    top:${Math.random()*100}%;left:${Math.random()*100}%;
    --d:${2+Math.random()*3}s;--delay:-${Math.random()*3}s;
  `;
            starsEl.appendChild(s);
        }

        function selectChar(btn, emoji) {
            document.querySelectorAll('.char-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            document.getElementById('mascot-emoji').textContent = emoji;
        }
    </script>
</body>

</html>
