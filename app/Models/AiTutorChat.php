<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiTutorChat extends Model
{
    protected $fillable = [
        'child_id',
        'game_session_id',
        'topic_id',
        'messages',
        'message_count',
        'tokens_used',
        'ai_model',
    ];

    protected $casts = ['messages' => 'array'];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    public function session()
    {
        return $this->belongsTo(GameSession::class, 'game_session_id');
    }
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
