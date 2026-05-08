@extends('layouts.app')
@section('title', 'المستخدمون')
@section('page-title', 'إدارة المستخدمين')

@section('content')
    <div class="space-y-5">

        {{-- Filters --}}
        <form class="card p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-40">
                <label>بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم أو البريد...">
            </div>
            <div class="w-40">
                <label>الدور</label>
                <select name="role">
                    <option value="">الكل</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                            {{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label>الحالة</label>
                <select name="status">
                    <option value="">الكل</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>موقوف</option>
                </select>
            </div>
            <button class="btn-primary">بحث</button>
            <a href="{{ route('admin.users') }}" class="btn-ghost">إعادة تعيين</a>
        </form>

        {{-- Table --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-black text-slate-700">المستخدمون ({{ $users->total() }})</h3>
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
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-white text-sm flex-shrink-0"
                                            style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php $role = $user->getRoleNames()->first() ?? '-' @endphp
                                    <form action="{{ route('admin.users.role', $user) }}" method="POST"
                                        class="flex gap-1">
                                        @csrf @method('PATCH')
                                        <select name="role" class="text-xs py-1 px-2 rounded-lg border border-slate-200"
                                            style="width:auto">
                                            @foreach ($roles as $r)
                                                <option value="{{ $r->name }}" {{ $role === $r->name ? 'selected' : '' }}>
                                                    {{ $r->name }}</option>
                                            @endforeach
                                        </select>
                                        <button
                                            class="text-xs text-indigo-500 hover:text-indigo-700 font-bold px-1">✓</button>
                                    </form>
                                </td>
                                <td class="font-bold text-center">{{ $user->children_count }}</td>
                                <td>
                                    <span class="badge {{ $user->status === 'active' ? 'badge-green' : 'badge-red' }}">
                                        {{ $user->status === 'active' ? 'نشط' : 'موقوف' }}
                                    </span>
                                </td>
                                <td class="text-xs text-slate-400">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="btn-ghost text-xs py-1 px-2">عرض</a>
                                        @if ($user->status === 'active')
                                            <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button class="btn-danger text-xs py-1 px-2">تعليق</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button
                                                    class="text-xs py-1 px-2 rounded-lg bg-green-50 text-green-700 border border-green-200 font-bold hover:bg-green-100 transition">تفعيل</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400">لا توجد نتائج</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-100">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
