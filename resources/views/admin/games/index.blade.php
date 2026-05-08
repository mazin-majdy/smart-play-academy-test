@extends('layouts.app')
@section('title', 'الألعاب')
@section('page-title', 'إدارة الألعاب')

@section('topbar-actions')
    <a href="{{ route('admin.games.create') }}" class="btn-primary">+ إضافة لعبة</a>
@endsection

@section('content')
    <div class="space-y-5">

        {{-- Filters --}}
        <form class="card p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-40">
                <label>بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم اللعبة...">
            </div>
            <div class="w-40">
                <label>نوع اللعبة</label>
                <select name="game_type">
                    <option value="">الكل</option>
                    @foreach (['drag_drop' => 'سحب وإفلات', 'visual_match' => 'مطابقة بصرية', 'math_puzzle' => 'لغز رياضي', 'logic_chain' => 'سلسلة منطقية', 'quiz' => 'أسئلة', 'story_interactive' => 'قصة تفاعلية', 'timed_challenge' => 'تحدي زمني', 'strategy_game' => 'لعبة استراتيجية', 'science_sim' => 'محاكاة علمية', 'block_code' => 'برمجة بلوكات', 'virtual_lab' => 'مختبر افتراضي', 'word_challenge' => 'تحدي كلمات', 'sound_guess' => 'تخمين صوتي'] as $v => $l)
                        <option value="{{ $v }}" {{ request('game_type') === $v ? 'selected' : '' }}>
                            {{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label>الفئة العمرية</label>
                <select name="age_group">
                    <option value="">الكل</option>
                    <option value="6-8" {{ request('age_group') === '6-8' ? 'selected' : '' }}>6–8 سنوات</option>
                    <option value="9-11" {{ request('age_group') === '9-11' ? 'selected' : '' }}>9–11 سنة</option>
                    <option value="12-14" {{ request('age_group') === '12-14' ? 'selected' : '' }}>12–14 سنة</option>
                </select>
            </div>
            <div class="w-32">
                <label>الصعوبة</label>
                <select name="difficulty">
                    <option value="">الكل</option>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('difficulty') == $i ? 'selected' : '' }}>مستوى
                            {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <button class="btn-primary">بحث</button>
            <a href="{{ route('admin.games.index') }}" class="btn-ghost">إعادة تعيين</a>
        </form>

        {{-- Games Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($games as $game)
                @php
                    $typeIcons = [
                        'drag_drop' => '🖐',
                        'visual_match' => '👁',
                        'math_puzzle' => '🔢',
                        'logic_chain' => '🧩',
                        'quiz' => '❓',
                        'story_interactive' => '📖',
                        'timed_challenge' => '⏱',
                        'strategy_game' => '♟',
                        'science_sim' => '🔬',
                        'block_code' => '💻',
                        'virtual_lab' => '🧪',
                        'word_challenge' => '📝',
                        'sound_guess' => '🔊',
                    ];
                    $ageColors = [
                        '6-8' => ['bg' => '#ede9fe', 'text' => '#7c3aed'],
                        '9-11' => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
                        '12-14' => ['bg' => '#dcfce7', 'text' => '#15803d'],
                    ];
                    $ac = $ageColors[$game->age_group] ?? ['bg' => '#f1f5f9', 'text' => '#64748b'];
                @endphp
                <div class="card p-5 flex flex-col gap-3 hover:shadow-md transition">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                                style="background:{{ $ac['bg'] }}">
                                {{ $typeIcons[$game->game_type] ?? '🎮' }}
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 leading-tight">{{ $game->title }}</h3>
                                <p class="text-xs text-slate-400">{{ $game->topic?->subject?->name }} ·
                                    {{ $game->topic?->name }}</p>
                            </div>
                        </div>
                        {{-- Active toggle --}}
                        <form action="{{ route('admin.games.update', $game) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="is_active" value="{{ $game->is_active ? 0 : 1 }}">
                            <button class="text-xs px-2.5 py-1 rounded-full font-bold border transition"
                                style="{{ $game->is_active ? 'background:#dcfce7;color:#15803d;border-color:#bbf7d0' : 'background:#f1f5f9;color:#94a3b8;border-color:#e2e8f0' }}">
                                {{ $game->is_active ? 'نشط' : 'مخفي' }}
                            </button>
                        </form>
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-1.5">
                        <span class="badge"
                            style="background:{{ $ac['bg'] }};color:{{ $ac['text'] }}">{{ $game->age_group }}
                            سنة</span>
                        <span class="badge badge-gray">مستوى {{ $game->difficulty }}/5</span>
                        @if ($game->ai_generated)
                            <span class="badge badge-purple">🤖 AI</span>
                        @endif
                        <span class="badge badge-gray">{{ $game->game_type }}</span>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 rounded-xl bg-slate-50">
                            <div class="font-black text-sm text-slate-700">{{ $game->sessions()->count() }}</div>
                            <div class="text-xs text-slate-400">جلسة</div>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50">
                            <div class="font-black text-sm text-slate-700">
                                {{ $game->questions()->where('is_active', true)->count() }}</div>
                            <div class="text-xs text-slate-400">سؤال</div>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50">
                            <div class="font-black text-sm text-yellow-500">{{ $game->stars_reward }}⭐</div>
                            <div class="text-xs text-slate-400">مكافأة</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 pt-1 border-t border-slate-100">
                        <a href="{{ route('admin.games.show', $game) }}"
                            class="btn-ghost text-xs flex-1 justify-center">عرض التفاصيل</a>
                        @if ($game->ai_generated)
                            <form action="{{ route('admin.games.generate', $game) }}" method="POST">
                                @csrf
                                <button class="btn-primary text-xs py-1.5 px-3">🤖 توليد أسئلة</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 card p-12 text-center">
                    <div class="text-5xl mb-3">🎮</div>
                    <p class="font-bold text-slate-500 text-lg mb-1">لا توجد ألعاب</p>
                    <p class="text-sm text-slate-400 mb-4">أضف أول لعبة لتبدأ</p>
                    <a href="{{ route('admin.games.create') }}" class="btn-primary">+ إضافة لعبة</a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($games->hasPages())
            <div class="flex justify-center">{{ $games->links() }}</div>
        @endif

    </div>
@endsection
