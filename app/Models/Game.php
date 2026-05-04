<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'topic_id',
        'title',
        'title_en',
        'description',
        'game_type',
        'age_group',
        'config',
        'difficulty',
        'stars_reward',
        'xp_reward',
        'ai_generated',
        'is_active',
    ];

    protected $casts = [
        'config'       => 'array',
        'ai_generated' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function sessions()
    {
        return $this->hasMany(GameSession::class);
    }

    // أسئلة حسب الصعوبة
    public function questionsForDifficulty(int $level)
    {
        return $this->questions()
            ->where('difficulty', $level)
            ->where('is_active', true)
            ->inRandomOrder();
    }
}
