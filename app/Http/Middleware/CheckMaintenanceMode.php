<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
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
            if ($setting->maintenance_reopen_at && Carbon::now()->greaterThanOrEqualTo($setting->maintenance_reopen_at)) {
                // Auto-disable maintenance at reopen time
                $setting->maintenance_enabled = false;
                $setting->save();

                return $next($request);
            }

            // Show maintenance page for public requests
            $locale = app()->getLocale();
            $messages = is_array($setting->maintenance_message) ? $setting->maintenance_message : (@json_decode($setting->maintenance_message, true) ?: []);
            $message = $messages[$locale] ?? $messages['en'] ?? __('The site is under maintenance. Please check back later.');

            return response()->view('errors.maintenance', [
                'message' => $message,
                'reopen_at' => $setting->maintenance_reopen_at,
            ], 503);
        }

        return $next($request);
    }
}
