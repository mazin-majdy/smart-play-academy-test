@extends('layouts.app')
@section('title', 'تفاصيل المستخدم')
@section('page-title', 'ملف المستخدم')

@section('content')
    <div class="max-w-4xl space-y-5">

        {{-- Profile card --}}
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-black text-white"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800">{{ $user->name }}</h2>
                        <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                        <div class="flex gap-2 mt-1">
                            @php $role = $user->getRoleNames()->first() ?? '-' @endphp
                            <span class="badge badge-purple">{{ $role }}</span>
                            <span class="badge {{ $user->status === 'active' ? 'badge-green' : 'badge-red' }}">
                                {{ $user->status === 'active' ? 'نشط' : 'موقوف' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if ($user->status === 'active')
                        <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="btn-danger text-sm">تعليق الحساب</button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="btn-primary text-sm">تفعيل الحساب</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Children --}}
        @if ($user->children->count())
            <div class="card p-5">
                <h3 class="font-black text-slate-700 mb-4">👦 الأطفال ({{ $user->children->count() }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($user->children as $child)
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-sm"
                                style="background:{{ $child->avatar_color }}">
                                {{ mb_substr($child->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-700 text-sm">{{ $child->name }}</p>
                                <p class="text-xs text-slate-400">@{{ $child - > username }} · {{ $child->age_group }} سنة ·
                                    Lv.{{ $child->current_level }}</p>
                            </div>
                            <div class="text-xs font-bold text-yellow-500">{{ $child->total_stars }}⭐</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Recent sessions --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-black text-slate-700">آخر جلسات الأطفال</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>الطفل</th>
                            <th>اللعبة</th>
                            <th>النجوم</th>
                            <th>الدقة</th>
                            <th>المدة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSessions as $s)
                            <tr>
                                <td class="font-medium">{{ $s->child?->name }}</td>
                                <td>
                                    <p class="font-medium text-sm">{{ $s->game?->title }}</p>
                                    <p class="text-xs text-slate-400">{{ $s->game?->topic?->name }}</p>
                                </td>
                                <td class="font-bold text-yellow-500">{{ $s->stars_earned }}⭐</td>
                                <td>
                                    <span
                                        class="badge {{ $s->accuracy >= 70 ? 'badge-green' : 'badge-red' }}">{{ $s->accuracy }}%</span>
                                </td>
                                <td class="font-mono text-xs text-slate-500">{{ gmdate('i:s', $s->duration_seconds) }}</td>
                                <td class="text-xs text-slate-400">{{ $s->started_at->format('d/m H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-400 text-sm">لا توجد جلسات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
