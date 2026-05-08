<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentNotification extends Model
{
    protected $fillable = [
        'user_id',
        'child_id',
        'type',
        'title',
        'body',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data'     => 'array',
        'is_read'  => 'boolean',
        'read_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function markRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }
}
