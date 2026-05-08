@extends('layouts.app')
@section('title', 'التقارير الأسبوعية')
@section('page-title', 'التقارير الأسبوعية — ' . $child->name)

@section('content')
    <div class="space-y-4">

        @forelse($reports as $r)
            <div class="card p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-black text-slate-700">
                            {{ $r->week_start->format('d') }} – {{ $r->week_end->format('d M Y') }}
                        </h3>
                        @if ($r->generated_at)
                            <p class="text-xs text-slate-400">تم التوليد {{ $r->generated_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    <div class="flex gap-3 text-center">
                        <div class="px-3 py-2 rounded-xl bg-slate-50">
                            <div class="font-black text-indigo-600">{{ $r->total_sessions }}</div>
                            <div class="text-xs text-slate-400">جلسة</div>
                        </div>
                        <div class="px-3 py-2 rounded-xl bg-slate-50">
                            <div class="font-black text-green-600">{{ $r->total_minutes }}</div>
                            <div class="text-xs text-slate-400">دقيقة</div>
                        </div>
                        <div class="px-3 py-2 rounded-xl bg-slate-50">
                            <div class="font-black text-yellow-500">{{ $r->stars_earned }}</div>
                            <div class="text-xs text-slate-400">⭐ نجمة</div>
                        </div>
                    </div>
                </div>

                @if ($r->ai_summary)
                    <p
                        class="text-sm text-slate-600 leading-relaxed mb-3 p-3 rounded-xl bg-indigo-50 border border-indigo-100">
                        🤖 {{ $r->ai_summary }}
                    </p>
                @endif

                @if ($r->real_activities)
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">أنشطة مقترحة:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($r->real_activities as $act)
                                <span
                                    class="text-xs px-3 py-1.5 rounded-full bg-green-50 text-green-700 font-medium border border-green-100">
                                    💡 {{ $act['activity'] ?? '' }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="card p-12 text-center">
                <div class="text-4xl mb-3">📊</div>
                <p class="text-slate-500 font-medium">لا توجد تقارير أسبوعية بعد</p>
                <p class="text-xs text-slate-400 mt-1">تُولَّد التقارير تلقائياً كل أحد</p>
            </div>
        @endforelse

        {{ $reports->links() }}
    </div>
@endsection
