<?php

return [
    'enabled' => true,
    // Write activity records via queue (after response) to reduce request latency
    'async' => env('ACTIVITY_ASYNC', true),
    // Number of days to keep activity records before pruning
    'prune_days' => env('ACTIVITY_PRUNE_DAYS', 30),
    // Deduplication window seconds (ignore identical activities within this window)
    'dedup_seconds' => env('ACTIVITY_DEDUP_SECONDS', 60),
];
