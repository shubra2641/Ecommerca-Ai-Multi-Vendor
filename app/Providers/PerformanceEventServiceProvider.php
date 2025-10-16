<?php

namespace App\Providers;

use App\Support\Performance\PerformanceRecorder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class PerformanceEventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! config('performance.enabled')) {
            return;
        }

        // Skip during migrations (cache tables may not exist and we don't want overhead)
        if ($this->runningMaintenanceCommand()) {
            return;
        }

        // If database cache driver but cache table missing, skip to avoid query explosion
        if (config('cache.default') === 'database' && ! Schema::hasTable(config('cache.stores.database.table', 'cache'))) {
            return;
        }

        // DB query listener (aggregate total queries & cumulative time ms)
        DB::listen(function ($query) {
            PerformanceRecorder::increment('db_queries');
            if (isset($query->time)) {
                PerformanceRecorder::timing('db_query', (float) $query->time);
            }
        });

        // Queue job processing times
        Queue::before(function ($event) {
            if (method_exists($event->job, 'uuid')) {
                cache()->put('job:start:' . $event->job->uuid(), microtime(true), now()->addMinutes(5));
            }
        });
        Queue::after(function ($event) {
            $start = 0;
            if (method_exists($event->job, 'uuid')) {
                $start = cache()->pull('job:start:' . $event->job->uuid(), 0);
            }
            if ($start) {
                $elapsed = (microtime(true) - $start) * 1000;
                PerformanceRecorder::increment('queue_jobs');
                PerformanceRecorder::timing('queue_job', $elapsed);
            }
        });
        Queue::failing(function ($event) {
            PerformanceRecorder::increment('queue_failed');
        });
    }

    private function runningMaintenanceCommand(): bool
    {
        if (! app()->runningInConsole()) {
            return false;
        }
        $argv = implode(' ', $_SERVER['argv'] ?? []);

        return preg_match('/\b(migrate|db:seed)(:(fresh|install|refresh|reset|rollback))?/i', $argv) === 1;
    }
}
