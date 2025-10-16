<?php

namespace App\Console\Commands;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MigrateFreshProfiled extends \Illuminate\Database\Console\Migrations\FreshCommand
{
    protected $name = 'migrate:fresh:profiled';

    protected $description = 'Drop all tables and re-run all migrations with profiling & timeout guard';

    public function handle()
    {
        $threshold = config('migration_profiler.threshold_seconds', 60);
        $forceSeed = (bool) config('migration_profiler.force_seed', false);
        // Some environments (notably Windows/XAMPP) lack the PCNTL extension.
        // A missing pcntl_async_signals() call showed up as a fatal in logs because
        // an unqualified function call resolved into this namespace. Provide a
        // small no-op namespaced stub when the global function is unavailable so
        // callers that reference the unqualified name here won't fatal.
        if (! function_exists('pcntl_async_signals') && ! function_exists(__NAMESPACE__ . '\\pcntl_async_signals')) {
            // define a namespaced no-op via eval to avoid parse-time function declaration issues
            eval('namespace ' . __NAMESPACE__ . '; function pcntl_async_signals($enable) { return false; }');
        }
        $start = microtime(true);
        $output = $this->output;
        $output->writeln('<info>[Profiler]</info> Starting migrate:fresh (threshold ' . $threshold . 's)');

        $lastLog = microtime(true);
        DB::listen(function ($query) use (&$lastLog, $output) {
            $now = microtime(true);
            $warnEach = config('migration_profiler.warn_each_seconds', 3);
            if (($now - $lastLog) >= $warnEach) {
                $lastLog = $now;
                $output->writeln('<comment>[Profiler]</comment> Running... ' . round($now - $_SERVER['__mig_start'], 1) . 's');
            }
        });
        $_SERVER['__mig_start'] = microtime(true);

        try {
            // Performance tweaks (especially for SQLite dev/testing DBs)
            $originalDispatcher = Model::getEventDispatcher();
            Model::unsetEventDispatcher();
            $foreignKeysTemporarilyDisabled = false;
            try {
                Schema::disableForeignKeyConstraints();
                $foreignKeysTemporarilyDisabled = true;
            } catch (\Throwable $e) {
                // ignore if driver not supported
            }
            if (DB::getDriverName() === 'sqlite') {
                try {
                    DB::statement('PRAGMA synchronous = OFF');
                    DB::statement('PRAGMA journal_mode = MEMORY');
                    DB::statement('PRAGMA temp_store = MEMORY');
                    DB::statement('PRAGMA foreign_keys = OFF');
                } catch (\Throwable $e) {
                    // ignore
                }
            }
            // استدع parent بدون --seed حتى لا يتكرر التسييد، ثم ننفذ seed يدوياً مرة واحدة لاحقاً
            $originalSeedOption = $this->option('seed');
            $originalSeeder = $this->option('seeder');
            $this->input->setOption('seed', false);
            $this->input->setOption('seeder', null);
            parent::handle();
            // Re-enable FKs & events
            if ($foreignKeysTemporarilyDisabled) {
                try {
                    Schema::enableForeignKeyConstraints();
                } catch (\Throwable $e) {
                }
            }
            if ($originalDispatcher) {
                Model::setEventDispatcher($originalDispatcher);
            }
            $elapsedAfterMigrations = microtime(true) - $start;
            $output->writeln('<info>[Profiler]</info> migrations done in ' . round($elapsedAfterMigrations, 2) . 's');
            if ($elapsedAfterMigrations > $threshold && ! $forceSeed) {
                $output->writeln('<error>[Profiler] Threshold exceeded before seeding. Aborting seeding.</error>');

                return 1;
            } elseif ($elapsedAfterMigrations > $threshold && $forceSeed) {
                $output->writeln('<comment>[Profiler]</comment> Threshold exceeded (' . round($elapsedAfterMigrations, 2) . 's) but continuing seeding due to force_seed=on');
            }
            if ($originalSeedOption || $originalSeeder) {
                $seedStart = microtime(true);
                $this->call('db:seed', [
                    '--class' => $originalSeeder ?: 'DatabaseSeeder',
                ]);
                $seedElapsed = microtime(true) - $seedStart;
                $output->writeln('<info>[Profiler]</info> seeding done in ' . round($seedElapsed, 2) . 's (total ' . round(microtime(true) - $start, 2) . 's)');
            }
        } catch (Throwable $e) {
            $elapsed = microtime(true) - $start;
            $output->writeln('<error>[Profiler] FAILED after ' . round($elapsed, 2) . 's: ' . $e->getMessage() . '</error>');
            Log::error('[MigrationProfiler] ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($elapsed > $threshold) {
                $output->writeln('<error>[Profiler] Terminating due to time threshold</error>');
            }

            return 1;
        }

        $total = microtime(true) - $start;
        $output->writeln('<info>[Profiler]</info> total time ' . round($total, 2) . 's');

        return $total > $threshold ? 1 : 0;
    }
}
