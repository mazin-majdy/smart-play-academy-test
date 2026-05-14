<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Topic, Subject};
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics   = Topic::with('subject')->orderBy('subject_id')->orderBy('difficulty_level')->paginate(30);
        $subjects = Subject::active()->get();
        return view('admin.topics.index', compact('topics', 'subjects'));
    }

    // ✅ المضافة: عرض صفحة الإضافة
    public function create()
    {
        $subjects = Subject::active()->pluck('name', 'id');
        return view('admin.topics.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'name'             => 'required|string|max:100',
            'difficulty_level' => 'required|integer|between:1,5',
            'age_group'        => 'required|in:6-8,9-11,12-14',
            'sort_order'       => 'integer|min:0',
        ]);
        Topic::create($data);
        return back()->with('success', 'تمت إضافة الموضوع');
    }

    // ✅ المضافة: عرض صفحة التفاصيل
    public function show(Topic $topic)
    {
        $topic->load('subject', 'games'); // تحميل العلاقات اللازمة للعرض
        return view('admin.topics.show', compact('topic'));
    }

    // ✅ المضافة: عرض صفحة التعديل
    public function edit(Topic $topic)
    {
        $subjects = Subject::active()->pluck('name', 'id');
        return view('admin.topics.edit', compact('topic', 'subjects'));
    }

    public function update(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'difficulty_level' => 'required|integer|between:1,5',
            'is_active'        => 'boolean',
        ]);
        $topic->update($data);
        return back()->with('success', 'تم تحديث الموضوع');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return back()->with('success', 'تم حذف الموضوع');
    }
}
