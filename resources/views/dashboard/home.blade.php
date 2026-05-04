{{-- لوحة تحكم الأهل — تقارير وإدارة الأطفال                        --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'لوحة التحكم')

@section('content')

    {{-- إحصائيات سريعة --}}
    @foreach ($children as $child)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold text-white"
                        style="background:{{ $child->avatar_color }}">
                        {{ mb_substr($child->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $child->name }}</h3>
                        <p class="text-xs text-gray-500">
                            الفئة العمرية: {{ $child->age_group }} سنة ·
                            المستوى {{ $child->current_level }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-orange-500 font-bold">🔥 {{ $child->streak_days }} يوم</span>
                    <a href="{{ route('dashboard.reports.show', $child) }}"
                        class="text-xs bg-violet-100 text-violet-700 px-3 py-1.5 rounded-full hover:bg-violet-200 transition">
                        التقرير الكامل
                    </a>
                </div>
            </div>

            {{-- إحصائيات اليوم --}}
            <div class="grid grid-cols-4 gap-3 mb-5">
                @php
                    $todayMin = $child->getTodayPlayMinutes();
                    $limitMin = $child->daily_limit_minutes;
                    $pct = min(100, round(($todayMin / $limitMin) * 100));
                @endphp
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-lg font-bold text-violet-600">{{ $todayMin }}</div>
                    <div class="text-xs text-gray-500">دقيقة اليوم</div>
                    <div class="text-xs text-gray-400">من {{ $limitMin }}</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-lg font-bold text-yellow-500">{{ $child->total_stars }}</div>
                    <div class="text-xs text-gray-500">النجوم ⭐</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-lg font-bold text-green-600">
                        {{ $child->progress()->where('mastery_score', '>=', 80)->count() }}
                    </div>
                    <div class="text-xs text-gray-500">موضوع مُتقن</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-lg font-bold text-blue-600">
                        {{ $child->gameSessions()->whereDate('created_at', today())->count() }}
                    </div>
                    <div class="text-xs text-gray-500">جلسات اليوم</div>
                </div>
            </div>

            {{-- وقت الشاشة --}}
            <div class="mb-4">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>وقت الشاشة اليوم</span>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all" style="width:{{ $pct }}%"
                        :class="{{ $pct >= 80 ? '\"bg-red-400\"' : '\"bg-green-400\"' }}">
                    </div>
                </div>
            </div>

            {{-- إعداد الحد اليومي --}}
            <div class="flex items-center gap-3">
                <label class="text-xs text-gray-500">الحد اليومي:</label>
                <form action="{{ route('dashboard.children.update', $child) }}" method="POST" class="flex gap-2">
                    @csrf @method('PUT')
                    <input type="number" name="daily_limit_minutes" value="{{ $child->daily_limit_minutes }}"
                        min="15" max="180" step="15"
                        class="w-20 px-2 py-1 text-sm border rounded-lg focus:ring-2 focus:ring-violet-400 outline-none">
                    <button class="text-xs bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-lg transition">تحديث</button>
                </form>
            </div>
        </div>
    @endforeach

    {{-- إضافة طفل جديد --}}
    <a href="{{ route('dashboard.children.create') }}"
        class="block w-full border-2 border-dashed border-gray-300 hover:border-violet-400 rounded-2xl p-6 text-center text-gray-400 hover:text-violet-500 transition">
        <span class="text-2xl">+</span>
        <p class="text-sm mt-1">إضافة طفل جديد</p>
    </a>

    {{-- الإشعارات الأخيرة --}}
    @if ($notifications->count())
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-700">آخر الإشعارات</h3>
                <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full">
                    {{ $notifications->where('is_read', false)->count() }} جديد
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($notifications->take(5) as $notif)
                    <div class="px-5 py-3 flex items-start gap-3 {{ $notif->is_read ? 'opacity-60' : '' }}">
                        <div class="text-lg flex-shrink-0">
                            @switch($notif->type)
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

                                @default
                                    📣
                            @endswitch
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $notif->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $notif->body }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
