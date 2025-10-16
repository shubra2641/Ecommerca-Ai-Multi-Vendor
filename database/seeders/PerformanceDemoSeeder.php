<?php

namespace Database\Seeders;

use App\Support\Performance\PerformanceRecorder;
use Illuminate\Database\Seeder;

class PerformanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Simulate some metric noise for demo (idempotent effect limited by minute keys)
        for ($i = 0; $i < 5; $i++) {
            PerformanceRecorder::increment('http_requests', random_int(5, 15));
            PerformanceRecorder::increment('catalog_page', random_int(1, 5));
            PerformanceRecorder::increment('category_page', random_int(1, 3));
            PerformanceRecorder::increment('tag_page', random_int(0, 2));
            PerformanceRecorder::timing('http_request', random_int(80, 250));
        }
    }
}
