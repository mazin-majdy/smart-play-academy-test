<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'game_id',
        'topic_id',
        'content',
        'content_type',
        'media_path',
        'answers',
        'answer_type',
        'difficulty',
        'explanation',
        'hint',
        'ai_generated',
        'ai_model',
        'times_used',
        'success_rate',
        'is_active',
    ];

    protected $casts = [
        'answers'      => 'array',
        'ai_generated' => 'boolean',
        'is_active'    => 'boolean',
        'success_rate' => 'decimal:2',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // الإجابة الصحيحة
    public function getCorrectAnswer(): array
    {
        return collect($this->answers)->firstWhere('is_correct', true) ?? [];
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeForLevel($q, int $level)
    {
        return $q->where('difficulty', $level);
    }
}
