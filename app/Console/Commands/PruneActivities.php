<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneActivities extends Command
{
    protected $signature = 'activity:prune {--days= : Number of days to keep (overrides config/env) }';

    protected $description = 'Prune activity records older than a configured number of days';

    public function handle()
    {
        $days = $this->option('days') ?? config('activity.prune_days', env('ACTIVITY_PRUNE_DAYS', 30));
        $days = (int) $days ?: 30;
        $threshold = Carbon::now()->subDays($days);

        $count = DB::table('activities')->where('created_at', '<', $threshold)->count();
        if ($count === 0) {
            $this->info("No activities older than {$days} days to prune.");

            return 0;
        }

        DB::table('activities')->where('created_at', '<', $threshold)->delete();
        $this->info("Pruned {$count} activity records older than {$days} days.");

        return 0;
    }
}
