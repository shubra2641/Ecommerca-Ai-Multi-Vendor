<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallationMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    // Check if installed using storage marker only (authoritative)
    // Storage::put('.installed') writes to storage/app/.installed by default on local driver
    $installed = file_exists(storage_path('app/.installed'))
        || file_exists(storage_path('.installed')); // fallback if some envs wrote directly to storage/.installed

        // If not installed, redirect to install
        if (!$installed) {
            // Allow access to install routes
            if ($request->is('install*')) {
                return $next($request);
            }
            return redirect()->route('install.welcome');
        }

        // If installed, prevent access to install routes except completion page (to show success)
        if ($request->is('install*')) {
            if ($request->is('install/complete') && $request->isMethod('get')) {
                return $next($request);
            }
            return redirect()->route('home');
        }

        return $next($request);
    }
}
