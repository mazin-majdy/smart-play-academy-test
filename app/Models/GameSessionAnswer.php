<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameSessionAnswer extends Model
{
    protected $fillable = [
        'game_session_id',
        'question_id',
        'child_id',
        'given_answer',
        'correct_answer',
        'is_correct',
        'time_taken_seconds',
        'points_earned',
        'difficulty_level',
        'metadata',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'metadata' => 'array',
        'answered_at' => 'datetime',
    ];

    // ✅ العلاقات
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}
