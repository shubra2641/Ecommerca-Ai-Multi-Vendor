<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailActivated
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && empty($user->email_verified_at)) {
            // logout the user to prevent lingering authenticated session
            Auth::logout();

            // if AJAX/json request, return JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Account not activated'], 403);
            }

            return response()->view('account_not_activated');
        }

        return $next($request);
    }
}
