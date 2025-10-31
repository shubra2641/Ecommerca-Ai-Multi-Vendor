<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Setting;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure the application is installed before any other logic.
        // Installation marker is written by the installer to storage/app/.installed
        $root = dirname(__DIR__, 3);
        $installed = file_exists($root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '.installed')
            || file_exists($root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . '.installed'); // fallback for some environments

        if (! $installed) {
            // Allow installer routes and public storage assets to load
            if ($request->is('install*') || $request->is('storage/*')) {
                return $next($request);
            }

            // Redirect everything else to the installer welcome page
            return Redirect::route('install.welcome');
        }

        // Skip for admin area and asset calls
        if ($request->is('admin/*') || $request->is('api/*') || $request->is('storage/*')) {
            return $next($request);
        }

        $setting = Cache::remember('maintenance_settings', 300, function () {
            return Setting::first();
        });

        if (! $setting) {
            return $next($request);
        }

        if ($setting->maintenance_enabled) {
            // allow admin users to access admin pages (already skipped) and allow admin login
            // if a reopen timestamp is set, and now >= reopen_at, disable maintenance
            if (
                $setting->maintenance_reopen_at &&
                Carbon::now()->greaterThanOrEqualTo($setting->maintenance_reopen_at)
            ) {
                // Auto-disable maintenance at reopen time
                $setting->maintenance_enabled = false;
                $setting->save();

                return $next($request);
            }

            // Show maintenance page for public requests
            $locale = App::getLocale();
            $messages = is_array($setting->maintenance_message)
                ? $setting->maintenance_message
                : (json_decode($setting->maintenance_message, true) ? json_decode($setting->maintenance_message, true) : []);
            $message = $messages[$locale] ?? $messages['en'] ??
                Lang::get('The site is under maintenance. Please check back later.');

            return Response::view('errors.maintenance', [
                'message' => $message,
                'reopen_at' => $setting->maintenance_reopen_at,
            ], 503);
        }

        return $next($request);
    }
}
