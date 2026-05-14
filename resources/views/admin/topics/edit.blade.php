@extends('layouts.app')
@section('title', 'تعديل القسم: ' . $topic->name)
@section('page-title', 'تعديل القسم')

@section('topbar-actions')
    <a href="{{ route('admin.topics.index') }}" class="btn-ghost">↩️ رجوع للقائمة</a>
    <a href="{{ route('admin.topics.show', $topic) }}" class="btn-ghost">️ عرض التفاصيل</a>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-6">
            <form action="{{ route('admin.topics.update', $topic) }}" method="POST">
                @csrf @method('PUT')

                {{-- المادة التابعة (للعرض فقط، لأن الكونترولر لا يحدثها حالياً) --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">المادة التابعة</label>
                    <input type="text" value="{{ $topic->subject->name ?? 'غير محدد' }}" disabled
                        class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                </div>

                {{-- اسم القسم --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">اسم القسم *</label>
                    <input type="text" name="name" value="{{ old('name', $topic->name) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- مستوى الصعوبة --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">مستوى الصعوبة *</label>
                    <input type="number" name="difficulty_level" min="1" max="5"
                        value="{{ old('difficulty_level', $topic->difficulty_level) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <p class="text-xs text-slate-400 mt-1">من 1 (سهل) إلى 5 (صعب جداً)</p>
                    @error('difficulty_level')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- حالة التفعيل --}}
                <div class="mb-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $topic->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 rounded accent-indigo-600">
                        <span class="text-sm font-bold text-slate-700">نشط (ظاهر للمستخدمين)</span>
                    </label>
                    @error('is_active')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- أزرار الحفظ --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.topics.index') }}" class="btn-ghost">إلغاء</a>
                    <button type="submit" class="btn-primary">💾 حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
@endsection
