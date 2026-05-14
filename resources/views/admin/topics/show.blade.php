@extends('layouts.app')
@section('title', $topic->name)
@section('page-title', 'تفاصيل القسم: ' . $topic->name)

@section('topbar-actions')
    <a href="{{ route('admin.topics.index') }}" class="btn-ghost">↩️ رجوع للقائمة</a>
    <a href="{{ route('admin.topics.edit', $topic) }}" class="btn-primary">✏️ تعديل</a>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- ══ بطاقة المعلومات الأساسية ═ --}}
        <div class="card p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-800">{{ $topic->name }}</h2>
                    <p class="text-slate-500 mt-1 flex items-center gap-2">
                        <span>{{ $topic->subject->icon ?? '' }}</span>
                        <span>{{ $topic->subject->name ?? 'غير محدد' }}</span>
                    </p>
                </div>
                <span class="badge {{ $topic->is_active ? 'badge-green' : 'badge-gray' }} mt-3 md:mt-0">
                    {{ $topic->is_active ? 'نشط' : 'مخفي' }}
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="text-xs text-slate-400 mb-1">الفئة العمرية</div>
                    <div class="font-bold text-slate-700">{{ $topic->age_group }} سنة</div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="text-xs text-slate-400 mb-1">مستوى الصعوبة</div>
                    <div class="font-bold text-slate-700">مستوى {{ $topic->difficulty_level }} / 5</div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="text-xs text-slate-400 mb-1">عدد الألعاب</div>
                    <div class="font-bold text-indigo-600">{{ $topic->games->count() }}</div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="text-xs text-slate-400 mb-1">عدد الأسئلة</div>
                    <div class="font-bold text-yellow-600">{{ $topic->questions->count() }}</div>
                </div>
            </div>

            @if ($topic->description)
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <h4 class="text-sm font-bold text-slate-600 mb-2">الوصف</h4>
                    <p class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $topic->description }}</p>
                </div>
            @endif
        </div>

        {{-- ══ الألعاب المرتبطة ══ --}}
        <div class="card p-6">
            <h3 class="font-black text-slate-700 text-lg mb-4 flex items-center gap-2">
                🎮 الألعاب التابعة لهذا القسم
            </h3>
            @if ($topic->games->count())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($topic->games as $game)
                        <div
                            class="border border-slate-200 rounded-xl p-4 hover:border-indigo-300 hover:shadow-sm transition bg-white">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-bold text-slate-800">{{ $game->name }}</h4>
                                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded-full">
                                    {{ $game->questions->count() }} سؤال
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ Str::limit($game->description ?? '', 80) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-xl">
                    <div class="text-3xl mb-2">🕹️</div>
                    <p>لا توجد ألعاب مرتبطة بهذا القسم بعد</p>
                </div>
            @endif
        </div>

        {{-- ══ منطقة الحذف ═ --}}
        <div class="flex justify-end pt-2">
            <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST"
                onsubmit="return confirm('️ هل أنت متأكد؟ سيتم حذف القسم وجميع الألعاب والأسئلة المرتبطة به نهائياً.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger flex items-center gap-2">
                    🗑️ حذف القسم نهائياً
                </button>
            </form>
        </div>
    </div>
@endsection
