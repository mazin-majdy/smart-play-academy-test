<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        // 1️⃣ جلب المواد مع عدد المواضيع
        $subjects = Subject::withCount('topics')->orderBy('sort_order')->get();

        // 2️⃣ جلب المواضيع مع العلاقات اللازمة للعرض ✅ هذا اللي كان ناقص
        $topics = \App\Models\Topic::with('subject')
            ->orderBy('sort_order', 'asc')
            ->orderBy('difficulty_level', 'asc')
            ->paginate(10); // أو 15 أو 30 حسب رغبتك

        // 3️⃣ تمرير المتغيرين للفيو
        return view('admin.subjects.index', compact('subjects', 'topics'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:60',
            'name_en'    => 'nullable|string|max:60',
            'icon'       => 'required|string|max:10',
            'color'      => 'required|string|max:10',
            'age_groups' => 'required|in:all,6-8,9-11,12-14',
            'sort_order' => 'integer|min:0',
        ]);
        Subject::create($data);
        return back()->with('success', 'تمت إضافة المادة');
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:60',
            'icon'       => 'required|string|max:10',
            'color'      => 'required|string|max:10',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);
        $subject->update($data);
        return back()->with('success', 'تم تحديث المادة');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->topics()->count()) {
            return back()->with('error', 'لا يمكن حذف مادة تحتوي على مواضيع');
        }
        $subject->delete();
        return back()->with('success', 'تم حذف المادة');
    }
}
