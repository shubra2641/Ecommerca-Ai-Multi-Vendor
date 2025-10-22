<?php

return [
    'enabled' => env('MIGRATION_PROFILER', true),
    'threshold_seconds' => env('MIGRATION_THRESHOLD', 120),
    'warn_each_seconds' => env('MIGRATION_WARN_EACH', 3),
    'force_seed' => env('MIGRATION_FORCE_SEED', false),
];
