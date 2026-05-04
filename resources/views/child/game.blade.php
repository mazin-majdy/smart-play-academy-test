{{-- صفحة اللعبة — تحمّل الـ Game Engine                             --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@extends('layouts.child')
@section('title', $game->title)

@section('content')
    <div x-data="gameEngine()" x-init="init()" class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('child.subject', $game->topic->subject_id) }}"
                class="text-sm text-gray-400 hover:text-gray-600">← رجوع</a>
            <div>
                <h2 class="font-bold text-gray-800 text-center">{{ $game->title }}</h2>
                <p class="text-xs text-violet-600 text-center">مستوى الصعوبة: {{ $difficulty }}/5</p>
            </div>
            {{-- عداد الوقت --}}
            <div class="font-mono text-lg font-bold text-gray-600" x-text="formatTime(elapsed)"></div>
        </div>

        {{-- Progress bar السؤال الحالي --}}
        <div class="w-full h-2 bg-gray-200 rounded-full mb-6 overflow-hidden">
            <div class="h-full bg-violet-500 rounded-full transition-all duration-500"
                :style="`width:${(currentQ / totalQ) * 100}%`"></div>
        </div>

        {{-- GAME AREA --}}
        <div class="bg-white rounded-3xl shadow-md p-6 min-h-64">

            {{-- حالة: جاري التحميل --}}
            <div x-show="phase === 'loading'" class="text-center py-12">
                <div class="text-4xl animate-bounce mb-3">🎮</div>
                <p class="text-gray-500">جاري تحضير اللعبة...</p>
            </div>

            {{-- حالة: سؤال --}}
            <div x-show="phase === 'question'" x-transition>

                {{-- نص السؤال --}}
                <div class="text-center mb-6">
                    <div class="text-sm text-gray-400 mb-2" x-text="`سؤال ${currentQ + 1} من ${totalQ}`"></div>
                    <p class="text-xl font-bold text-gray-800" x-text="currentQuestion.content"></p>
                </div>

                {{-- الخيارات --}}
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="answer in currentQuestion.answers" :key="answer.id">
                        <button @click="submitAnswer(answer.id)" :disabled="answered"
                            class="p-4 rounded-2xl border-2 text-center font-medium transition-all"
                            :class="{
                                'border-gray-200 hover:border-violet-400 hover:bg-violet-50': !answered,
                                'border-green-400 bg-green-50 text-green-700': answered && answer.id == correctAnswerId,
                                'border-red-300 bg-red-50 text-red-500': answered && answer.id == selectedAnswerId && !
                                    lastWasCorrect,
                                'opacity-50': answered && answer.id != correctAnswerId && answer.id != selectedAnswerId,
                            }"
                            x-text="answer.text">
                        </button>
                    </template>
                </div>

                {{-- Hint --}}
                <div class="text-center mt-4" x-show="currentQuestion.hint && !hintShown">
                    <button @click="showHint()" class="text-xs text-violet-500 hover:text-violet-700 underline">
                        💡 تلميح
                    </button>
                </div>
                <div x-show="hintShown" x-transition
                    class="mt-3 bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-sm text-yellow-800 text-center"
                    x-text="currentQuestion.hint">
                </div>

                {{-- تفسير الخطأ --}}
                <div x-show="answered && !lastWasCorrect && explanation" x-transition
                    class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-800 text-center"
                    x-text="explanation">
                </div>

                {{-- زر التالي --}}
                <div class="text-center mt-5" x-show="answered">
                    <button @click="nextQuestion()"
                        class="bg-violet-600 hover:bg-violet-700 text-white font-bold px-8 py-3 rounded-full transition bounce-in">
                        <span x-text="currentQ + 1 < totalQ ? 'السؤال التالي ←' : 'إنهاء اللعبة 🏁'"></span>
                    </button>
                </div>
            </div>

            {{-- حالة: نتيجة --}}
            <div x-show="phase === 'result'" x-transition class="text-center py-6">
                <div class="text-6xl mb-4" x-text="resultEmoji"></div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2" x-text="resultMessage"></h3>

                {{-- النجوم --}}
                <div class="flex justify-center gap-2 my-4">
                    <template x-for="i in 3">
                        <span class="text-4xl transition-all"
                            :class="i <= starsEarned ? 'star-shine' : 'grayscale opacity-30'">⭐</span>
                    </template>
                </div>

                <div class="flex justify-center gap-6 text-sm text-gray-600 mb-6">
                    <div>
                        <div class="font-bold text-lg text-violet-600" x-text="accuracyPct + '%'"></div>
                        <div>الدقة</div>
                    </div>
                    <div>
                        <div class="font-bold text-lg text-yellow-500" x-text="'+' + xpEarned"></div>
                        <div>XP</div>
                    </div>
                    <div>
                        <div class="font-bold text-lg text-green-500" x-text="formatTime(elapsed)"></div>
                        <div>الوقت</div>
                    </div>
                </div>

                {{-- رسالة الـ Adaptive Engine --}}
                <div x-show="adaptMessage" class="bg-violet-50 rounded-xl p-3 text-sm text-violet-700 mb-4"
                    x-text="adaptMessage"></div>

                <div class="flex justify-center gap-3">
                    <button @click="playAgain()"
                        class="bg-violet-600 hover:bg-violet-700 text-white font-bold px-6 py-3 rounded-full transition">
                        العب مجدداً 🔄
                    </button>
                    <a href="{{ route('child.home') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-6 py-3 rounded-full transition">
                        الرئيسية 🏠
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function gameEngine() {
            return {
                // ── State ──
                phase: 'loading',
                questions: @json($questions),
                currentQ: 0,
                totalQ: 0,
                currentQuestion: null,
                answered: false,
                selectedAnswerId: null,
                correctAnswerId: null,
                lastWasCorrect: false,
                explanation: '',
                hintShown: false,
                hintsUsed: 0,

                // Session
                sessionId: null,
                gameId: {{ $game->id }},
                difficulty: {{ $difficulty }},

                // Timer
                elapsed: 0,
                timerInterval: null,

                // Results
                starsEarned: 0,
                xpEarned: 0,
                accuracyPct: 0,
                resultMessage: '',
                resultEmoji: '',
                adaptMessage: '',

                // Engagement tracking
                thinkStart: null,
                thinkTimes: [],
                frustrationCount: 0,
                pauseCount: 0,

                // ── Init ──
                async init() {
                    this.totalQ = this.questions.length;
                    this.currentQuestion = this.questions[0];

                    // بدء الجلسة
                    const res = await fetch('/play/session/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            game_id: this.gameId,
                            difficulty: this.difficulty
                        }),
                    });
                    const data = await res.json();
                    this.sessionId = data.session_id;

                    // بدء الـ timer
                    this.timerInterval = setInterval(() => this.elapsed++, 1000);
                    this.thinkStart = Date.now();

                    this.phase = 'question';

                    // Engagement tracker كل 30 ثانية
                    setInterval(() => this.sendEngagementData(), 30000);
                },

                // ── إرسال إجابة ──
                async submitAnswer(answerId) {
                    if (this.answered) return;
                    this.answered = true;
                    this.selectedAnswerId = answerId;

                    // وقت التفكير
                    const thinkTime = (Date.now() - this.thinkStart) / 1000;
                    this.thinkTimes.push(thinkTime);

                    // إرسال للـ backend
                    const res = await fetch(`/play/session/${this.sessionId}/answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            question_id: this.currentQuestion.id,
                            answer_id: answerId,
                            think_time: thinkTime,
                        }),
                    });
                    const data = await res.json();

                    this.lastWasCorrect = data.correct;
                    this.correctAnswerId = this.currentQuestion.answers.find(a =>
                        data.correct ? a.id == answerId : a.text === data.correct_answer
                    )?.id ?? answerId;
                    this.explanation = data.explanation ?? '';

                    // كشف الإحباط
                    if (!data.correct) {
                        this.frustrationCount++;
                    }
                },

                // ── السؤال التالي ──
                nextQuestion() {
                    this.currentQ++;
                    if (this.currentQ >= this.totalQ) {
                        this.endGame();
                        return;
                    }
                    this.currentQuestion = this.questions[this.currentQ];
                    this.answered = false;
                    this.selectedAnswerId = null;
                    this.correctAnswerId = null;
                    this.explanation = '';
                    this.hintShown = false;
                    this.thinkStart = Date.now();
                },

                // ── إنهاء اللعبة ──
                async endGame() {
                    clearInterval(this.timerInterval);

                    const res = await fetch(`/play/session/${this.sessionId}/end`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            hints_used: this.hintsUsed,
                            engagement_data: {
                                avg_think_time: this.thinkTimes.length ?
                                    (this.thinkTimes.reduce((a, b) => a + b, 0) / this.thinkTimes
                                        .length).toFixed(1) :
                                    0,
                                frustration_signals: this.frustrationCount,
                                pauses: this.pauseCount,
                            },
                        }),
                    });
                    const data = await res.json();

                    this.starsEarned = data.stars;
                    this.xpEarned = data.xp;
                    this.accuracyPct = data.accuracy;
                    this.resultMessage = data.message;
                    this.adaptMessage = data.mastered ? '🎉 أتقنت هذا الموضوع!' : '';
                    this.resultEmoji = ['😔', '🙂', '😊', '🌟', '🏆'][data.stars] ?? '🌟';
                    this.phase = 'result';
                },

                showHint() {
                    this.hintShown = true;
                    this.hintsUsed++;
                },

                playAgain() {
                    window.location.reload();
                },

                formatTime(s) {
                    const m = Math.floor(s / 60);
                    const sec = s % 60;
                    return `${m}:${sec.toString().padStart(2,'0')}`;
                },

                async sendEngagementData() {
                    if (!this.sessionId || this.phase !== 'question') return;
                    await fetch(`/play/session/${this.sessionId}/track`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            avg_think_time: this.thinkTimes.length ?
                                (this.thinkTimes.reduce((a, b) => a + b, 0) / this.thinkTimes.length)
                                .toFixed(1) :
                                0,
                            frustration_signals: this.frustrationCount,
                            pauses: this.pauseCount,
                            hints_requested: this.hintsUsed,
                        }),
                    });
                },
            }
        }

        // كشف الـ pause — لو الطفل غاب أكثر من 10 ثواني
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                window._pauseStart = Date.now();
            } else if (window._pauseStart) {
                const pauseDuration = (Date.now() - window._pauseStart) / 1000;
                if (pauseDuration > 10 && window.Alpine) {
                    // نزيد عداد الـ pause في الـ Alpine component
                    const comp = document.querySelector('[x-data]')?._x_dataStack?.[0];
                    if (comp) comp.pauseCount++;
                }
            }
        });
    </script>
@endpush
