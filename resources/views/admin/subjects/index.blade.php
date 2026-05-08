@extends('layouts.app')
@section('title', 'المواد الدراسية')
@section('page-title', 'إدارة المواد والمواضيع')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ══ SUBJECTS ══ --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-black text-slate-700 text-lg">المواد الدراسية</h2>
                <span class="badge badge-gray">{{ $subjects->count() }} مادة</span>
            </div>

            {{-- Add subject form --}}
            <div class="card p-5">
                <h3 class="font-bold text-slate-600 text-sm mb-4">+ إضافة مادة جديدة</h3>
                <form action="{{ route('admin.subjects.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label>الاسم (عربي)</label>
                            <input type="text" name="name" placeholder="الرياضيات" required>
                        </div>
                        <div>
                            <label>الاسم (إنجليزي)</label>
                            <input type="text" name="name_en" placeholder="Mathematics">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label>الأيقونة (Emoji)</label>
                            <input type="text" name="icon" placeholder="🔢" maxlength="4">
                        </div>
                        <div>
                            <label>اللون (HEX)</label>
                            <div class="flex gap-2">
                                <input type="color" name="color" value="#4f46e5"
                                    class="h-10 w-12 p-1 rounded-lg border border-slate-200 cursor-pointer"
                                    style="padding:.2rem">
                            </div>
                        </div>
                        <div>
                            <label>الفئة العمرية</label>
                            <select name="age_groups">
                                <option value="all">الكل</option>
                                <option value="6-8">6-8 سنة</option>
                                <option value="9-11">9-11 سنة</option>
                                <option value="12-14">12-14 سنة</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn-primary text-sm w-full justify-center">إضافة المادة</button>
                </form>
            </div>

            {{-- Subjects list --}}
            <div class="space-y-2">
                @foreach ($subjects as $subject)
                    <div class="card p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                                style="background:{{ $subject->color }}22">{{ $subject->icon }}</div>
                            <div class="flex-1">
                                <h4 class="font-black text-slate-700">{{ $subject->name }}</h4>
                                <p class="text-xs text-slate-400">{{ $subject->topics_count }} موضوع</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge {{ $subject->is_active ? 'badge-green' : 'badge-gray' }}">
                                    {{ $subject->is_active ? 'نشط' : 'مخفي' }}
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('admin.subjects.update', $subject) }}" method="POST"
                            class="flex items-end gap-2">
                            @csrf @method('PUT')
                            <div class="flex-1">
                                <label class="text-xs">الاسم</label>
                                <input type="text" name="name" value="{{ $subject->name }}" class="text-sm py-1.5">
                            </div>
                            <div class="w-12">
                                <label class="text-xs">أيقونة</label>
                                <input type="text" name="icon" value="{{ $subject->icon }}"
                                    class="text-sm py-1.5 text-center">
                            </div>
                            <div>
                                <label class="text-xs">لون</label>
                                <input type="color" name="color" value="{{ $subject->color }}"
                                    class="h-9 w-10 p-1 rounded-lg border border-slate-200 cursor-pointer">
                            </div>
                            <label class="flex items-center gap-1.5 mb-1 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="accent-green-500"
                                    {{ $subject->is_active ? 'checked' : '' }}>
                                <span class="text-xs text-slate-500">نشط</span>
                            </label>
                            <button class="btn-primary text-xs py-1.5 px-3">حفظ</button>
                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST"
                                onsubmit="return confirm('حذف المادة؟')">
                                @csrf @method('DELETE')
                                <button class="btn-danger text-xs py-1.5 px-3">حذف</button>
                            </form>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ══ TOPICS ══ --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-black text-slate-700 text-lg">المواضيع</h2>
                <span class="badge badge-gray">{{ $topics->total() }} موضوع</span>
            </div>

            {{-- Add topic --}}
            <div class="card p-5">
                <h3 class="font-bold text-slate-600 text-sm mb-4">+ إضافة موضوع جديد</h3>
                <form action="{{ route('admin.topics.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label>الاسم</label>
                        <input type="text" name="name" placeholder="الجمع البسيط" required>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label>المادة</label>
                            <select name="subject_id" required>
                                <option value="">اختر...</option>
                                @foreach ($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->icon }} {{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>الفئة العمرية</label>
                            <select name="age_group" required>
                                <option value="6-8">6-8 سنة</option>
                                <option value="9-11">9-11 سنة</option>
                                <option value="12-14">12-14 سنة</option>
                            </select>
                        </div>
                        <div>
                            <label>الصعوبة</label>
                            <select name="difficulty_level">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">مستوى {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button class="btn-primary text-sm w-full justify-center">إضافة الموضوع</button>
                </form>
            </div>

            {{-- Topics list --}}
            <div class="card overflow-hidden">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>الموضوع</th>
                                <th>المادة</th>
                                <th>العمر</th>
                                <th>الصعوبة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topics as $topic)
                                <tr>
                                    <td class="font-medium text-slate-700">{{ $topic->name }}</td>
                                    <td>
                                        <span class="text-sm">{{ $topic->subject?->icon }}
                                            {{ $topic->subject?->name }}</span>
                                    </td>
                                    <td><span class="badge badge-blue">{{ $topic->age_group }}</span></td>
                                    <td>
                                        <div class="flex gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <div
                                                    class="w-1.5 h-1.5 rounded-full {{ $i <= $topic->difficulty_level ? 'bg-indigo-500' : 'bg-slate-200' }}">
                                                </div>
                                            @endfor
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST"
                                            onsubmit="return confirm('حذف هذا الموضوع؟')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="text-red-300 hover:text-red-500 transition text-lg leading-none">×</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-slate-100">{{ $topics->links() }}</div>
            </div>
        </div>

    </div>
@endsection
