<?php
// أمر يشتغل كل أحد تلقائياً (Scheduled Command)
// ══════════════════════════════════════════════════════════════════
namespace App\Console\Commands;

use App\Models\Child;
use App\Jobs\GenerateWeeklyReportJob;
use Illuminate\Console\Command;

class GenerateWeeklyReports extends Command
{
    protected $signature   = 'reports:weekly';
    protected $description = 'Generate weekly AI reports for all children';

    public function handle(): void
    {
        $children = Child::where('is_active', true)->get();
        $count    = 0;

        foreach ($children as $child) {
            GenerateWeeklyReportJob::dispatch($child->id);
            $count++;
        }

        $this->info("Dispatched weekly report jobs for {$count} children.");
    }
}
