{{-- Layout مخصص للطفل — مبهج وبسيط وبعيد عن التعقيد               --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'العب وتعلم') — أكاديمية اللعب الذكية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            400: '#7C3AED',
                            500: '#6D28D9',
                            600: '#5B21B6'
                        },
                        fun: {
                            yellow: '#FCD34D',
                            green: '#34D399',
                            blue: '#60A5FA',
                            pink: '#F472B6'
                        }
                    },
                    fontFamily: {
                        arabic: ['Tajawal', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }

        .star-shine {
            animation: shine 2s ease-in-out infinite;
        }

        @keyframes shine {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
            }

            50% {
                transform: scale(1.15) rotate(10deg);
            }
        }

        .bounce-in {
            animation: bounceIn .5s cubic-bezier(.68, -.55, .265, 1.55) both;
        }

        @keyframes bounceIn {
            from {
                opacity: 0;
                transform: scale(.3);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .progress-bar {
            transition: width .8s cubic-bezier(.4, 0, .2, 1);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-violet-50 to-blue-50 min-h-screen">

    {{-- NAV --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b-4 border-violet-400">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            {{-- اسم الطفل + المستوى --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl font-bold text-white"
                    style="background:{{ $currentChild->avatar_color }}">
                    {{ mb_substr($currentChild->name, 0, 1) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">{{ $currentChild->name }}</p>
                    <p class="text-xs text-violet-600">المستوى {{ $currentChild->current_level }} 🎮</p>
                </div>
            </div>

            {{-- النقاط والنجوم --}}
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 bg-yellow-50 px-3 py-1 rounded-full">
                    <span class="text-yellow-500 text-lg star-shine">⭐</span>
                    <span
                        class="font-bold text-yellow-700 text-sm">{{ number_format($currentChild->total_stars) }}</span>
                </div>
                <div class="flex items-center gap-1 bg-orange-50 px-3 py-1 rounded-full">
                    <span class="text-lg">🔥</span>
                    <span class="font-bold text-orange-600 text-sm">{{ $currentChild->streak_days }}</span>
                </div>
            </div>

            {{-- الوقت اليومي --}}
            <div class="hidden md:flex items-center gap-2">
                @php
                    $todayMin = $currentChild->getTodayPlayMinutes();
                    $limitMin = $currentChild->daily_limit_minutes;
                    $pct = min(100, round(($todayMin / $limitMin) * 100));
                    $barColor = $pct >= 80 ? 'bg-red-400' : 'bg-green-400';
                @endphp
                <div class="text-xs text-gray-500">{{ $todayMin }}/{{ $limitMin }} دقيقة</div>
                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full {{ $barColor }} progress-bar rounded-full"
                        style="width:{{ $pct }}%"></div>
                </div>
            </div>

            {{-- زر الخروج --}}
            <form action="{{ route('child.logout') }}" method="POST">
                @csrf
                <button class="text-xs text-gray-400 hover:text-red-400 transition">خروج</button>
            </form>
        </div>
    </nav>

    {{-- FLASH --}}
    @if (session('limit_reached'))
        <div class="bg-orange-100 border-b-2 border-orange-300 text-orange-800 text-center py-3 text-sm font-medium">
            ⏱️ وصلت لحد الوقت اليومي! استرح قليلاً وعد غداً أقوى 💪
        </div>
    @endif

    <main class="max-w-5xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <footer class="text-center py-4 text-xs text-gray-400">
        أكاديمية اللعب الذكية 🎓
    </footer>

    @stack('scripts')
</body>

</html>
