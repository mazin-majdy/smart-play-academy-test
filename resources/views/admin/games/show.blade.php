@extends('layouts.app')
@section('title', $game->title)
@section('page-title', $game->title)

@section('topbar-actions')
    @if ($game->ai_generated)
        <form action="{{ route('admin.games.generate', $game) }}" method="POST">
            @csrf
            <button class="btn-ghost text-xs">🤖 توليد أسئلة جديدة</button>
        </form>
    @endif
    <a href="{{ route('admin.questions.create', ['game_id' => $game->id]) }}" class="btn-primary text-xs">+ إضافة سؤال</a>
@endsection

@section('content')
    <div class="space-y-5">

        {{-- Game info --}}
        <div class="card p-6">
            <div class="flex items-start gap-5">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl flex-shrink-0 bg-indigo-50">
                    @php $icons=['drag_drop'=>'🖐','visual_match'=>'👁','math_puzzle'=>'🔢','logic_chain'=>'🧩','quiz'=>'❓','story_interactive'=>'📖','timed_challenge'=>'⏱','strategy_game'=>'♟','science_sim'=>'🔬','block_code'=>'💻','virtual_lab'=>'🧪','word_challenge'=>'📝','sound_guess'=>'🔊'] @endphp
                    {{ $icons[$game->game_type] ?? '🎮' }}
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span class="badge badge-purple">{{ $game->age_group }} سنة</span>
                        <span class="badge badge-blue">{{ $game->game_type }}</span>
                        <span class="badge badge-gray">مستوى {{ $game->difficulty }}/5</span>
                        @if ($game->ai_generated)
                            <span class="badge badge-purple">🤖 AI</span>
                        @endif
                        <span class="badge {{ $game->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $game->is_active ? 'نشطة' : 'مخفية' }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500">{{ $game->topic?->subject?->name }} → {{ $game->topic?->name }}</p>
                </div>
                <div class="flex gap-4 text-center">
                    <div class="px-4 py-3 rounded-xl bg-yellow-50">
                        <div class="font-black text-yellow-600 text-lg">{{ $stats['total_questions'] }}</div>
                        <div class="text-xs text-slate-400">سؤال</div>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-indigo-50">
                        <div class="font-black text-indigo-600 text-lg">{{ $stats['total_sessions'] }}</div>
                        <div class="text-xs text-slate-400">جلسة</div>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-green-50">
                        <div class="font-black text-green-600 text-lg">{{ $stats['avg_accuracy'] }}%</div>
                        <div class="text-xs text-slate-400">دقة متوسطة</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Questions --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-black text-slate-700">الأسئلة ({{ $game->questions->count() }})</h3>
                <div class="flex gap-2">
                    @foreach ([1, 2, 3, 4, 5] as $lvl)
                        @php $cnt = $game->questions->where('difficulty',$lvl)->count() @endphp
                        @if ($cnt)
                            <span class="badge badge-gray">مستوى {{ $lvl }}: {{ $cnt }}</span>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="divide-y divide-slate-50">
                @forelse($game->questions->take(20) as $q)
                    <div class="px-5 py-4 flex items-start gap-4 hover:bg-slate-50 transition">
                        <div class="flex-shrink-0 mt-0.5">
                            <span
                                class="badge {{ $q->is_active ? 'badge-green' : 'badge-gray' }} text-xs">{{ $q->difficulty }}⭐</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-800 text-sm leading-relaxed">
                                {{ Str::limit($q->content, 120) }}</p>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach (collect($q->answers)->take(4) as $ans)
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-full {{ $ans['is_correct'] ? 'bg-green-100 text-green-700 font-bold' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $ans['is_correct'] ? '✓' : '' }} {{ Str::limit($ans['text'], 30) }}
                                    </span>
                                @endforeach
                            </div>
                            @if ($q->hint)
                                <p class="text-xs text-amber-600 mt-1">💡 {{ Str::limit($q->hint, 80) }}</p>
                            @endif
                            @if ($q->ai_generated)
                                <span class="text-xs text-violet-400">🤖 AI Generated</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-xs text-slate-400">{{ $q->times_used }} مرة</span>
                            <form action="{{ route('admin.questions.toggle', $q) }}" method="POST">
                                @csrf @method('PATCH')
                                <button
                                    class="text-xs px-2 py-1 rounded-lg border transition {{ $q->is_active ? 'border-green-200 text-green-600 hover:bg-green-50' : 'border-slate-200 text-slate-400 hover:bg-slate-50' }}">
                                    {{ $q->is_active ? 'نشط' : 'مخفي' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.questions.destroy', $q) }}" method="POST"
                                onsubmit="return confirm('حذف هذا السؤال؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-300 hover:text-red-500 transition text-lg leading-none">×</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center">
                        <div class="text-4xl mb-3">❓</div>
                        <p class="text-slate-400 font-medium">لا توجد أسئلة بعد</p>
                        @if ($game->ai_generated)
                            <form action="{{ route('admin.games.generate', $game) }}" method="POST" class="mt-3">
                                @csrf
                                <button class="btn-primary text-sm">🤖 توليد أسئلة بالذكاء الاصطناعي</button>
                            </form>
                        @else
                            <a href="{{ route('admin.questions.create', ['game_id' => $game->id]) }}"
                                class="btn-primary text-sm mt-3 inline-flex">+ إضافة سؤال</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
