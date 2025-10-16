<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class PaymentSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting for payment endpoints
        $key = 'payment-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            Log::warning('Too many payment attempts', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json([
                'error' => 'Too many payment attempts. Please try again later.',
            ], 429);
        }

        RateLimiter::hit($key, 300); // 5 minutes

        // Validate CSRF token for non-webhook requests
        if (! $this->isWebhookRequest($request)) {
            $this->validateCsrfToken($request);
        }

        // Log payment requests for security monitoring
        Log::info('Payment request', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
        ]);

        return $next($request);
    }

    /**
     * Check if the request is a webhook request.
     */
    private function isWebhookRequest(Request $request): bool
    {
        return str_contains($request->path(), 'webhook') ||
               str_contains($request->path(), 'callback');
    }

    /**
     * Validate CSRF token for payment requests.
     */
    private function validateCsrfToken(Request $request): void
    {
        if ($request->isMethod('POST') && ! $request->hasValidSignature()) {
            $token = $request->header('X-CSRF-TOKEN') ?: $request->input('_token');

            if (! $token || ! hash_equals(session()->token(), $token)) {
                Log::warning('Invalid CSRF token in payment request', [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);

                abort(419, 'CSRF token mismatch');
            }
        }
    }
}
