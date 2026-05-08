<?php

// ══════════════════════════════════════════════════════════════════
// app/Jobs/GenerateQuestionsJob.php
// يشتغل في الـ background لتوليد أسئلة جديدة
// ══════════════════════════════════════════════════════════════════
namespace App\Jobs;

use App\Models\Game;
use App\Services\ContentGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateQuestionsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public int $gameId,
        public int $count = 10,
    ) {}

    public function handle(ContentGeneratorService $generator): void
    {
        $game = Game::find($this->gameId);
        if (!$game) return;

        // نولّد أسئلة جديدة فقط إذا عدد الأسئلة الحالية قليل
        $existing = $game->questions()
            ->where('difficulty', $game->difficulty)
            ->where('is_active', true)
            ->count();

        if ($existing < 20) {
            $generator->generateQuestionsForGame($game, $this->count);
        }
    }
}
