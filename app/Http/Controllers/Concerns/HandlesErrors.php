<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Reusable helpers for consistent API error responses & logging.
 * Keep very small so it can be safely mixed into many controllers.
 */
trait HandlesErrors
{
    /**
     * Build a standardized JSON error response structure.
     *
     * @param  string  $message  Human friendly message (already translated if needed)
     * @param  int  $status  HTTP status code
     * @param  array|null  $context  Optional extra context (safe for client)
     */
    protected function errorResponse(string $message, int $status = 422, ?array $context = null): JsonResponse
    {
        return response()->json([
            'ok' => false,
            'error' => [
                'message' => $message,
                'code' => $status,
                'context' => $context,
            ],
        ], $status);
    }

    /**
     * Try a callback and convert any throwable into an error response.
     * Narrow usage for small inline operations (e.g. AJAX endpoints).
     */
    protected function guard(callable $cb, string $fallbackMessage = 'Unexpected error', int $status = 500)
    {
        try {
            return $cb();
        } catch (\Throwable $e) {
            Log::warning('Guarded controller error: ' . $e->getMessage(), [
                'trace_hash' => substr(hash('sha256', $e->getFile() . $e->getLine()), 0, 12),
            ]);

            return $this->errorResponse(__($fallbackMessage), $status);
        }
    }
}
