<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('vendor')
                ->group(base_path('routes/vendor.php'));

            // Load API routes under the 'api' middleware and '/api' prefix so
            // routes defined in routes/api.php are registered and are exempt
            // from the web (CSRF) middleware.
            if (file_exists(base_path('routes/api.php'))) {
                \Illuminate\Support\Facades\Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));
            }
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\Localization::class,
            \App\Http\Middleware\ContentSecurityPolicy::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
