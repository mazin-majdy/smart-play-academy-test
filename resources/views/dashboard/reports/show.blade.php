@extends('layouts.app')
@section('title', 'تقرير ' . $child->name)
@section('page-title', 'تقرير تقدم ' . $child->name)

@section('topbar-actions')
    <a href="{{ route('dashboard.reports.weekly', $child) }}" class="btn-ghost text-xs">📅 التقارير الأسبوعية</a>
@endsection

@section('content')
    <div class="space-y-5">

        {{-- Child header card --}}
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-black text-white"
                    style="background:{{ $child->avatar_color }}">
                    {{ mb_substr($child->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800">{{ $child->name }}</h2>
                    <p class="text-sm text-slate-400">{{ $child->age_group }} سنة · المستوى {{ $child->current_level }} · 🔥
                        {{ $child->streak_days }} يوم</p>
                </div>
                <div class="mr-auto flex gap-3 text-center">
                    <div class="px-4 py-3 rounded-xl bg-yellow-50 border border-yellow-100">
                        <div class="text-xl font-black text-yellow-600">{{ $child->total_stars }}</div>
                        <div class="text-xs text-slate-400">نجمة ⭐</div>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-indigo-50 border border-indigo-100">
                        <div class="text-xl font-black text-indigo-600">{{ $child->total_xp }}</div>
                        <div class="text-xs text-slate-400">XP</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Subject progress --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($subjects as $item)
                <div class="card p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                            style="background:{{ $item['subject']->color }}22">
                            {{ $item['subject']->icon }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-black text-slate-700">{{ $item['subject']->name }}</h3>
                            <p class="text-xs text-slate-400">{{ $item['mastered_count'] }}/{{ $item['total_topics'] }}
                                مواضيع مُتقنة</p>
                        </div>
                        <div class="text-lg font-black" style="color:{{ $item['subject']->color }}">
                            {{ $item['avg_mastery'] }}%</div>
                    </div>

                    {{-- Progress --}}
                    <div class="h-2 rounded-full bg-slate-100 mb-3 overflow-hidden">
                        <div class="h-full rounded-full"
                            style="width:{{ $item['avg_mastery'] }}%;background:{{ $item['subject']->color }}"></div>
                    </div>

                    {{-- Topics list --}}
                    <div class="space-y-1.5">
                        @foreach ($item['progress']->take(4) as $p)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-600 truncate">{{ $p->topic?->name }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full"
                                            style="width:{{ $p->mastery_score }}%;background:{{ $item['subject']->color }}">
                                        </div>
                                    </div>
                                    <span class="text-slate-400 w-8 text-left">{{ $p->mastery_score }}%</span>
                                </div>
                            </div>
                        @endforeach
                        @if ($item['weakest_topic'])
                            <p class="text-xs mt-2 text-orange-500 font-medium">⚠ يحتاج مراجعة:
                                {{ $item['weakest_topic'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Activity chart --}}
        <div class="card p-5">
            <h3 class="font-black text-slate-700 mb-4">📊 النشاط — آخر 30 يوم</h3>
            <div class="flex items-end gap-1 h-24">
                @php $maxSessions = max(array_values($activityChart) ?: [1]); @endphp
                @foreach ($activityChart as $date => $count)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full rounded-sm transition-all"
                            style="height:{{ $maxSessions > 0 ? round(($count / $maxSessions) * 80) : 0 }}px;background:linear-gradient(to top,#4f46e5,#818cf8);min-height:2px">
                        </div>
                        <span class="text-xs text-slate-300 rotate-45 origin-right whitespace-nowrap"
                            style="font-size:.6rem">
                            {{ \Carbon\Carbon::parse($date)->format('d/m') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent sessions --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-black text-slate-700">آخر الجلسات</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentSessions as $s)
                    <div class="flex items-center gap-4 px-5 py-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm bg-slate-100">🎮</div>
                        <div class="flex-1">
                            <p class="font-bold text-sm text-slate-700">{{ $s->game?->title }}</p>
                            <p class="text-xs text-slate-400">{{ $s->topic?->name }} ·
                                {{ $s->started_at->format('d/m H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-yellow-500 font-bold">{{ $s->stars_earned }}⭐</span>
                            <span
                                class="{{ $s->accuracy >= 70 ? 'text-green-600' : 'text-red-400' }} font-bold">{{ $s->accuracy }}%</span>
                            <span class="text-slate-400 text-xs">{{ gmdate('i:s', $s->duration_seconds) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">لا توجد جلسات بعد</div>
                @endforelse
            </div>
        </div>

        {{-- Last weekly report --}}
        @if ($lastReport)
            <div class="card p-5" style="border:1.5px solid #dbeafe;background:#eff6ff">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-black text-blue-700">📋 آخر تقرير أسبوعي</h3>
                    <span class="text-xs text-blue-400">{{ $lastReport->week_start->format('d/m') }} –
                        {{ $lastReport->week_end->format('d/m') }}</span>
                </div>
                @if ($lastReport->ai_summary)
                    <p class="text-sm text-blue-800 leading-relaxed mb-3">{{ $lastReport->ai_summary }}</p>
                @endif
                @if ($lastReport->gaps_detected)
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-wide">نقاط تحتاج اهتماماً:</p>
                        @foreach ($lastReport->gaps_detected as $gap)
                            <div class="flex items-center justify-between text-xs text-blue-700">
                                <span>📌 {{ $gap['topic'] ?? '' }}</span>
                                <span class="font-bold">{{ $gap['mastery'] ?? 0 }}% إتقان</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

    </div>
@endsection
