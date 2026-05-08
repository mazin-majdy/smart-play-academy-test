{{-- لوحة تحكم الأهل — تقارير وإدارة الأطفال                        --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'الرئيسية')
@section('page-title', 'لوحة التحكم')

@section('topbar-actions')
    <a href="{{ route('dashboard.children.create') }}" class="btn-primary">+ إضافة طفل</a>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- ══ Stats ══ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                // ✅ استخدام المتغير $children القادم من الـ Controller مباشرة
                // هذا يحل مشكلة الغموض (Ambiguous) ويقلل الاستعلامات
                $totalChildren = $children->count();
                $totalStars = $children->sum('total_stars');

                // ✅ حساب جلسات اليوم من الكوليكشن المحملة مسبقاً (Eager Loaded)
                // بدلاً من عمل Query جديد يسبب مشكلة SQL
                $todaySessions = $children
                    ->flatMap(function ($child) {
                        return $child->gameSessions;
                    })
                    ->where('created_at', '>=', today()->startOfDay())
                    ->count();

                $unreadNotifs = auth()->user()->unreadNotifications()->count();
            @endphp

            @foreach ([['👦', 'إجمالي الأطفال', $totalChildren, 'bg-indigo-50', 'text-indigo-600', 'border-indigo-100'], ['⭐', 'مجموع النجوم', number_format($totalStars), 'bg-yellow-50', 'text-yellow-600', 'border-yellow-100'], ['🎮', 'جلسات اليوم', $todaySessions, 'bg-green-50', 'text-green-600', 'border-green-100'], ['🔔', 'إشعارات جديدة', $unreadNotifs, 'bg-red-50', 'text-red-600', 'border-red-100']] as [$icon, $label, $val, $bg, $color, $border])
                <div class="rounded-2xl p-5 {{ $bg }} border {{ $border }}">
                    <div class="text-2xl mb-2">{{ $icon }}</div>
                    <div class="text-2xl font-black {{ $color }}">{{ $val }}</div>
                    <div class="text-xs font-medium text-slate-500 mt-1">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        {{-- ══ Children Cards ══ --}}
        @forelse($children as $child)
            <div class="card p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl font-black text-white flex-shrink-0"
                            style="background:{{ $child->avatar_color }}">
                            {{ mb_substr($child->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-black text-slate-800 text-lg">{{ $child->name }}</h3>
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <span>{{ $child->age_group }} سنة</span>
                                <span>·</span>
                                <span>المستوى {{ $child->current_level }}</span>
                                @if ($child->streak_days > 0)
                                    <span>· 🔥 {{ $child->streak_days }} يوم</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.reports.show', $child) }}" class="btn-ghost text-xs">📊 التقرير</a>
                        <a href="{{ route('dashboard.children.edit', $child) }}" class="btn-ghost text-xs">✏️ تعديل</a>
                    </div>
                </div>

                {{-- Stats row --}}
                <div class="grid grid-cols-4 gap-3 mb-4">
                    @php
                        $todayMin = $child->getTodayPlayMinutes();
                        $limitMin = $child->daily_limit_minutes;
                        $pct = $limitMin > 0 ? min(100, round(($todayMin / $limitMin) * 100)) : 0;
                        $masteredCount = $child->progress()->where('mastery_score', '>=', 80)->count();
                    @endphp
                    <div class="text-center p-3 rounded-xl bg-slate-50">
                        <div class="text-lg font-black text-indigo-600">{{ $todayMin }}<span
                                class="text-xs font-medium text-slate-400">/{{ $limitMin }}</span></div>
                        <div class="text-xs text-slate-500 mt-0.5">دقيقة اليوم</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-slate-50">
                        <div class="text-lg font-black text-yellow-500">{{ $child->total_stars }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">⭐ النجوم</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-slate-50">
                        <div class="text-lg font-black text-green-600">{{ $masteredCount }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">موضوع مُتقن</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-slate-50">
                        <div class="text-lg font-black text-purple-600">
                            {{-- ✅ استخدام العلاقة المحملة مسبقاً بدلاً من Query جديد --}}
                            {{ $child->gameSessions->where('created_at', '>=', today()->startOfDay())->count() }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">جلسات اليوم</div>
                    </div>
                </div>

                {{-- Daily time progress --}}
                <div class="mb-3">
                    <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                        <span>وقت الشاشة اليوم</span>
                        <span class="{{ $pct >= 80 ? 'text-red-500 font-bold' : '' }}">{{ $pct }}%</span>
                    </div>
                    <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full transition-all"
                            style="width:{{ $pct }}%;background:{{ $pct >= 80 ? 'linear-gradient(90deg,#ef4444,#dc2626)' : 'linear-gradient(90deg,#22c55e,#16a34a)' }}">
                        </div>
                    </div>
                </div>

                {{-- Update daily limit --}}
                <form action="{{ route('dashboard.children.update', $child) }}" method="POST"
                    class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <label class="text-xs text-slate-500 flex-shrink-0 m-0">الحد اليومي:</label>
                    <input type="number" name="daily_limit_minutes" value="{{ $child->daily_limit_minutes }}"
                        min="15" max="180" step="15" class="w-20 text-center py-1.5 text-sm"
                        style="padding:.35rem .5rem">
                    <button class="btn-ghost text-xs py-1.5 px-3">حفظ</button>
                </form>
            </div>

        @empty
            <div class="card p-12 text-center">
                <div class="text-5xl mb-4">👶</div>
                <h3 class="font-black text-slate-700 text-xl mb-2">أضف طفلك الأول</h3>
                <p class="text-sm text-slate-400 mb-5">ابدأ رحلة التعلم الممتعة مع أكاديمية اللعب الذكية</p>
                <a href="{{ route('dashboard.children.create') }}" class="btn-primary">+ إضافة طفل</a>
            </div>
        @endforelse

        {{-- ══ Recent Notifications ══ --}}
        @if ($notifications->count())
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h3 class="font-black text-slate-700">آخر الإشعارات</h3>
                    <a href="{{ route('dashboard.notifications') }}" class="text-xs text-indigo-500 font-bold">عرض الكل
                        ←</a>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach ($notifications->take(5) as $n)
                        <div class="flex items-start gap-3 px-5 py-3.5 {{ $n->is_read ? '' : 'bg-indigo-50/30' }}">
                            <div class="text-xl flex-shrink-0 mt-0.5">
                                @switch($n->type)
                                    @case('frustration_detected')
                                        ⚠️
                                    @break

                                    @case('achievement')
                                        🏆
                                    @break

                                    @case('limit_reached')
                                        ⏱️
                                    @break

                                    @case('weekly_report')
                                        📊
                                    @break

                                    @case('streak_broken')
                                        💔
                                    @break

                                    @default
                                        📣
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 leading-snug">{{ $n->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $n->body }}</p>
                            </div>
                            <span class="text-xs text-slate-400 flex-shrink-0">{{ $n->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection
