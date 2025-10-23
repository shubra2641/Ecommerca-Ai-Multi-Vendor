<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// If the application is running in the console, register our scheduled pruning task.
if (app()->runningInConsole()) {
    app()->booted(function (): void {
        app(Schedule::class);
    });
}

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
