<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /** @var array */
    protected $middleware = [
        \App\Http\Middleware\CheckInstalled::class,
        // Global HTTP middleware stack
        \App\Http\Middleware\CheckMaintenanceMode::class,
        \App\Http\Middleware\ContentSecurityPolicy::class,
        \App\Http\Middleware\Localization::class,
        \App\Http\Middleware\CurrencyDetection::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            // Laravel default web group is normally in parent; assuming sessions, cookies, etc auto-handled
            \App\Http\Middleware\SanitizeInput::class,
        ],
        'api' => [
            'throttle:api',
        ],
    ];

    protected $routeMiddleware = [
        'role' => \App\Http\Middleware\EnsureRole::class,
        'activated' => \App\Http\Middleware\EnsureEmailActivated::class,
        'payment.security' => \App\Http\Middleware\PaymentSecurityMiddleware::class,
        'sanitize' => \App\Http\Middleware\SanitizeInput::class,
    ];
}
