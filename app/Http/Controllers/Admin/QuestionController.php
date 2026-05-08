<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Question, Game, Topic};
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['game', 'topic'])->latest();
        if ($request->filled('game_id'))   $query->where('game_id',   $request->game_id);
        if ($request->filled('difficulty')) $query->where('difficulty', $request->difficulty);
        if ($request->boolean('ai_only'))  $query->where('ai_generated', true);

        $questions = $query->paginate(25)->withQueryString();
        $games     = Game::with('topic')->get();
        return view('admin.questions.index', compact('questions', 'games'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'game_id'     => 'required|exists:games,id',
            'content'     => 'required|string',
            'difficulty'  => 'required|integer|between:1,5',
            'answer_type' => 'required|in:single_choice,multiple_choice,drag_drop,fill_blank,order_steps,matching',
            'answers'     => 'required|array|min:2',
            'answers.*.text'       => 'required|string',
            'answers.*.is_correct' => 'boolean',
            'explanation' => 'nullable|string',
            'hint'        => 'nullable|string',
        ]);

        // أضف ID لكل إجابة
        $data['answers'] = collect($data['answers'])
            ->map(fn($a, $i) => array_merge($a, ['id' => $i + 1]))
            ->values()
            ->toArray();

        $game = Game::find($data['game_id']);
        $data['topic_id'] = $game->topic_id;

        Question::create($data);
        return back()->with('success', 'تمت إضافة السؤال');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('success', 'تم حذف السؤال');
    }

    public function toggle(Question $question)
    {
        $question->update(['is_active' => !$question->is_active]);
        return back()->with('success', $question->is_active ? 'السؤال نشط' : 'السؤال مخفي');
    }
}
