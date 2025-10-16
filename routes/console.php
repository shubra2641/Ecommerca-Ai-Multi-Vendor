<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// If the application is running in the console, register our scheduled pruning task.
if (app()->runningInConsole()) {
    app()->booted(function () {
        $schedule = app(Schedule::class);
        $schedule->command('activity:prune')->daily();
    });
}

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
