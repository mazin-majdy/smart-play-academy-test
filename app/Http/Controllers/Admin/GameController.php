<?php
// إدارة الألعاب من لوحة الأدمن
// ══════════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Game, Topic, Subject};
use App\Jobs\GenerateQuestionsJob;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with(['topic.subject'])
            ->latest()
            ->paginate(20);
        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        $subjects = Subject::with(['topics'])->get();
        return view('admin.games.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'topic_id'     => 'required|exists:topics,id',
            'title'        => 'required|string|max:100',
            'game_type'    => 'required|in:drag_drop,visual_match,math_puzzle,logic_chain,word_challenge,quiz,story_interactive,timed_challenge,strategy_game,science_sim,block_code,virtual_lab,sound_guess',
            'age_group'    => 'required|in:6-8,9-11,12-14',
            'difficulty'   => 'required|integer|between:1,5',
            'stars_reward' => 'integer|min:1',
            'xp_reward'    => 'integer|min:1',
            'ai_generated' => 'boolean',
            'config'       => 'nullable|json',
        ]);

        $game = Game::create($data);

        // إذا اخترنا AI Generated، نولّد أسئلة في الـ background
        if ($request->boolean('ai_generated')) {
            GenerateQuestionsJob::dispatch($game->id, 15);
        }

        return redirect()->route('admin.games.show', $game)
            ->with('success', 'تمت إضافة اللعبة وجاري توليد الأسئلة...');
    }

    public function show(Game $game)
    {
        $game->load('topic.subject', 'questions');
        $stats = [
            'total_questions'  => $game->questions->count(),
            'total_sessions'   => $game->sessions()->count(),
            'avg_score'        => round($game->sessions()->avg('score') ?? 0),
            'avg_accuracy'     => round(
                $game->sessions()->selectRaw('AVG(correct_count/(correct_count+wrong_count)*100) as acc')->value('acc') ?? 0
            ),
        ];
        return view('admin.games.show', compact('game', 'stats'));
    }

    // POST /admin/games/{game}/generate-questions
    public function generateMoreQuestions(Game $game)
    {
        GenerateQuestionsJob::dispatch($game->id, 10);
        return back()->with('success', 'جاري توليد 10 أسئلة جديدة...');
    }
}
