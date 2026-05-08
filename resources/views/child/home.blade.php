{{-- resources/views/child/home.blade.php --}}
@extends('layouts.child')
@section('title', 'عالم الألعاب')

@section('content')
    <div class="space-y-6">

        {{-- ══ HERO WELCOME BANNER ══ --}}
        <div class="relative overflow-hidden rounded-3xl"
            style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 50%,#a855f7 100%);min-height:180px">

            {{-- Decorative circles --}}
            <div class="absolute"
                style="width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06);top:-60px;right:-60px">
            </div>
            <div class="absolute"
                style="width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.08);bottom:-40px;right:30%">
            </div>

            <div class="relative p-6 flex items-center justify-between">
                <div class="text-white">
                    <p class="text-sm font-medium opacity-70 mb-1">أهلاً مجدداً 👋</p>
                    <h1 class="text-3xl font-black leading-tight mb-2" style="letter-spacing:-.03em">
                        {{ $child->name }}!
                    </h1>

                    {{-- Streak badge --}}
                    @if ($child->streak_days > 0)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold"
                            style="background:rgba(255,255,255,.15);backdrop-filter:blur(8px)">
                            🔥 {{ $child->streak_days }} يوم متتالي!
                        </div>
                    @endif
                </div>

                {{-- Level ring --}}
                <div class="relative flex-shrink-0">
                    <svg width="90" height="90" viewBox="0 0 90 90">
                        <circle cx="45" cy="45" r="38" fill="none" stroke="rgba(255,255,255,.15)"
                            stroke-width="7" />
                        <circle cx="45" cy="45" r="38" fill="none" stroke="#a3e635" stroke-width="7"
                            stroke-linecap="round" stroke-dasharray="{{ min(239, (($child->total_xp % 200) / 200) * 239) }} 239"
                            transform="rotate(-90 45 45)" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center">
                        <span class="text-xs opacity-70">مستوى</span>
                        <span class="text-2xl font-black">{{ $child->current_level }}</span>
                    </div>
                </div>
            </div>

            {{-- Stars + XP bar --}}
            <div class="relative px-6 pb-5">
                <div class="flex items-center justify-between text-xs text-white mb-2 opacity-70">
                    <span>{{ $child->total_xp % 200 }} / 200 XP للمستوى القادم</span>
                    <span>⭐ {{ number_format($child->total_stars) }} نجمة</span>
                </div>
                <div class="h-2 rounded-full" style="background:rgba(255,255,255,.15)">
                    <div class="h-2 rounded-full transition-all"
                        style="width:{{ min(100, (($child->total_xp % 200) / 200) * 100) }}%;background:linear-gradient(90deg,#a3e635,#65a30d)">
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SUGGESTED TOPIC ══ --}}
        @if ($suggestedTopic)
            <div class="relative overflow-hidden rounded-2xl p-5"
                style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:2px solid #fbbf24">
                <div class="absolute" style="font-size:5rem;opacity:.15;top:-10px;left:-5px;line-height:1">🎯</div>
                <div class="relative flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl flex-shrink-0"
                        style="background:rgba(251,191,36,.3)">
                        {{ $suggestedTopic->subject->icon ?? '🎯' }}
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold uppercase tracking-widest" style="color:#92400e;opacity:.7">يُقترح لك
                            الآن</p>
                        <h3 class="text-lg font-black" style="color:#78350f">{{ $suggestedTopic->name }}</h3>
                        <p class="text-sm" style="color:#92400e">{{ $suggestedTopic->subject->name }}</p>
                    </div>
                    <a href="{{ route('child.subject', $suggestedTopic->subject_id) }}"
                        class="flex-shrink-0 px-5 py-3 rounded-xl text-sm font-black transition"
                        style="background:#f59e0b;color:#fff;box-shadow:0 4px 12px rgba(245,158,11,.4)">
                        العب! ←
                    </a>
                </div>
            </div>
        @endif

        {{-- ══ QUICK STATS ══ --}}
        <div class="grid grid-cols-3 gap-3">
            @php
                $todayMin = $stats['today_minutes'];
                $limitMin = $stats['limit_minutes'];
                $pct = min(100, $limitMin > 0 ? round(($todayMin / $limitMin) * 100) : 0);
            @endphp

            <div class="rounded-2xl p-4 text-center" style="background:#f0fdf4;border:1.5px solid #bbf7d0">
                <div class="text-2xl font-black" style="color:#15803d">{{ $todayMin }}</div>
                <div class="text-xs font-medium mt-1" style="color:#166534">دقيقة اليوم</div>
                <div class="mt-2 h-1.5 rounded-full overflow-hidden" style="background:#dcfce7">
                    <div class="h-full rounded-full"
                        style="width:{{ $pct }}%;background:#22c55e;transition:width .8s"></div>
                </div>
                <div class="text-xs mt-1" style="color:#86efac">من {{ $limitMin }} دقيقة</div>
            </div>

            <div class="rounded-2xl p-4 text-center" style="background:#fefce8;border:1.5px solid #fef08a">
                <div class="text-2xl font-black" style="color:#ca8a04">{{ number_format($stats['total_stars']) }}</div>
                <div class="text-xs font-medium mt-1" style="color:#a16207">نجمة</div>
                <div class="text-2xl mt-1">⭐</div>
            </div>

            <div class="rounded-2xl p-4 text-center" style="background:#fff7ed;border:1.5px solid #fed7aa">
                <div class="text-2xl font-black" style="color:#ea580c">{{ $stats['streak_days'] }}</div>
                <div class="text-xs font-medium mt-1" style="color:#c2410c">يوم متتالي</div>
                <div class="text-2xl mt-1">🔥</div>
            </div>
        </div>

        {{-- ══ SUBJECTS GRID ══ --}}
        <div>
            <h2 class="font-black text-xl mb-4" style="color:#1e293b;letter-spacing:-.02em">
                اختر مادتك 📚
            </h2>
            <div class="grid grid-cols-2 gap-4">
                @foreach ($subjects as $subject)
                    @php
                        $gradients = [
                            '#4f46e5,#7c3aed' => 'from-indigo',
                            '#059669,#10b981' => 'from-green',
                            '#dc2626,#ef4444' => 'from-red',
                            '#7c3aed,#a855f7' => 'from-purple',
                        ];
                        $colors = [
                            '#4f46e5' => [
                                'bg' => 'rgba(79,70,229,.08)',
                                'border' => 'rgba(79,70,229,.2)',
                                'grad' => '#4f46e5,#7c3aed',
                                'text' => '#3730a3',
                            ],
                            '#059669' => [
                                'bg' => 'rgba(5,150,105,.08)',
                                'border' => 'rgba(5,150,105,.2)',
                                'grad' => '#059669,#10b981',
                                'text' => '#065f46',
                            ],
                            '#dc2626' => [
                                'bg' => 'rgba(220,38,38,.08)',
                                'border' => 'rgba(220,38,38,.2)',
                                'grad' => '#dc2626,#ef4444',
                                'text' => '#991b1b',
                            ],
                            '#7c3aed' => [
                                'bg' => 'rgba(124,58,237,.08)',
                                'border' => 'rgba(124,58,237,.2)',
                                'grad' => '#7c3aed,#a855f7',
                                'text' => '#5b21b6',
                            ],
                        ];
                        $c = $colors[$subject->color] ?? $colors['#4f46e5'];
                    @endphp
                    <a href="{{ route('child.subject', $subject) }}"
                        class="group relative overflow-hidden rounded-3xl p-5 block transition-all hover:scale-105"
                        style="background:{{ $c['bg'] }};border:2px solid {{ $c['border'] }}">

                        {{-- BG decoration --}}
                        <div class="absolute -bottom-4 -left-4 text-7xl opacity-10 group-hover:opacity-20 transition-opacity"
                            style="line-height:1">
                            {{ $subject->icon }}
                        </div>

                        <div class="relative">
                            {{-- Icon --}}
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl mb-3"
                                style="background:linear-gradient(135deg,{{ $c['grad'] }});box-shadow:0 6px 20px rgba(0,0,0,.15)">
                                {{ $subject->icon }}
                            </div>

                            <h3 class="font-black text-base mb-1" style="color:{{ $c['text'] }}">{{ $subject->name }}
                            </h3>

                            {{-- Progress --}}
                            <div class="flex items-center justify-between text-xs mb-2"
                                style="color:{{ $c['text'] }};opacity:.7">
                                <span>{{ $subject->topics_mastered }}/{{ $subject->topics_total }} موضوع</span>
                                <span>{{ $subject->progress_percent }}%</span>
                            </div>
                            <div class="h-2 rounded-full overflow-hidden" style="background:rgba(0,0,0,.08)">
                                <div class="h-full rounded-full transition-all"
                                    style="width:{{ $subject->progress_percent }}%;background:linear-gradient(90deg,{{ $c['grad'] }})">
                                </div>
                            </div>

                            {{-- Badge if mastered --}}
                            @if ($subject->progress_percent >= 80)
                                <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold"
                                    style="background:linear-gradient(135deg,{{ $c['grad'] }});color:#fff">
                                    🏆 متقن!
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ══ ACHIEVEMENTS TEASER ══ --}}
        @if ($lastAchievement)
            <div class="flex items-center gap-4 p-4 rounded-2xl"
                style="background:linear-gradient(135deg,#fef9c3,#fef3c7);border:2px solid #fde047">
                <div class="text-4xl flex-shrink-0">{{ $lastAchievement->icon }}</div>
                <div class="flex-1">
                    <p class="text-xs font-bold uppercase tracking-widest" style="color:#713f12;opacity:.7">آخر إنجاز</p>
                    <p class="font-black" style="color:#78350f">{{ $lastAchievement->name }}</p>
                    <p class="text-xs" style="color:#92400e">{{ $lastAchievement->description }}</p>
                </div>
                <a href="{{ route('child.achievements') }}" class="text-xs font-bold px-3 py-2 rounded-xl"
                    style="background:rgba(120,53,15,.12);color:#78350f">
                    الكل →
                </a>
            </div>
        @endif

    </div>
@endsection
