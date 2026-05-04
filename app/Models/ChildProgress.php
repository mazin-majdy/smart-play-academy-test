<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildProgress extends Model
{
    protected $fillable = [
        'child_id',
        'topic_id',
        'mastery_score',
        'current_difficulty',
        'sessions_count',
        'correct_answers',
        'wrong_answers',
        'total_time_seconds',
        'hints_used',
        'performance_data',
        'last_played_at',
        'completed_at',
    ];

    protected $casts = [
        'performance_data' => 'array',
        'last_played_at'   => 'datetime',
        'completed_at'     => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // هل أتقن الطفل هذا الموضوع؟ (80%+)
    public function isMastered(): bool
    {
        return $this->mastery_score >= 80;
    }
}
