<?php
// يشتغل كل ليلة للتحقق من انقطاع الـ streak
// ══════════════════════════════════════════════════════════════════
namespace App\Console\Commands;

use App\Models\Child;
use App\Jobs\NotifyParentJob;
use Illuminate\Console\Command;

class CheckStreaks extends Command
{
    protected $signature   = 'streaks:check';
    protected $description = 'Check for broken streaks and notify parents';

    public function handle(): void
    {
        // الأطفال الذين لم يلعبوا أمس
        $inactive = Child::where('is_active', true)
            ->where('streak_days', '>', 0)
            ->where(function ($q) {
                $q->whereNull('last_play_date')
                    ->orWhere('last_play_date', '<', today()->subDay());
            })
            ->get();

        foreach ($inactive as $child) {
            // إعادة تصفير الـ streak
            $child->update(['streak_days' => 0]);
            NotifyParentJob::dispatch($child, 'streak_broken');
        }

        $this->info("Checked streaks for {$inactive->count()} children.");
    }
}
