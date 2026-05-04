<?php
// يعمل في الـ background بعد كل جلسة
// ══════════════════════════════════════════════════════════════════
namespace App\Jobs;

use App\Models\{Child, Achievement};
use App\Models\ParentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckAchievementsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public int $childId) {}

    public function handle(): void
    {
        $child       = Child::with('achievements')->find($this->childId);
        $earnedIds   = $child->achievements->pluck('id')->toArray();
        $achievements = Achievement::where('is_active', true)
            ->whereNotIn('id', $earnedIds)
            ->get();

        foreach ($achievements as $achievement) {
            if ($this->isEarned($child, $achievement)) {
                // منح الإنجاز
                $child->achievements()->attach($achievement->id, [
                    'earned_at' => now(),
                ]);
                $child->increment('total_stars', $achievement->stars_reward);

                // إشعار الأهل
                foreach ($child->parents as $parent) {
                    ParentNotification::create([
                        'user_id'  => $parent->id,
                        'child_id' => $child->id,
                        'type'     => 'achievement',
                        'title'    => "🏆 {$child->name} فاز بإنجاز جديد!",
                        'body'     => "حصل على وسام '{$achievement->name}' 🎉",
                        'data'     => ['achievement_id' => $achievement->id],
                    ]);
                }
            }
        }
    }

    protected function isEarned(Child $child, Achievement $achievement): bool
    {
        $condition = $achievement->condition ?? [];

        return match ($achievement->type) {
            'streak'  => $child->streak_days >= ($condition['streak_days'] ?? 7),
            'mastery' => $child->progress()
                ->where('mastery_score', '>=', 80)
                ->count() >= ($condition['topics_count'] ?? 1),
            'accuracy' => false, // نفعّله لاحقاً
            default   => false,
        };
    }
}
