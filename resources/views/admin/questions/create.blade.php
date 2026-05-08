@extends('layouts.app')
@section('title', 'سؤال جديد')
@section('page-title', 'إضافة سؤال جديد')

@section('content')
    <div class="max-w-2xl" x-data="questionForm()">
        <div class="card p-6">
            <form action="{{ route('admin.questions.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Game --}}
                <div>
                    <label>اللعبة <span class="text-red-400">*</span></label>
                    <select name="game_id" required>
                        <option value="">اختر اللعبة...</option>
                        @foreach ($games as $g)
                            <option value="{{ $g->id }}"
                                {{ request('game_id') == $g->id || old('game_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->title }} ({{ $g->topic?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Question content --}}
                <div>
                    <label>نص السؤال <span class="text-red-400">*</span></label>
                    <textarea name="content" rows="3" required placeholder="اكتب السؤال هنا...">{{ old('content') }}</textarea>
                </div>

                {{-- Answer type + Difficulty --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>نوع الإجابة</label>
                        <select name="answer_type">
                            @foreach (['single_choice' => 'اختيار واحد', 'multiple_choice' => 'اختيار متعدد', 'drag_drop' => 'سحب وإفلات', 'fill_blank' => 'ملء الفراغ', 'order_steps' => 'ترتيب خطوات', 'matching' => 'مطابقة'] as $v => $l)
                                <option value="{{ $v }}"
                                    {{ old('answer_type', 'single_choice') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>مستوى الصعوبة</label>
                        <select name="difficulty">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('difficulty', 1) == $i ? 'selected' : '' }}>مستوى
                                    {{ $i }}/5</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Answers --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="m-0">الإجابات <span class="text-red-400">*</span></label>
                        <button type="button" @click="addAnswer()"
                            class="text-xs text-indigo-500 hover:text-indigo-700 font-bold">+ إضافة إجابة</button>
                    </div>
                    <div class="space-y-2" id="answers-container">
                        <template x-for="(ans, idx) in answers" :key="idx">
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-2 flex-shrink-0 cursor-pointer">
                                    <input type="radio" :name="'answers[' + idx + '][is_correct]'" value="1"
                                        :checked="ans.is_correct" @change="setCorrect(idx)"
                                        class="accent-green-500 w-4 h-4">
                                    <span class="text-xs text-slate-500">صحيحة</span>
                                </label>
                                <input type="text" :name="'answers[' + idx + '][text]'" x-model="ans.text"
                                    :placeholder="'الإجابة ' + (idx + 1)" required class="flex-1">
                                <input type="hidden" :name="'answers[' + idx + '][is_correct]'"
                                    :value="ans.is_correct ? 1 : 0">
                                <button type="button" @click="removeAnswer(idx)" x-show="answers.length > 2"
                                    class="text-red-300 hover:text-red-500 text-xl leading-none transition">×</button>
                            </div>
                        </template>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">اختر الإجابة الصحيحة بالنقر على دائرة الاختيار</p>
                </div>

                {{-- Explanation + Hint --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>شرح الإجابة (للـ AI Tutor)</label>
                        <textarea name="explanation" rows="2" placeholder="لماذا هذه الإجابة صحيحة؟">{{ old('explanation') }}</textarea>
                    </div>
                    <div>
                        <label>تلميح للطفل</label>
                        <textarea name="hint" rows="2" placeholder="تلميح يساعد دون إعطاء الإجابة...">{{ old('hint') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="submit" class="btn-primary">حفظ السؤال ←</button>
                    <a href="{{ route('admin.questions.index') }}" class="btn-ghost">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function questionForm() {
                return {
                    answers: [{
                            text: '',
                            is_correct: true
                        },
                        {
                            text: '',
                            is_correct: false
                        },
                        {
                            text: '',
                            is_correct: false
                        },
                        {
                            text: '',
                            is_correct: false
                        },
                    ],
                    addAnswer() {
                        this.answers.push({
                            text: '',
                            is_correct: false
                        });
                    },
                    removeAnswer(idx) {
                        if (this.answers.length > 2) {
                            const wasCorrect = this.answers[idx].is_correct;
                            this.answers.splice(idx, 1);
                            if (wasCorrect && this.answers.length > 0) {
                                this.answers[0].is_correct = true;
                            }
                        }
                    },
                    setCorrect(idx) {
                        this.answers.forEach((a, i) => a.is_correct = i === idx);
                    }
                }
            }
        </script>
    @endpush
@endsection
