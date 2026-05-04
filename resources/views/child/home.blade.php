@extends('layouts.child')
@section('title', 'الرئيسية')

@section('content')

    {{-- ترحيب + اقتراح ذكي --}}
    <div class="bg-gradient-to-l from-violet-600 to-purple-700 rounded-3xl p-6 text-white mb-6">
        <h1 class="text-2xl font-bold mb-1">أهلاً {{ $child->name }}! 👋</h1>
        <p class="text-violet-200 text-sm mb-4">استمر في رحلة التعلم الممتعة</p>

        @if ($suggestedTopic)
            <div class="bg-white/20 backdrop-blur rounded-2xl p-4 flex items-center gap-4">
                <div class="text-3xl">🎯</div>
                <div class="flex-1">
                    <p class="text-xs text-violet-200 mb-0.5">نقترح عليك</p>
                    <p class="font-bold">{{ $suggestedTopic->name }}</p>
                </div>
                <a href="{{ route('child.subject', $suggestedTopic->subject_id) }}"
                    class="bg-white text-violet-700 font-bold text-sm px-4 py-2 rounded-full hover:bg-violet-50 transition">
                    العب الآن
                </a>
            </div>
        @endif
    </div>

    {{-- إحصائيات سريعة --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-violet-600">{{ $stats['current_level'] }}</div>
            <div class="text-xs text-gray-500 mt-1">المستوى</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-yellow-500">{{ $stats['total_stars'] }}</div>
            <div class="text-xs text-gray-500 mt-1">النجوم ⭐</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-orange-500">{{ $stats['streak_days'] }}</div>
            <div class="text-xs text-gray-500 mt-1">أيام متتالية 🔥</div>
        </div>
    </div>

    {{-- المواد --}}
    <h2 class="font-bold text-gray-700 text-lg mb-4">اختر مادتك 📚</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach ($subjects as $subject)
            <a href="{{ route('child.subject', $subject) }}"
                class="bg-white rounded-3xl p-5 text-center shadow-sm hover:shadow-md transition hover:-translate-y-1 group">
                <div class="text-4xl mb-3 group-hover:scale-110 transition">{{ $subject->icon }}</div>
                <h3 class="font-bold text-gray-800 text-sm mb-2">{{ $subject->name }}</h3>

                {{-- Progress bar --}}
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all"
                        style="width:{{ $subject->progress_percent }}%;background:{{ $subject->color }}">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">
                    {{ $subject->topics_mastered }}/{{ $subject->topics_total }} موضوع
                </p>
            </a>
        @endforeach
    </div>

    {{-- آخر إنجاز --}}
    @if ($lastAchievement)
        <div
            class="bg-gradient-to-l from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl p-4 flex items-center gap-4">
            <div class="text-3xl">{{ $lastAchievement->icon }}</div>
            <div>
                <p class="text-xs text-gray-500">آخر إنجاز حصلت عليه</p>
                <p class="font-bold text-gray-800">{{ $lastAchievement->name }}</p>
            </div>
            <a href="{{ route('child.achievements') }}"
                class="mr-auto text-xs text-yellow-600 font-medium hover:text-yellow-700">
                كل الإنجازات ←
            </a>
        </div>
    @endif
@endsection
