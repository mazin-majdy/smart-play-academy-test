<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('topics')->orderBy('sort_order')->get();
        return view('admin.subjects.index', compact('subjects'));
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
