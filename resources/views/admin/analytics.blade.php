@extends('layouts.app')
@section('title', 'التحليلات')
@section('page-title', 'تحليلات المنصة')

@section('topbar-actions')
    <form class="flex gap-2">
        <select name="days" onchange="this.form.submit()"
            class="text-sm py-1.5 px-3 rounded-xl border border-slate-200 bg-white font-medium" style="width:auto">
            @foreach ([7 => 'آخر 7 أيام', 14 => 'آخر 14 يوم', 30 => 'آخر 30 يوم', 90 => 'آخر 3 أشهر'] as $d => $l)
                <option value="{{ $d }}" {{ $period == $d ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
    </form>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            @foreach ([['👥', 'مستخدمون نشطون', $stats['active_children'], '#ede9fe', '#7c3aed'], ['🎮', 'جلسات اللعب', $stats['total_sessions'], '#dbeafe', '#1d4ed8'], ['⏱️', 'إجمالي الدقائق', number_format($stats['total_minutes']), '#dcfce7', '#15803d'], ['🎯', 'دقة متوسطة', $stats['avg_accuracy'] . '%', '#fef9c3', '#a16207'], ['🤖', 'محادثات AI', $stats['total_sessions'], '#fce7f3', '#be185d'], ['📊', 'تقارير أسبوعية', $aiStats['weekly_reports'], '#fff7ed', '#c2410c']] as [$icon, $label, $val, $bg, $color])
                <div class="rounded-2xl p-4 border" style="background:{{ $bg }};border-color:{{ $color }}22">
                    <div class="text-xl mb-1.5">{{ $icon }}</div>
                    <div class="text-xl font-black" style="color:{{ $color }}">{{ $val }}</div>
                    <div class="text-xs font-medium text-slate-500 mt-0.5">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        {{-- Sessions + Minutes chart --}}
        <div class="card p-5">
            <h3 class="font-black text-slate-700 mb-4">📈 النشاط اليومي — الجلسات والدقائق</h3>
            @php
                $labels = $dailySessions->pluck('label')->toJson();
                $sessions = $dailySessions->pluck('sessions')->toJson();
                $minutes = $dailySessions->pluck('minutes')->toJson();
            @endphp
            <div class="relative h-48">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        {{-- 2 col: subject dist + age groups --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Subject distribution --}}
            <div class="card p-5">
                <h3 class="font-black text-slate-700 mb-4">📚 توزيع الجلسات حسب المادة</h3>
                @php $totalS = $subjectDist->sum('sessions') ?: 1 @endphp
                <div class="space-y-4">
                    @foreach ($subjectDist as $s)
                        @php $pct = round($s['sessions']/$totalS*100) @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-sm text-slate-700">{{ $s['icon'] }}
                                    {{ $s['name'] }}</span>
                                <span class="text-sm font-black" style="color:{{ $s['color'] }}">{{ $s['sessions'] }}
                                    جلسة</span>
                            </div>
                            <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full"
                                    style="width:{{ $pct }}%;background:{{ $s['color'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Age group + Top children --}}
            <div class="card p-5">
                <h3 class="font-black text-slate-700 mb-4">👦 توزيع الفئات العمرية</h3>
                @php $totalAge = $ageGroups->sum() ?: 1 @endphp
                <div class="grid grid-cols-3 gap-3 mb-5">
                    @foreach (['6-8' => '🧒', '9-11' => '👦', '12-14' => '🧑'] as $ag => $emoji)
                        @php
                            $cnt = $ageGroups->get($ag, 0);
                            $pct = round(($cnt / $totalAge) * 100);
                        @endphp
                        <div class="text-center p-3 rounded-xl bg-slate-50">
                            <div class="text-2xl mb-1">{{ $emoji }}</div>
                            <div class="text-xl font-black text-slate-700">{{ $cnt }}</div>
                            <div class="text-xs text-slate-400">{{ $ag }} سنة</div>
                            <div class="text-xs font-bold text-indigo-500 mt-0.5">{{ $pct }}%</div>
                        </div>
                    @endforeach
                </div>

                <h4 class="font-black text-slate-600 text-sm mb-3">🏆 أفضل اللاعبين</h4>
                <div class="space-y-2">
                    @foreach ($topChildren->take(5) as $i => $child)
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-black w-5 text-center"
                                style="color:{{ ['#f59e0b', '#94a3b8', '#b45309', '#64748b', '#64748b'][$i] ?? '#94a3b8' }}">
                                {{ $i + 1 }}
                            </span>
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black text-white flex-shrink-0"
                                style="background:{{ $child->avatar_color }}">
                                {{ mb_substr($child->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-slate-700 flex-1 truncate">{{ $child->name }}</span>
                            <span class="text-xs text-yellow-500 font-black">{{ $child->total_stars }}⭐</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- AI Stats --}}
        <div class="card p-5">
            <h3 class="font-black text-slate-700 mb-4">🤖 إحصائيات الذكاء الاصطناعي</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([['أسئلة مولّدة بـ AI', $aiStats['ai_questions'], '#ede9fe', '#7c3aed', '🧠'], ['محادثات AI Tutor', $aiStats['tutor_chats'], '#dbeafe', '#1d4ed8', '💬'], ['Tokens مستخدمة', number_format($aiStats['tokens_used']), '#dcfce7', '#15803d', '⚡'], ['تقارير أسبوعية', $aiStats['weekly_reports'], '#fef9c3', '#a16207', '📋']] as [$label, $val, $bg, $color, $icon])
                    <div class="rounded-xl p-4 text-center" style="background:{{ $bg }}">
                        <div class="text-2xl mb-1">{{ $icon }}</div>
                        <div class="text-2xl font-black" style="color:{{ $color }}">{{ $val }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        new Chart(document.getElementById('activityChart'), {
            type: 'bar',
            data: {
                labels: @json($dailySessions->pluck('label')),
                datasets: [{
                        label: 'جلسات',
                        data: @json($dailySessions->pluck('sessions')),
                        backgroundColor: 'rgba(79,70,229,.7)',
                        borderRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        label: 'دقائق',
                        data: @json($dailySessions->pluck('minutes')),
                        type: 'line',
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,.1)',
                        fill: true,
                        tension: .4,
                        pointRadius: 3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'left'
                    },
                }
            }
        });
    </script>
@endpush
