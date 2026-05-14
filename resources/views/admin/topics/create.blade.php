@extends('layouts.app')
@section('title', 'إضافة قسم جديد')
@section('page-title', 'إضافة قسم جديد')

@section('topbar-actions')
    <a href="{{ route('admin.topics.index') }}" class="btn-ghost">↩️ رجوع للقائمة</a>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-6">
            <form action="{{ route('admin.topics.store') }}" method="POST">
                @csrf

                {{-- المادة التابعة --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">المادة التابعة *</label>
                    <select name="subject_id"
                        class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">اختر المادة</option>
                        @foreach ($subjects as $id => $name)
                            <option value="{{ $id }}" {{ old('subject_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- اسم القسم --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">اسم القسم *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="مثال: الجمع والطرح" required>
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- الفئة العمرية --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">الفئة العمرية *</label>
                    <select name="age_group"
                        class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">اختر الفئة</option>
                        <option value="6-8" {{ old('age_group') == '6-8' ? 'selected' : '' }}>6-8 سنوات</option>
                        <option value="9-11" {{ old('age_group') == '9-11' ? 'selected' : '' }}>9-11 سنة</option>
                        <option value="12-14" {{ old('age_group') == '12-14' ? 'selected' : '' }}>12-14 سنة</option>
                    </select>
                    @error('age_group')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- مستوى الصعوبة --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">مستوى الصعوبة *</label>
                    <input type="number" name="difficulty_level" min="1" max="5"
                        value="{{ old('difficulty_level', 1) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <p class="text-xs text-slate-400 mt-1">من 1 (سهل) إلى 5 (صعب جداً)</p>
                    @error('difficulty_level')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ترتيب العرض --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">ترتيب العرض (اختياري)</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('sort_order')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- أزرار الحفظ --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.topics.index') }}" class="btn-ghost">إلغاء</a>
                    <button type="submit" class="btn-primary">💾 حفظ القسم</button>
                </div>
            </form>
        </div>
    </div>
@endsection
