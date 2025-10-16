<?php

namespace App\Support\Performance;

use Illuminate\Support\Facades\Cache;

class PerformanceRecorder
{
    public static function increment(string $metric, int $amount = 1): void
    {
        if (! config('performance.enabled')) {
            return;
        }
        if (self::isMigrationContext()) {
            return; // avoid overhead during migrations
        }
        $cache = cache();
        if (app()->environment('testing')) {
            $cache = Cache::store('array');
        }
        $key = self::minuteKey($metric);
        // If cache driver is array or file skip lock complexity
        $driver = config('cache.default');
        if (app()->environment('testing') || in_array($driver, ['array', 'file'])) {
            $current = $cache->get($key, 0);
            $cache->put($key, $current + $amount, now()->addMinutes(config('performance.raw_ttl', 10)));

            return;
        }
        try {
            Cache::lock($key . ':lock', 5)->block(1, function () use ($key, $amount, $cache) {
                $current = $cache->get($key, 0);
                $cache->put($key, $current + $amount, now()->addMinutes(config('performance.raw_ttl', 10)));
            });
        } catch (\Throwable $e) {
            // Fallback no-lock
            $current = $cache->get($key, 0);
            $cache->put($key, $current + $amount, now()->addMinutes(config('performance.raw_ttl', 10)));
        }
    }

    public static function timing(string $metric, float $ms): void
    {
        // store aggregate sum + count to compute mean later
        self::increment($metric . '_count');
        self::increment($metric . '_time', (int) round($ms));
    }

    public static function snapshot(array $metrics): array
    {
        $window = config('performance.snapshot_window', 5);
        $data = [];
        if (self::isMigrationContext()) {
            return [];
        }
        $cache = cache();
        if (app()->environment('testing')) {
            $cache = Cache::store('array');
        }
        foreach ($metrics as $metric) {
            $sum = 0;
            $count = 0;
            $time = 0;
            for ($i = 0; $i < $window; $i++) {
                $minute = now()->subMinutes($i)->format('YmdHi');
                $sum += $cache->get(self::key($metric, $minute), 0);
                $count += $cache->get(self::key($metric . '_count', $minute), 0);
                $time += $cache->get(self::key($metric . '_time', $minute), 0);
            }
            $data[$metric] = [
                'sum' => $sum,
                'count' => $count,
                'avg_time_ms' => $count > 0 ? round($time / $count, 2) : 0,
            ];
        }

        return $data;
    }

    private static function minuteKey(string $metric): string
    {
        return self::key($metric, now()->format('YmdHi'));
    }

    private static function key(string $metric, string $minute): string
    {
        return 'perf:' . $metric . ':' . $minute;
    }

    private static function isMigrationContext(): bool
    {
        if (! app()->runningInConsole()) {
            return false;
        }
        $argv = implode(' ', $_SERVER['argv'] ?? []);

        return preg_match('/\b(migrate|db:seed)(:(fresh|install|refresh|reset|rollback))?/i', $argv) === 1;
    }
}
