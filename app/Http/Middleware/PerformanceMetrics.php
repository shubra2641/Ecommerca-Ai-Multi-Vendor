<?php

namespace App\Http\Middleware;

use App\Support\Performance\PerformanceRecorder;
use Closure;
use Illuminate\Http\Request;

class PerformanceMetrics
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);

        // Record generic HTTP request metric
        PerformanceRecorder::increment('http_requests');
        $elapsed = (microtime(true) - $start) * 1000; // ms
        PerformanceRecorder::timing('http_request', $elapsed);

        // Route-specific markers (coarse)
        if ($request->is('catalog') || $request->is('catalog/*')) {
            PerformanceRecorder::increment('catalog_page');
            PerformanceRecorder::timing('catalog_page', $elapsed);
        } elseif ($request->is('category/*')) {
            PerformanceRecorder::increment('category_page');
            PerformanceRecorder::timing('category_page', $elapsed);
        } elseif ($request->is('tag/*')) {
            PerformanceRecorder::increment('tag_page');
            PerformanceRecorder::timing('tag_page', $elapsed);
        }

        return $response;
    }
}
