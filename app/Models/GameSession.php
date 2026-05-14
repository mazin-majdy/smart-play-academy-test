<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $fillable = [
        'child_id',
        'game_id',
        'topic_id',
        'status',
        'difficulty_used',
        'score',
        'stars_earned',
        'xp_earned',
        'correct_count',
        'wrong_count',
        'hints_used',
        'duration_seconds',
        'started_at',
        'ended_at',
        'engagement_data',
        'answers_log',
        'difficulty_adjusted',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'engagement_data'  => 'array',
        'answers_log'      => 'array',
        'difficulty_adjusted' => 'boolean',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
    // داخل كلاس GameSession
    public function answers()
    {
        return $this->hasMany(GameSessionAnswer::class);
    }

    // Helper: حساب دقة الإجابات في الجلسة
    public function getAccuracyAttribute()
    {
        $total = $this->answers()->count();
        if ($total === 0) return 0;

        $correct = $this->answers()->where('is_correct', true)->count();
        return round(($correct / $total) * 100);
    }

    // إنهاء الجلسة وحساب المكافآت
    public function complete(array $data): void
    {
        $this->update([
            'status'        => 'completed',
            'ended_at'      => now(),
            'duration_seconds' => now()->diffInSeconds($this->started_at),
            ...$data,
        ]);

        // نحدّث الـ child
        $this->child->addXp($this->xp_earned);
        $this->child->increment('total_stars', $this->stars_earned);
        $this->child->updateStreak();
    }
}
