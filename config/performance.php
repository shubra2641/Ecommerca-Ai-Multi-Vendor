<?php

return [
    'enabled' => env('PERFORMANCE_MONITOR_ENABLED', true),
    // Minutes of per-minute keys to aggregate when snapshotting (default 5)
    'snapshot_window' => env('PERFORMANCE_SNAPSHOT_WINDOW', 5),
    // How many minutes to keep raw minute counters in cache
    'raw_ttl' => env('PERFORMANCE_RAW_TTL', 600),
];
