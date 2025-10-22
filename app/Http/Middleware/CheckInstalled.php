<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstalled
{
    /**
     * If the application is not yet installed (no storage/app/installed file),
     * redirect all requests to the installer except requests for installer routes
     * and public assets.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow access to installer routes, assets and typical public files
        if (
            $request->is('install*')
            || $request->is('storage/*')
            || $request->is('vendor/*')
            || $request->is('admin/install*')
            || $request->is('manifest.webmanifest')
            || $request->is('offline')
            || $request->is('favicon.ico')
        ) {
            return $next($request);
        }

        $installedFile = storage_path('app/installed');

        if (file_exists($installedFile)) {
            return $next($request);
        }

        // Not installed yet: redirect to installer welcome
        return redirect()->route('install.welcome');
    }
}
