<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Support\Performance\PerformanceRecorder;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    private array $defaultMetrics = [
        'http_requests',
        'catalog_page',
        'category_page',
        'tag_page',
        'push_sent',
    ];

    public function index(Request $request)
    {
        $metrics = $request->get('metrics');
        $metrics = $metrics ? array_filter(explode(',', $metrics)) : $this->defaultMetrics;
        $snapshot = PerformanceRecorder::snapshot($metrics);

        return view('vendor.performance.index', compact('snapshot', 'metrics'));
    }

    public function apiSnapshot(Request $request)
    {
        $metrics = $request->get('metrics');
        $metrics = $metrics ? array_filter(explode(',', $metrics)) : $this->defaultMetrics;

        return response()->json([
            'success' => true,
            'data' => PerformanceRecorder::snapshot($metrics),
            'generated_at' => now()->toISOString(),
        ]);
    }
}
