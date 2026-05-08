@extends('layouts.app')
@section('title', 'الأسئلة')
@section('page-title', 'إدارة الأسئلة')

@section('topbar-actions')
    <a href="{{ route('admin.questions.create') }}" class="btn-primary">+ إضافة سؤال</a>
@endsection

@section('content')
    <div class="space-y-5">

        {{-- Filters --}}
        <form class="card p-4 flex flex-wrap gap-3 items-end">
            <div class="w-48">
                <label>اللعبة</label>
                <select name="game_id">
                    <option value="">الكل</option>
                    @foreach ($games as $g)
                        <option value="{{ $g->id }}" {{ request('game_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label>الصعوبة</label>
                <select name="difficulty">
                    <option value="">الكل</option>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('difficulty') == $i ? 'selected' : '' }}>مستوى
                            {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end gap-2">
                <label class="flex items-center gap-2 mb-0 cursor-pointer">
                    <input type="checkbox" name="ai_only" value="1" class="accent-violet-600 w-4 h-4"
                        {{ request()->boolean('ai_only') ? 'checked' : '' }}>
                    <span class="text-sm font-bold text-slate-600">AI فقط 🤖</span>
                </label>
            </div>
            <button class="btn-primary">بحث</button>
            <a href="{{ route('admin.questions.index') }}" class="btn-ghost">إعادة تعيين</a>
        </form>

        {{-- Stats bar --}}
        <div class="grid grid-cols-4 gap-3">
            @foreach ([['إجمالي الأسئلة', $questions->total(), 'badge-blue'], ['أسئلة AI', \App\Models\Question::where('ai_generated', true)->count(), 'badge-purple'], ['نشطة', \App\Models\Question::where('is_active', true)->count(), 'badge-green'], ['مخفية', \App\Models\Question::where('is_active', false)->count(), 'badge-gray']] as [$label, $val, $badge])
                <div class="card p-4 text-center">
                    <div class="text-xl font-black text-slate-700">{{ $val }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        {{-- Questions table --}}
        <div class="card overflow-hidden">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:40%">السؤال</th>
                            <th>اللعبة</th>
                            <th>الصعوبة</th>
                            <th>الاستخدام</th>
                            <th>نسبة النجاح</th>
                            <th>المصدر</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $q)
                            <tr>
                                <td>
                                    <p class="font-medium text-slate-800 text-sm leading-snug">
                                        {{ Str::limit($q->content, 100) }}</p>
                                    {{-- Answers preview --}}
                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                        @foreach (collect($q->answers)->take(4) as $ans)
                                            <span
                                                class="text-xs px-1.5 py-0.5 rounded {{ $ans['is_correct'] ? 'bg-green-100 text-green-700 font-bold' : 'bg-slate-100 text-slate-400' }}">
                                                {{ $ans['is_correct'] ? '✓ ' : '' }}{{ Str::limit($ans['text'], 20) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm font-medium text-slate-700">{{ Str::limit($q->game?->title, 30) }}
                                    </p>
                                    <p class="text-xs text-slate-400">{{ $q->game?->age_group }}</p>
                                </td>
                                <td>
                                    <div class="flex gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <div
                                                class="w-2 h-2 rounded-full {{ $i <= $q->difficulty ? 'bg-indigo-500' : 'bg-slate-200' }}">
                                            </div>
                                        @endfor
                                    </div>
                                </td>
                                <td class="font-mono text-sm text-center">{{ $q->times_used }}</td>
                                <td>
                                    @php $rate = (float)$q->success_rate @endphp
                                    <span
                                        class="font-bold text-sm {{ $rate >= 70 ? 'text-green-600' : ($rate >= 40 ? 'text-amber-600' : 'text-red-500') }}">
                                        {{ $rate }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $q->ai_generated ? 'badge-purple' : 'badge-gray' }}">
                                        {{ $q->ai_generated ? '🤖 AI' : '✏️ يدوي' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-1">
                                        <form action="{{ route('admin.questions.toggle', $q) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button
                                                class="text-xs px-2 py-1 rounded-lg border transition
                               {{ $q->is_active ? 'border-green-200 text-green-600 bg-green-50' : 'border-slate-200 text-slate-400' }}">
                                                {{ $q->is_active ? 'نشط' : 'مخفي' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.questions.destroy', $q) }}" method="POST"
                                            onsubmit="return confirm('حذف هذا السؤال نهائياً؟')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="text-red-300 hover:text-red-500 transition text-lg px-1 leading-none">×</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-slate-400">لا توجد أسئلة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-100">{{ $questions->links() }}</div>
        </div>
    </div>
@endsection
