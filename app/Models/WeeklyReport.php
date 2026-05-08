<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    protected $fillable = [
        'child_id',
        'week_start',
        'week_end',
        'ai_summary',
        'total_sessions',
        'total_minutes',
        'stars_earned',
        'topics_practiced',
        'gaps_detected',
        'real_activities',
        'recommended_daily_minutes',
        'generated_at',
    ];

    protected $casts = [
        'week_start'       => 'date',
        'week_end'         => 'date',
        'gaps_detected'    => 'array',
        'real_activities'  => 'array',
        'generated_at'     => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
