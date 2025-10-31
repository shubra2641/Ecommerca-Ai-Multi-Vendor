<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware applied to all HTTP requests (web + api)
        $middleware->use([
            \App\Http\Middleware\CheckInstallationMode::class,
        ]);

        // Web group middleware (order preserved from legacy Http Kernel)
        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\SanitizeInput::class,
            \App\Http\Middleware\ContentSecurityPolicy::class,
            \App\Http\Middleware\Localization::class,
            \App\Http\Middleware\DemoModeMiddleware::class,
        ]);

        // API group middleware
        $middleware->appendToGroup('api', [
            'throttle:api',
            \App\Http\Middleware\DemoModeMiddleware::class,
        ]);

        // Route middleware aliases migrated from Http Kernel
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'activated' => \App\Http\Middleware\EnsureEmailActivated::class,
            'payment.security' => \App\Http\Middleware\PaymentSecurityMiddleware::class,
            'sanitize' => \App\Http\Middleware\SanitizeInput::class,
            'demo' => \App\Http\Middleware\DemoModeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
