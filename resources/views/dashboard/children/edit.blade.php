@extends('layouts.app')
@section('title', 'تعديل بيانات ' . $child->name)
@section('page-title', 'تعديل بيانات ' . $child->name)

@section('content')
    <div class="max-w-xl space-y-5">

        {{-- Main info --}}
        <div class="card p-6">
            <h3 class="font-black text-slate-700 mb-5">معلومات الطفل</h3>
            <form action="{{ route('dashboard.children.update', $child) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label>الاسم</label>
                    <input type="text" name="name" value="{{ old('name', $child->name) }}" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>الفئة العمرية</label>
                        <select name="age_group">
                            @foreach (['6-8', '9-11', '12-14'] as $ag)
                                <option value="{{ $ag }}" {{ $child->age_group === $ag ? 'selected' : '' }}>
                                    {{ $ag }} سنة</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>أسلوب التعلم</label>
                        <select name="learning_style">
                            <option value="mixed" {{ $child->learning_style === 'mixed' ? 'selected' : '' }}>مختلط</option>
                            <option value="visual" {{ $child->learning_style === 'visual' ? 'selected' : '' }}>بصري</option>
                            <option value="auditory" {{ $child->learning_style === 'auditory' ? 'selected' : '' }}>سمعي</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label>الحد اليومي (دقيقة)</label>
                    <input type="number" name="daily_limit_minutes" value="{{ $child->daily_limit_minutes }}"
                        min="15" max="180" step="15">
                </div>
                <button class="btn-primary">حفظ التغييرات</button>
            </form>
        </div>

        {{-- Login credentials --}}
        <div class="card p-6">
            <h3 class="font-black text-slate-700 mb-4">بيانات دخول الطفل</h3>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">اسم المستخدم</p>
                        <p class="font-black text-slate-700 font-mono">{{ $child->username }}</p>
                    </div>
                    <div class="text-2xl">🔑</div>
                </div>
            </div>
            <form action="{{ route('dashboard.children.password', $child) }}" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label>كلمة مرور جديدة للطفل</label>
                    <input type="text" name="password" placeholder="مثلاً: 1234 أو أحمد123" required>
                    <p class="text-xs text-slate-400 mt-1">اجعلها سهلة يتذكرها طفلك</p>
                </div>
                <button class="btn-primary text-sm">تغيير كلمة المرور</button>
            </form>
        </div>

        {{-- Danger zone --}}
        <div class="card p-5 border-red-100" style="border-color:#fecaca">
            <h3 class="font-black text-red-600 mb-3">منطقة الخطر</h3>
            <p class="text-sm text-slate-500 mb-3">سيتم إيقاف حساب الطفل مؤقتاً. يمكن إعادة تفعيله لاحقاً.</p>
            <form action="{{ route('dashboard.children.destroy', $child) }}" method="POST"
                onsubmit="return confirm('هل أنت متأكد من إيقاف حساب {{ $child->name }}؟')">
                @csrf @method('DELETE')
                <button class="btn-danger">إيقاف الحساب</button>
            </form>
        </div>

    </div>
@endsection
