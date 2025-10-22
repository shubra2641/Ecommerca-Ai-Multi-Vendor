<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:vendor,user')
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        if (! $request->user()) {
            // If unauthenticated, redirect to login (auth middleware should normally handle this)
            return redirect()->guest(route('login'));
        }

        $allowed = [];
        if ($roles) {
            $allowed = array_map('trim', explode(',', $roles));
        }

        // If no roles provided, allow authenticated only
        if (empty($allowed)) {
            return $next($request);
        }

        $role = $request->user()->role ?? null;
        if (! $role || ! in_array($role, $allowed)) {
            // Render a friendly 403 page for forbidden access
            return response()->view('errors.403-role', ['user' => $request->user()], 403);
        }

        return $next($request);
    }
}
