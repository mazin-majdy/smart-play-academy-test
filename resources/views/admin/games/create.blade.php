@extends('layouts.app')
@section('title', 'لعبة جديدة')
@section('page-title', 'إضافة لعبة جديدة')

@section('content')
    <div class="max-w-2xl" x-data="gameForm()">

        <div class="card p-6">
            <form action="{{ route('admin.games.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Title --}}
                <div>
                    <label>عنوان اللعبة <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="مثال: تحدي الجمع السريع">
                </div>

                {{-- Subject + Topic --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>المادة الدراسية <span class="text-red-400">*</span></label>
                        <select name="subject_id" x-model="selectedSubject" @change="filterTopics()" required>
                            <option value="">اختر المادة...</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->icon }} {{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>الموضوع <span class="text-red-400">*</span></label>
                        <select name="topic_id" required>
                            <option value="">اختر الموضوع...</option>
                            @foreach ($subjects as $subject)
                                @foreach ($subject->topics as $topic)
                                    <option value="{{ $topic->id }}" data-subject="{{ $subject->id }}"
                                        data-age="{{ $topic->age_group }}" class="topic-option">
                                        {{ $topic->name }} ({{ $topic->age_group }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Game type --}}
                <div>
                    <label>نوع اللعبة <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-1">
                        @php
                            $gameTypes = [
                                'drag_drop' => ['🖐', 'سحب وإفلات', '6-8'],
                                'visual_match' => ['👁', 'مطابقة بصرية', '6-8'],
                                'story_interactive' => ['📖', 'قصة تفاعلية', '6-8'],
                                'sound_guess' => ['🔊', 'تخمين صوتي', '6-8'],
                                'math_puzzle' => ['🔢', 'لغز رياضي', '9-11'],
                                'logic_chain' => ['🧩', 'سلسلة منطقية', '9-11'],
                                'word_challenge' => ['📝', 'تحدي كلمات', '9-11'],
                                'virtual_lab' => ['🧪', 'مختبر افتراضي', '9-11'],
                                'strategy_game' => ['♟', 'لعبة استراتيجية', '12-14'],
                                'science_sim' => ['🔬', 'محاكاة علمية', '12-14'],
                                'block_code' => ['💻', 'برمجة بلوكات', '12-14'],
                                'timed_challenge' => ['⏱', 'تحدي زمني', '12-14'],
                                'quiz' => ['❓', 'أسئلة وأجوبة', 'all'],
                            ];
                        @endphp
                        @foreach ($gameTypes as $val => [$icon, $label, $age])
                            <label class="relative cursor-pointer">
                                <input type="radio" name="game_type" value="{{ $val }}" class="sr-only peer"
                                    {{ old('game_type') === $val ? 'checked' : '' }}>
                                <div
                                    class="p-3 rounded-xl border-2 border-slate-200 text-center transition
                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300">
                                    <div class="text-xl mb-1">{{ $icon }}</div>
                                    <div class="text-xs font-bold text-slate-700 leading-tight">{{ $label }}</div>
                                    <div class="text-xs mt-0.5 font-medium"
                                        style="color:{{ $age === '6-8' ? '#7c3aed' : ($age === '9-11' ? '#1d4ed8' : ($age === '12-14' ? '#15803d' : '#94a3b8')) }}">
                                        {{ $age === 'all' ? 'الكل' : $age . ' سنة' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Age group + Difficulty --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>الفئة العمرية <span class="text-red-400">*</span></label>
                        <select name="age_group" required>
                            <option value="6-8" {{ old('age_group') === '6-8' ? 'selected' : '' }}>6 – 8 سنوات</option>
                            <option value="9-11" {{ old('age_group') === '9-11' ? 'selected' : '' }}>9 – 11 سنة</option>
                            <option value="12-14" {{ old('age_group') === '12-14' ? 'selected' : '' }}>12 – 14 سنة</option>
                        </select>
                    </div>
                    <div>
                        <label>مستوى الصعوبة (1-5) <span class="text-red-400">*</span></label>
                        <div class="flex gap-2 mt-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="difficulty" value="{{ $i }}"
                                        class="sr-only peer" {{ old('difficulty', 1) == $i ? 'checked' : '' }}>
                                    <div
                                        class="py-2 rounded-xl border-2 border-slate-200 text-center text-sm font-black transition
                          peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300 text-slate-600">
                                        {{ $i }}
                                    </div>
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Rewards --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>مكافأة النجوم ⭐</label>
                        <input type="number" name="stars_reward" value="{{ old('stars_reward', 10) }}" min="1"
                            max="100">
                    </div>
                    <div>
                        <label>مكافأة XP ⚡</label>
                        <input type="number" name="xp_reward" value="{{ old('xp_reward', 50) }}" min="1"
                            max="500">
                    </div>
                </div>

                {{-- Config JSON --}}
                <div>
                    <label>إعدادات إضافية (JSON — اختياري)</label>
                    <textarea name="config" rows="3" placeholder='{"time_limit": 60, "lives": 3, "hints_allowed": true}'
                        class="font-mono text-xs">{{ old('config') }}</textarea>
                    <p class="text-xs text-slate-400 mt-1">time_limit (ثانية) · lives · hints_allowed</p>
                </div>

                {{-- AI Generated --}}
                <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-dashed border-violet-200 bg-violet-50">
                    <label class="flex items-center gap-3 cursor-pointer w-full">
                        <input type="checkbox" name="ai_generated" value="1" class="w-5 h-5 accent-violet-600"
                            {{ old('ai_generated') ? 'checked' : '' }}>
                        <div>
                            <p class="font-bold text-violet-700">🤖 توليد الأسئلة بالذكاء الاصطناعي</p>
                            <p class="text-xs text-violet-500">سيتم توليد 15 سؤال تلقائياً في الـ background بعد الحفظ</p>
                        </div>
                    </label>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="submit" class="btn-primary">حفظ اللعبة ←</button>
                    <a href="{{ route('admin.games.index') }}" class="btn-ghost">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function gameForm() {
                return {
                    selectedSubject: '',
                    filterTopics() {
                        document.querySelectorAll('.topic-option').forEach(opt => {
                            opt.hidden = this.selectedSubject && opt.dataset.subject !== this.selectedSubject;
                        });
                    }
                }
            }
        </script>
    @endpush
@endsection
