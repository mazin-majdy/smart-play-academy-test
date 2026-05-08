@extends('layouts.app')
@section('title', 'لوحة الأدمن')
@section('page-title', 'لوحة التحكم الإدارية')

@section('topbar-actions')
    <a href="{{ route('admin.analytics') }}" class="btn-ghost text-xs">📊 التحليلات</a>
    <a href="{{ route('admin.games.create') }}" class="btn-primary text-xs">+ لعبة جديدة</a>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- ══ KPI Stats ══ --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach ([['🧑‍🤝‍🧑', 'المستخدمون', $stats['total_users'], 'bg-indigo-50', 'text-indigo-600', 'border-indigo-100', 'route' => 'admin.users'], ['👦', 'الأطفال', $stats['total_children'], 'bg-violet-50', 'text-violet-600', 'border-violet-100', 'route' => null], ['🎮', 'الألعاب', $stats['total_games'], 'bg-emerald-50', 'text-emerald-600', 'border-emerald-100', 'route' => 'admin.games.index'], ['⚡', 'جلسات اليوم', $stats['sessions_today'], 'bg-amber-50', 'text-amber-600', 'border-amber-100', 'route' => null], ['🔴', 'نشطون الآن', $stats['active_now'], 'bg-rose-50', 'text-rose-600', 'border-rose-100', 'route' => null]] as $card)
                <div class="rounded-2xl p-5 {{ $card[3] }} border {{ $card[4] }} {{ isset($card['route']) && $card['route'] ? 'hover:shadow-md transition cursor-pointer' : '' }}"
                    @if (isset($card['route']) && $card['route']) onclick="window.location='{{ route($card['route']) }}'" @endif>
                    <div class="text-2xl mb-2">{{ $card[0] }}</div>
                    <div class="text-2xl font-black {{ $card[4] }}">{{ $card[2] }}</div>
                    <div class="text-xs font-medium text-slate-500 mt-1">{{ $card[1] }}</div>
                </div>
            @endforeach
        </div>

        {{-- ══ Charts Row ══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Sessions Chart --}}
            <div class="lg:col-span-2 card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-slate-700">جلسات اللعب — آخر 7 أيام</h3>
                </div>
                <div class="flex items-end gap-2 h-32">
                    @php $maxC = max(collect($sessionsChart)->pluck('count')->max(), 1) @endphp
                    @foreach ($sessionsChart as $day)
                        <div class="flex-1 flex flex-col items-center gap-1.5">
                            <span class="text-xs font-bold text-slate-500">{{ $day['count'] }}</span>
                            <div class="w-full rounded-t-lg transition-all"
                                style="height:{{ round(($day['count'] / $maxC) * 96) }}px;min-height:4px;background:linear-gradient(to top,#4f46e5,#818cf8)">
                            </div>
                            <span class="text-xs text-slate-400">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Popular Games --}}
            <div class="card p-5">
                <h3 class="font-black text-slate-700 mb-4">🔥 أشهر الألعاب</h3>
                <div class="space-y-3">
                    @php $maxG = max($popularGames->pluck('sessions_count')->max(), 1) @endphp
                    @foreach ($popularGames->take(5) as $i => $game)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-slate-700 truncate">{{ $game->title }}</span>
                                <span class="text-slate-400 text-xs flex-shrink-0 mr-2">{{ $game->sessions_count }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full"
                                    style="width:{{ round(($game->sessions_count / $maxG) * 100) }}%;background:{{ ['#4f46e5', '#7c3aed', '#ec4899', '#f59e0b', '#10b981'][$i % 5] }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.games.index') }}" class="btn-ghost w-full justify-center mt-4 text-xs">عرض الكل
                    ←</a>
            </div>
        </div>

        {{-- ══ Recent Users ══ --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-black text-slate-700">أحدث المستخدمين</h3>
                <a href="{{ route('admin.users') }}" class="text-xs text-indigo-500 font-bold">عرض الكل ←</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>الدور</th>
                            <th>الأطفال</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black text-white flex-shrink-0"
                                            style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php $role = $user->getRoleNames()->first() ?? 'user' @endphp
                                    <span
                                        class="badge {{ match ($role) {'admin' => 'badge-purple','parent' => 'badge-blue','teacher' => 'badge-green',default => 'badge-gray'} }}">
                                        {{ $role }}
                                    </span>
                                </td>
                                <td class="font-medium">{{ $user->children_count }}</td>
                                <td>
                                    <span class="badge {{ $user->status === 'active' ? 'badge-green' : 'badge-red' }}">
                                        {{ $user->status === 'active' ? 'نشط' : 'موقوف' }}
                                    </span>
                                </td>
                                <td class="text-slate-400 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}"
                                        class="text-indigo-400 hover:text-indigo-600 text-xs font-bold">عرض</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
