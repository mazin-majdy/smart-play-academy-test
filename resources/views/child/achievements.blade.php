{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- resources/views/child/achievements.blade.php                      --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@extends('layouts.child')
@section('title', 'إنجازاتي')

@section('content')
    <div class="space-y-5">

        {{-- Header --}}
        <div class="text-center py-4">
            <div class="text-5xl mb-2">🏆</div>
            <h1 class="text-2xl font-black" style="color:#1e293b">إنجازاتي</h1>
            <p class="text-sm" style="color:#64748b">{{ $earned->count() }} من {{ $all->count() }} إنجاز</p>
        </div>

        {{-- Progress ring --}}
        <div class="flex justify-center mb-2">
            <div class="relative w-28 h-28">
                <svg width="112" height="112" viewBox="0 0 112 112">
                    <circle cx="56" cy="56" r="48" fill="none" stroke="#f1f5f9" stroke-width="10" />
                    <circle cx="56" cy="56" r="48" fill="none" stroke="#fbbf24" stroke-width="10"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $all->count() > 0 ? round(($earned->count() / $all->count()) * 302) : 0 }} 302"
                        transform="rotate(-90 56 56)" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-black" style="color:#f59e0b">
                            {{ $all->count() > 0 ? round(($earned->count() / $all->count()) * 100) : 0 }}%</div>
                        <div class="text-xs" style="color:#94a3b8">مكتمل</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Earned achievements --}}
        @if ($earned->count())
            <div>
                <h2 class="font-black text-base mb-3" style="color:#1e293b">✅ حصلت عليها</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($earned as $a)
                        <div class="rounded-2xl p-4 relative overflow-hidden"
                            style="background:linear-gradient(135deg,#fef9c3,#fef3c7);border:2px solid #fde047">
                            <div class="text-3xl mb-2">{{ $a->icon }}</div>
                            <h3 class="font-black text-sm" style="color:#78350f">{{ $a->name }}</h3>
                            <p class="text-xs mt-0.5" style="color:#92400e">{{ $a->description }}</p>
                            @if ($a->stars_reward)
                                <div class="mt-2 text-xs font-bold" style="color:#f59e0b">+{{ $a->stars_reward }} ⭐</div>
                            @endif
                            {{-- Earned date --}}
                            <div class="absolute top-3 left-3 text-xs" style="color:#92400e;opacity:.6">
                                {{ \Carbon\Carbon::parse($a->pivot->earned_at)->format('d/m') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Locked achievements --}}
        @php $locked = $all->whereNotIn('id', $earned->pluck('id')); @endphp
        @if ($locked->count())
            <div>
                <h2 class="font-black text-base mb-3" style="color:#1e293b">🔒 في الانتظار</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($locked as $a)
                        <div class="rounded-2xl p-4 relative overflow-hidden grayscale"
                            style="background:#f8fafc;border:2px solid #e2e8f0">
                            <div class="text-3xl mb-2 opacity-40">{{ $a->icon }}</div>
                            <h3 class="font-black text-sm" style="color:#475569">{{ $a->name }}</h3>
                            <p class="text-xs mt-0.5" style="color:#94a3b8">{{ $a->description }}</p>
                            @if ($a->stars_reward)
                                <div class="mt-2 text-xs font-bold" style="color:#94a3b8">+{{ $a->stars_reward }} ⭐</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection
