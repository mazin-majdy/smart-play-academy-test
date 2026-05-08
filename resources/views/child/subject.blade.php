{{-- resources/views/child/subject.blade.php --}}
@extends('layouts.child')
@section('title', $subject->name)

@section('content')
    <div class="space-y-5">

        {{-- Subject Header --}}
        <div class="rounded-3xl p-6 text-white relative overflow-hidden"
            style="background:linear-gradient(135deg,{{ $subject->color }},{{ $subject->color }}cc)">
            <div class="absolute"
                style="width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.06);top:-60px;right:-40px">
            </div>
            <div class="relative flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-4xl flex-shrink-0"
                    style="background:rgba(255,255,255,.15)">
                    {{ $subject->icon }}
                </div>
                <div>
                    <h1 class="text-2xl font-black">{{ $subject->name }}</h1>
                    <p class="text-sm opacity-70">{{ $topics->count() }} موضوع في انتظارك</p>
                </div>
                <a href="{{ route('child.home') }}" class="mr-auto text-sm px-3 py-2 rounded-xl font-bold"
                    style="background:rgba(255,255,255,.15)">← رجوع</a>
            </div>
        </div>

        {{-- Topics --}}
        <h2 class="font-black text-lg" style="color:#1e293b">اختر الموضوع 🎯</h2>

        <div class="space-y-3">
            @foreach ($topics as $topic)
                @php
                    $mastery = $topic->mastery;
                    $isLocked = $topic->is_locked ?? false;
                    $isMastered = $mastery >= 80;
                    $inProgress = $mastery > 0 && $mastery < 80;
                @endphp
                <div class="relative rounded-2xl overflow-hidden transition-all {{ $isLocked ? 'opacity-50' : 'hover:scale-[1.01]' }}"
                    style="background:#fff;border:2px solid {{ $isMastered ? '#bbf7d0' : ($inProgress ? '#ddd6fe' : '#f1f5f9') }}">

                    {{-- Mastered banner --}}
                    @if ($isMastered)
                        <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl"
                            style="background:linear-gradient(90deg,#22c55e,#16a34a)"></div>
                    @endif

                    <div class="p-4 flex items-center gap-4">
                        {{-- Difficulty dots --}}
                        <div class="flex-shrink-0 flex flex-col items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="w-2 h-2 rounded-full"
                                    style="background:{{ $i <= $topic->difficulty_level ? $subject->color : '#e2e8f0' }}">
                                </div>
                            @endfor
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-black" style="color:#1e293b">{{ $topic->name }}</h3>
                                @if ($isMastered)
                                    <span class="text-xs px-2 py-0.5 rounded-full font-bold"
                                        style="background:#dcfce7;color:#15803d">✓ مُتقن</span>
                                @elseif($inProgress)
                                    <span class="text-xs px-2 py-0.5 rounded-full font-bold"
                                        style="background:#ede9fe;color:#7c3aed">جارٍ...</span>
                                @endif
                            </div>

                            {{-- Progress bar --}}
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background:#f1f5f9">
                                    <div class="h-full rounded-full transition-all"
                                        style="width:{{ $mastery }}%;background:{{ $isMastered ? '#22c55e' : $subject->color }}">
                                    </div>
                                </div>
                                <span class="text-xs font-bold" style="color:#94a3b8">{{ $mastery }}%</span>
                            </div>

                            {{-- Games count --}}
                            <p class="text-xs mt-1" style="color:#94a3b8">
                                {{ $topic->games->count() }} ألعاب متاحة
                            </p>
                        </div>

                        {{-- Action --}}
                        @if ($isLocked)
                            <div class="text-2xl">🔒</div>
                        @else
                            <a href="{{ route('child.subject', $topic->subject_id) }}?topic={{ $topic->id }}"
                                class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center text-xl text-white font-bold transition"
                                style="background:{{ $subject->color }};box-shadow:0 4px 12px {{ $subject->color }}55">
                                {{ $isMastered ? '🔁' : '▶' }}
                            </a>
                        @endif
                    </div>

                    {{-- Games list (collapsed by default) --}}
                    @if ($topic->games->count() > 0 && !$isLocked)
                        <div class="border-t px-4 py-3 flex gap-2 flex-wrap" style="border-color:#f1f5f9">
                            @foreach ($topic->games as $game)
                                <a href="{{ route('child.game', $game) }}"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-bold transition hover:scale-105"
                                    style="background:{{ $subject->color }}15;color:{{ $subject->color }}">
                                    @switch($game->game_type)
                                        @case('drag_drop')
                                            🖐
                                        @break

                                        @case('visual_match')
                                            👁
                                        @break

                                        @case('math_puzzle')
                                            🔢
                                        @break

                                        @case('logic_chain')
                                            🧩
                                        @break

                                        @case('quiz')
                                            ❓
                                        @break

                                        @case('story_interactive')
                                            📖
                                        @break

                                        @case('timed_challenge')
                                            ⏱
                                        @break

                                        @default
                                            🎮
                                    @endswitch
                                    {{ $game->title }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection

