@extends('layouts.app')

@section('title', 'لعب وتعلم')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4 flex flex-col items-center justify-center"
        dir="rtl">

        {{-- 🎮 منطقة اللعب الرئيسية --}}
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden border-4 border-indigo-200 relative">

            {{-- 📊 الهيدر (المؤقت والنقاط) --}}
            <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">⏱️</span>
                    <span id="timer" class="font-black text-xl">00:00</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xl">⭐</span>
                    <span id="score" class="font-black text-xl">0</span>
                </div>
                <div class="flex-1 mx-4">
                    <div class="h-3 bg-indigo-900 rounded-full overflow-hidden">
                        <div id="progress-bar" class="h-full bg-yellow-400 w-0 transition-all duration-300"></div>
                    </div>
                </div>
            </div>

            {{-- 🕹️ المحتوى المتغير (يبدأ بزر "ابدأ") --}}
            <div id="game-container" class="p-8 text-center min-h-[400px] flex flex-col justify-center">

                {{-- شاشة البدء --}}
                <div id="start-screen">
                    <div class="text-6xl mb-4">🚀</div>
                    <h2 class="text-3xl font-black text-slate-800 mb-2">جاهز للمغامرة؟</h2>
                    <p class="text-slate-500 mb-8">أجب على الأسئلة واربح النجوم!</p>
                    <button onclick="startGame()"
                        class="btn-primary px-8 py-4 text-xl font-black rounded-2xl hover:scale-105 transition-transform shadow-lg">
                        ابدأ اللعب! ▶️
                    </button>
                </div>

                {{-- شاشة السؤال (مخفية مبدئياً) --}}
                <div id="question-screen" class="hidden">
                    <div class="flex justify-between text-sm text-slate-400 mb-4">
                        <span>السؤال <span id="q-current">1</span> من <span id="q-total">10</span></span>
                    </div>

                    <h3 id="question-text" class="text-2xl font-bold text-slate-800 mb-8 leading-relaxed"></h3>

                    <div id="options-grid" class="grid grid-cols-1 gap-4 mb-6 text-right">
                        <!-- الخيارات راح تتضاف هنا بالجافاسكربت -->
                    </div>

                    <button id="submit-btn" onclick="submitAnswer()" disabled
                        class="w-full py-4 bg-slate-200 text-slate-400 font-bold rounded-xl cursor-not-allowed transition-all">
                        تحقق من الإجابة ✨
                    </button>
                </div>

                {{-- شاشة النهاية --}}
                <div id="end-screen" class="hidden">
                    <div class="text-6xl mb-4"></div>
                    <h2 class="text-3xl font-black text-slate-800 mb-2">أحسنت! جلسة ممتازة</h2>
                    <div class="bg-yellow-50 p-6 rounded-2xl border-2 border-yellow-200 my-6">
                        <div class="text-4xl font-black text-yellow-600 mb-2">+<span id="final-points">0</span></div>
                        <div class="text-slate-500 font-medium">نقطة مجموعة</div>
                        <div class="mt-2 text-xl">⭐ +<span id="final-stars">0</span></div>
                    </div>
                    <button onclick="window.location.href='{{ route('dashboard.home') }}'"
                        class="btn-primary px-8 py-3 font-bold rounded-xl">
                        العودة للرئيسية 🏠
                    </button>
                </div>
            </div>
        </div>

        {{-- 📜 الجافاسكربت (المنطق) --}}
        <script>
            // ️ الإعدادات القادمة من السيرفر
            const CONFIG = {
                childId: {{ $child->id }},
                gameId: {{ $game->id }},
                startUrl: '{{ route('play.start', ['child' => $child, 'game' => $game]) }}',
                answerUrl: '{{ route('play.answer', 'session_placeholder') }}', // راح نستبدلها بالـ ID
                finishUrl: '{{ route('play.finish', 'session_placeholder') }}',
                csrf: '{{ csrf_token() }}'
            };

            //  حالة اللعبة
            let state = {
                sessionId: null,
                questions: [],
                currentQIndex: 0,
                timer: 0,
                timerInterval: null,
                selectedAnswer: null,
                points: 0,
                stars: 0
            };

            // ⏱️ دالة المؤقت
            function startTimer() {
                state.timerInterval = setInterval(() => {
                    state.timer++;
                    const mins = Math.floor(state.timer / 60).toString().padStart(2, '0');
                    const secs = (state.timer % 60).toString().padStart(2, '0');
                    document.getElementById('timer').textContent = `${mins}:${secs}`;
                }, 1000);
            }

            // 🎮 1. بدء اللعبة
            async function startGame() {
                const btn = document.querySelector('#start-screen button');
                btn.disabled = true;
                btn.textContent = 'جاري التحميل...';

                try {
                    const res = await fetch(CONFIG.startUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CONFIG.csrf
                        }
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'فشل البدء');

                    // حفظ البيانات
                    state.sessionId = data.session_id;
                    state.questions = data.questions;

                    // تحديث الروابط مع الـ Session ID
                    CONFIG.answerUrl = CONFIG.answerUrl.replace('session_placeholder', state.sessionId);
                    CONFIG.finishUrl = CONFIG.finishUrl.replace('session_placeholder', state.sessionId);

                    // التبديل للشاشة التالية
                    document.getElementById('start-screen').classList.add('hidden');
                    document.getElementById('question-screen').classList.remove('hidden');
                    document.getElementById('q-total').textContent = state.questions.length;

                    startTimer();
                    loadQuestion();

                } catch (err) {
                    alert(err.message);
                    btn.disabled = false;
                    btn.textContent = 'حاول مرة ثانية ❌';
                }
            }

            // 📖 2. عرض السؤال
            function loadQuestion() {
                const q = state.questions[state.currentQIndex];
                document.getElementById('q-current').textContent = state.currentQIndex + 1;
                document.getElementById('question-text').textContent = q.text;

                // تحديث شريط التقدم
                const progress = ((state.currentQIndex) / state.questions.length) * 100;
                document.getElementById('progress-bar').style.width = `${progress}%`;

                // توليد الخيارات
                const grid = document.getElementById('options-grid');
                grid.innerHTML = '';
                state.selectedAnswer = null;

                // تعطيل زر التحقق
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.className =
                    "w-full py-4 bg-slate-200 text-slate-400 font-bold rounded-xl cursor-not-allowed transition-all";

                q.options.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className =
                        "p-4 text-right border-2 border-slate-200 rounded-xl font-bold hover:border-indigo-400 hover:bg-indigo-50 transition-all text-lg";
                    btn.textContent = opt;
                    btn.onclick = () => selectOption(btn, opt);
                    grid.appendChild(btn);
                });
            }

            //  3. اختيار إجابة
            function selectOption(btn, value) {
                // إزالة التحديد السابق
                document.querySelectorAll('#options-grid button').forEach(b => {
                    b.classList.remove('border-indigo-600', 'bg-indigo-100');
                });

                // تحديد الجديد
                btn.classList.add('border-indigo-600', 'bg-indigo-100');
                state.selectedAnswer = value;

                // تفعيل زر التحقق
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = false;
                submitBtn.className =
                    "w-full py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg";
            }

            // ✅ 4. إرسال الإجابة
            async function submitAnswer() {
                if (!state.selectedAnswer) return;

                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'جاري التحقق...';

                const q = state.questions[state.currentQIndex];
                const timeTaken = 30; // *حالياً ثابتة، تقدر تحسبها بدقة*

                try {
                    const res = await fetch(CONFIG.answerUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CONFIG.csrf,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: q.id,
                            given_answer: state.selectedAnswer,
                            time_taken: timeTaken
                        })
                    });

                    const result = await res.json();
                    if (!res.ok) throw new Error('خطأ في الإرسال');

                    // تحديث النقاط
                    state.points += result.points_earned;
                    document.getElementById('score').textContent = state.points;

                    // تأثير بصري (صح/خطأ)
                    highlightAnswer(result.is_correct);

                    // الانتقال للسؤال التالي بعد ثانية ونصف
                    setTimeout(() => {
                        state.currentQIndex++;
                        if (state.currentQIndex < state.questions.length) {
                            loadQuestion();
                        } else {
                            finishGame();
                        }
                    }, 1500);

                } catch (err) {
                    alert(err.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'تحقق من الإجابة ';
                }
            }

            //  تأثيرات بصرية
            function highlightAnswer(isCorrect) {
                const grid = document.getElementById('options-grid');
                const btns = grid.querySelectorAll('button');

                btns.forEach(btn => {
                    btn.disabled = true; // منع التغيير
                    if (btn.classList.contains('border-indigo-600')) {
                        // الزر المختار
                        if (isCorrect) {
                            btn.classList.remove('border-indigo-600', 'bg-indigo-100');
                            btn.classList.add('bg-green-100', 'border-green-500', 'text-green-700');
                            btn.innerHTML += ' ✅';
                        } else {
                            btn.classList.remove('border-indigo-600', 'bg-indigo-100');
                            btn.classList.add('bg-red-100', 'border-red-500', 'text-red-700');
                            btn.innerHTML += ' ❌';
                        }
                    }
                });
            }

            // 🏁 5. إنهاء اللعبة
            async function finishGame() {
                clearInterval(state.timerInterval);

                // إخفاء السؤال وإظهار النهاية
                document.getElementById('question-screen').classList.add('hidden');
                document.getElementById('end-screen').classList.remove('hidden');
                document.getElementById('final-points').textContent = state.points;
                document.getElementById('progress-bar').style.width = '100%';

                // إرسال الإنهاء للسيرفر
                try {
                    await fetch(CONFIG.finishUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CONFIG.csrf
                        }
                    });
                } catch (e) {
                    console.error("Failed to report finish", e);
                }
            }
        </script>
    </div>
@endsection
