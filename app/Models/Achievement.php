<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
        'type',
        'condition',
        'stars_reward',
        'is_active',
    ];

    protected $casts = [
        'condition'  => 'array',
        'is_active'  => 'boolean',
    ];

    public function children()
    {
        return $this->belongsToMany(Child::class, 'child_achievements')
            ->withPivot('earned_at');
    }
}
