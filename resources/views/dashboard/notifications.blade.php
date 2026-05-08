@extends('layouts.app')
@section('title', 'الإشعارات')
@section('page-title', 'الإشعارات')

@section('topbar-actions')
    <form action="{{ route('dashboard.notifications.read-all') }}" method="POST">
        @csrf
        <button class="btn-ghost text-xs">✓ تعليم الكل مقروء</button>
    </form>
@endsection

@section('content')
    <div class="card overflow-hidden">
        @if ($notifications->isEmpty())
            <div class="p-16 text-center">
                <div class="text-5xl mb-3">🔔</div>
                <p class="font-bold text-slate-500">لا توجد إشعارات</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($notifications as $n)
                    <div
                        class="flex items-start gap-4 px-6 py-4 transition hover:bg-slate-50 {{ $n->is_read ? '' : 'bg-indigo-50/40 border-r-4 border-indigo-400' }}">

                        {{-- Icon --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                            style="background:{{ $n->is_read ? '#f8fafc' : '#ede9fe' }}">
                            @switch($n->type)
                                @case('frustration_detected')
                                    ⚠️
                                @break

                                @case('achievement')
                                    🏆
                                @break

                                @case('limit_reached')
                                    ⏱️
                                @break

                                @case('weekly_report')
                                    📊
                                @break

                                @case('streak_broken')
                                    💔
                                @break

                                @case('topic_mastered')
                                    🎓
                                @break

                                @default
                                    📣
                            @endswitch
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-bold text-slate-800 text-sm leading-snug">{{ $n->title }}</p>
                                @if (!$n->is_read)
                                    <span class="badge badge-purple flex-shrink-0">جديد</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-500 mt-1 leading-relaxed">{{ $n->body }}</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
                                @if ($n->child)
                                    <span class="text-xs text-slate-400">· {{ $n->child->name }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Delete --}}
                        <form action="{{ route('dashboard.notifications.destroy', $n) }}" method="POST"
                            class="flex-shrink-0">
                            @csrf @method('DELETE')
                            <button class="text-slate-300 hover:text-red-400 transition text-lg leading-none">×</button>
                        </form>
                    </div>
                @endforeach
            </div>
            <div class="px-6 py-3 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
