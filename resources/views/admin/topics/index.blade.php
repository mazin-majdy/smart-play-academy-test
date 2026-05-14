@extends('layouts.app')
@section('title', 'إدارة الأقسام')
@section('page-title', 'إدارة الأقسام التعليمية')

@section('topbar-actions')
    <a href="{{ route('admin.topics.create') }}" class="btn-primary">+ إضافة قسم جديد</a>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-slate-50 text-slate-600 text-sm">
                        <tr>
                            <th class="px-6 py-4 font-bold">اسم القسم</th>
                            <th class="px-6 py-4 font-bold">المادة التابعة</th>
                            <th class="px-6 py-4 font-bold text-center">عدد الألعاب</th>
                            <th class="px-6 py-4 font-bold text-center">عدد الأسئلة</th>
                            <th class="px-6 py-4 font-bold text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topics as $topic)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $topic->name }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $topic->subject->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full text-xs font-bold">
                                        {{ $topic->games_count ?? $topic->games()->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-yellow-50 text-yellow-600 px-2.5 py-1 rounded-full text-xs font-bold">
                                        {{ $topic->questions_count ?? $topic->questions()->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.topics.show', $topic) }}" class="btn-ghost text-xs">️
                                            عرض</a>
                                        <a href="{{ route('admin.topics.edit', $topic) }}" class="btn-ghost text-xs">✏️
                                            تعديل</a>
                                        <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟ سيتم حذف الألعاب والأسئلة المرتبطة به.')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost text-xs text-red-500 hover:bg-red-50">🗑️ حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="text-4xl mb-3">📂</div>
                                    <p class="font-medium text-slate-600">لا توجد أقسام تعليمية حالياً</p>
                                    <a href="{{ route('admin.topics.create') }}"
                                        class="text-indigo-500 text-sm font-bold mt-2 inline-block hover:underline">أضف أول
                                        قسم ←</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if (method_exists($topics, 'links'))
            <div class="mt-4 flex justify-end">
                {{ $topics->links() }}
            </div>
        @endif
    </div>
@endsection
