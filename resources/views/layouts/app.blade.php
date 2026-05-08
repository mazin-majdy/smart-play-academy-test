<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') — أكاديمية اللعب الذكية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f4ff',
                            100: '#e0e9ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca'
                        },
                        lime: {
                            400: '#a3e635',
                            500: '#84cc16'
                        },
                    },
                    fontFamily: {
                        arabic: ['Tajawal', 'sans-serif'],
                        mono: ['DM Mono', 'monospace']
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'Tajawal', sans-serif
        }

        [x-cloak] {
            display: none !important
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1rem;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 600;
            color: #94a3b8;
            transition: all .18s;
            text-decoration: none;
        }

        .sidebar-link:hover {
            background: #f1f5f9;
            color: #1e293b
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, .35)
        }

        .sidebar-link .icon {
            font-size: 1.1rem;
            width: 1.5rem;
            text-align: center;
            flex-shrink: 0
        }

        .card {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #f1f5f9;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05)
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem 1.25rem;
            background: #4f46e5;
            color: #fff;
            border-radius: 10px;
            font-weight: 700;
            font-size: .875rem;
            border: none;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(79, 70, 229, .35)
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem 1.25rem;
            background: #f8fafc;
            color: #475569;
            border-radius: 10px;
            font-weight: 700;
            font-size: .875rem;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none
        }

        .btn-ghost:hover {
            background: #f1f5f9;
            border-color: #cbd5e1
        }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem 1.25rem;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 10px;
            font-weight: 700;
            font-size: .875rem;
            border: 1.5px solid #fecaca;
            cursor: pointer;
            transition: all .15s
        }

        .btn-danger:hover {
            background: #fee2e2
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: .2rem .65rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8
        }

        .badge-yellow {
            background: #fef9c3;
            color: #a16207
        }

        .badge-red {
            background: #fee2e2;
            color: #dc2626
        }

        .badge-gray {
            background: #f1f5f9;
            color: #475569
        }

        .badge-purple {
            background: #ede9fe;
            color: #7c3aed
        }

        input[type=text],
        input[type=email],
        input[type=number],
        input[type=password],
        select,
        textarea {
            width: 100%;
            padding: .65rem .9rem;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Tajawal', sans-serif;
            font-size: .9rem;
            font-weight: 500;
            color: #1e293b;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
        }

        label {
            display: block;
            font-size: .82rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: .4rem
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 12px;
            border: 1.5px solid #f1f5f9
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        thead tr {
            background: #f8fafc;
            border-bottom: 1.5px solid #f1f5f9
        }

        thead th {
            text-align: right;
            padding: .75rem 1rem;
            font-size: .78rem;
            font-weight: 700;
            color: #64748b;
            letter-spacing: .04em;
            text-transform: uppercase;
            white-space: nowrap
        }

        tbody tr {
            border-bottom: 1px solid #f8fafc;
            transition: background .15s
        }

        tbody tr:hover {
            background: #fafbfc
        }

        tbody td {
            padding: .75rem 1rem;
            font-size: .875rem;
            color: #374151;
            vertical-align: middle
        }

        .flash {
            padding: .75rem 1.1rem;
            border-radius: 12px;
            font-size: .875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .6rem
        }

        .flash-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1.5px solid #bbf7d0
        }

        .flash-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1.5px solid #fecaca
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="flex min-h-screen">

        {{-- ══ SIDEBAR ══ --}}
        <aside
            class="w-60 bg-white border-l border-slate-100 flex flex-col flex-shrink-0 fixed top-0 bottom-0 right-0 z-30"
            style="box-shadow:4px 0 24px rgba(0,0,0,.04)">

            {{-- Logo --}}
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">🎓</div>
                    <div>
                        <p class="font-black text-slate-800 text-sm leading-tight">أكاديمية اللعب</p>
                        <p class="text-xs text-slate-400 font-medium">الذكية</p>
                    </div>
                </div>
            </div>

            {{-- User info --}}
            <div class="p-4 border-b border-slate-100">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-black text-white flex-shrink-0"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 text-sm truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ auth()->user()->getRoleNames()->first() }}</p>
                    </div>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 p-3 overflow-y-auto space-y-1">
                @php $route = request()->route()?->getName() ?? '' @endphp

                <a href="{{ route('dashboard.home') }}"
                    class="sidebar-link {{ str_starts_with($route, 'dashboard.home') ? 'active' : '' }}">
                    <span class="icon">🏠</span> الرئيسية
                </a>
                <a href="{{ route('dashboard.children.index') }}"
                    class="sidebar-link {{ str_starts_with($route, 'dashboard.children') ? 'active' : '' }}">
                    <span class="icon">👦</span> أطفالي
                </a>
                <a href="{{ route('dashboard.notifications') }}"
                    class="sidebar-link {{ str_starts_with($route, 'dashboard.notifications') ? 'active' : '' }}">
                    <span class="icon">🔔</span>
                    الإشعارات
                    @php $unread = auth()->user()->unreadNotifications()->count() @endphp
                    @if ($unread)
                        <span
                            class="mr-auto text-xs font-bold px-2 py-0.5 rounded-full bg-red-500 text-white">{{ $unread }}</span>
                    @endif
                </a>
                <a href="{{ route('dashboard.settings') }}"
                    class="sidebar-link {{ str_starts_with($route, 'dashboard.settings') ? 'active' : '' }}">
                    <span class="icon">⚙️</span> الإعدادات
                </a>

                @if (auth()->user()->hasRole(['admin', 'content_manager']))
                    <div class="pt-3 pb-1">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 px-3">إدارة المنصة</p>
                    </div>
                    <a href="{{ route('admin.home') }}"
                        class="sidebar-link {{ str_starts_with($route, 'admin') ? 'active' : '' }}">
                        <span class="icon">🛡️</span> لوحة الأدمن
                    </a>
                @endif
            </nav>

            {{-- Logout --}}
            <div class="p-3 border-t border-slate-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="sidebar-link w-full text-right hover:text-red-500 hover:bg-red-50">
                        <span class="icon">🚪</span> تسجيل خروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- ══ MAIN ══ --}}
        <div class="flex-1 flex flex-col" style="margin-right:240px">

            {{-- Topbar --}}
            <header class="bg-white border-b border-slate-100 sticky top-0 z-20"
                style="box-shadow:0 1px 4px rgba(0,0,0,.04)">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="font-black text-slate-800 text-lg">@yield('page-title', 'لوحة التحكم')</h1>
                        @hasSection('breadcrumb')
                            <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">@yield('breadcrumb')</div>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @yield('topbar-actions')
                        {{-- Quick notifications bell --}}
                        <a href="{{ route('dashboard.notifications') }}"
                            class="relative w-9 h-9 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-lg hover:bg-slate-100 transition">
                            🔔
                            @if (auth()->user()->unreadNotifications()->count())
                                <span
                                    class="absolute -top-1 -left-1 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center font-bold">
                                    {{ min(9, auth()->user()->unreadNotifications()->count()) }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            <div class="px-6 pt-4 space-y-2">
                @if (session('success'))
                    <div class="flash flash-success">✅ {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="flash flash-error">❌ {{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="flash flash-error">❌ {{ $errors->first() }}</div>
                @endif
            </div>

            {{-- Page content --}}
            <main class="flex-1 p-6">
                @yield('content')
            </main>

            <footer class="px-6 py-3 border-t border-slate-100 text-xs text-slate-400 text-center">
                أكاديمية اللعب الذكية © {{ date('Y') }}
            </footer>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>

</html>
