<?php
namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Child extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'username', 'password', 'avatar', 'avatar_color',
        'birthdate', 'age_group', 'preferred_language', 'learning_style',
        'total_stars', 'total_xp', 'current_level', 'streak_days',
        'last_play_date', 'daily_limit_minutes', 'is_active',
    ];

    
    protected $hidden = ['password'];

    protected $casts = [
        'birthdate'       => 'date',
        'last_play_date'  => 'date',
        'is_active'       => 'boolean',
    ];

    // ── RELATIONS ──
    public function parents()
    {
        return $this->belongsToMany(User::class, 'child_user')
                    ->wherePivot('relation', 'parent')
                    ->withPivot('is_primary');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'child_user')
                    ->wherePivot('relation', 'teacher');
    }

    public function progress()
    {
        return $this->hasMany(ChildProgress::class);
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'child_achievements')
                    ->withPivot('earned_at');
    }

    public function weeklyReports()
    {
        return $this->hasMany(WeeklyReport::class);
    }

    // ── HELPERS ──

    // حساب العمر تلقائياً
    public function getAgeAttribute(): ?int
    {
        return $this->birthdate?->age;
    }

    // الوقت المستخدم اليوم بالدقائق
    public function getTodayPlayMinutes(): int
    {
        return (int) $this->gameSessions()
            ->whereDate('started_at', today())
            ->where('status', 'completed')
            ->sum('duration_seconds') / 60;
    }

    // هل وصل الحد اليومي؟
    public function hasReachedDailyLimit(): bool
    {
        return $this->getTodayPlayMinutes() >= $this->daily_limit_minutes;
    }

    // تحديث الـ streak
    public function updateStreak(): void
    {
        $today     = today();
        $lastPlay  = $this->last_play_date;

        if ($lastPlay === null || $lastPlay->diffInDays($today) > 1) {
            // انقطع الـ streak
            $this->update(['streak_days' => 1, 'last_play_date' => $today]);
        } elseif ($lastPlay->isYesterday()) {
            // يوم جديد بالتسلسل
            $this->increment('streak_days');
            $this->update(['last_play_date' => $today]);
        }
        // لو نفس اليوم — ما نغيّر شي
    }

    // إضافة XP + level up تلقائياً
    public function addXp(int $amount): void
    {
        $this->increment('total_xp', $amount);
        $newLevel = (int) (sqrt($this->total_xp / 100)) + 1;
        if ($newLevel > $this->current_level) {
            $this->update(['current_level' => $newLevel]);
        }
    }
}
