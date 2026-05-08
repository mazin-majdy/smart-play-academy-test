<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'subject_id',
        'parent_topic_id',
        'name',
        'name_en',
        'description',
        'difficulty_level',
        'age_group',
        'sort_order',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function games()
    {
        return $this->hasMany(Game::class);
    }
    public function parent()
    {
        return $this->belongsTo(Topic::class, 'parent_topic_id');
    }
    public function children()
    {
        return $this->hasMany(Topic::class, 'parent_topic_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('difficulty_level');
    }
}
