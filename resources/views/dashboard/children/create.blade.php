@extends('layouts.app')
@section('title', 'إضافة طفل')
@section('page-title', 'إضافة طفل جديد')

@section('content')
    <div class="max-w-xl" x-data="{ avatar: '🦁', color: '#7c3aed' }">

        <div class="card p-6">
            <form action="{{ route('dashboard.children.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Avatar chooser --}}
                <div class="text-center py-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50">
                    <div class="text-6xl mb-3" x-text="avatar" style="line-height:1"></div>
                    <div class="flex justify-center gap-3 mb-3">
                        @foreach (['🦁', '🐼', '🦊', '🐬', '🦋', '🐯', '🐸', '🐺'] as $em)
                            <button type="button" @click="avatar='{{ $em }}'"
                                class="text-2xl w-10 h-10 rounded-xl border-2 transition flex items-center justify-center"
                                :class="avatar === '{{ $em }}' ? 'border-indigo-500 bg-indigo-50' :
                                    'border-transparent hover:border-slate-300'">
                                {{ $em }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="avatar" :value="avatar">
                    <div class="flex justify-center gap-2">
                        @foreach (['#7c3aed', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#ef4444'] as $col)
                            <button type="button" @click="color='{{ $col }}'"
                                class="w-6 h-6 rounded-full border-2 transition"
                                :class="color === '{{ $col }}' ? 'border-slate-800 scale-125' : 'border-transparent'"
                                style="background:{{ $col }}">
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="avatar_color" :value="color">
                </div>

                {{-- Name --}}
                <div>
                    <label>اسم الطفل <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="أحمد محمد">
                </div>

                {{-- Age group + Learning style --}}
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
                        <label>أسلوب التعلم</label>
                        <select name="learning_style">
                            <option value="mixed" {{ old('learning_style', 'mixed') === 'mixed' ? 'selected' : '' }}>مختلط
                                (موصى به)</option>
                            <option value="visual" {{ old('learning_style') === 'visual' ? 'selected' : '' }}>بصري
                            </option>
                            <option value="auditory"{{ old('learning_style') === 'auditory' ? 'selected' : '' }}>سمعي
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Daily limit --}}
                <div>
                    <label>الحد اليومي للشاشة (دقيقة)</label>
                    <div class="flex items-center gap-3">
                        <input type="range" name="daily_limit_minutes" min="15" max="120" step="15"
                            value="{{ old('daily_limit_minutes', 60) }}" class="flex-1 accent-indigo-600"
                            oninput="document.getElementById('limit-display').textContent=this.value+' دقيقة'">
                        <span id="limit-display" class="font-black text-indigo-600 w-24 text-center">
                            {{ old('daily_limit_minutes', 60) }} دقيقة
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">يُنصح بـ 30-60 دقيقة يومياً للأطفال</p>
                </div>

                {{-- Info box --}}
                <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100 text-sm text-indigo-700">
                    <p class="font-bold mb-1">📌 ملاحظة:</p>
                    <p>سيتم إنشاء اسم مستخدم تلقائياً وكلمة مرور افتراضية <strong>1234</strong> للطفل.</p>
                    <p>يمكنك تغييرها لاحقاً من إعدادات الطفل.</p>
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="submit" class="btn-primary">إضافة الطفل ←</button>
                    <a href="{{ route('dashboard.home') }}" class="btn-ghost">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@endsection
