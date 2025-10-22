<?php

return [
    // Minimum seconds before accepting another identical subscription update
    'min_repeat_seconds' => env('INTEREST_MIN_REPEAT', 180),
    // Chunk size per job iteration
    'mail_chunk' => env('INTEREST_MAIL_CHUNK', 100),
    // Max queued mails per minute for back-in-stock job (rough pacing using delay scheduling)
    'rate_per_minute' => env('INTEREST_RATE_PER_MINUTE', 600),
    // Cache TTL (seconds) for interest counts per product
    'cache_ttl' => env('INTEREST_CACHE_TTL', 600),
    // Minimum percent drop (integer) required to trigger price drop notification
    'price_drop_min_percent' => env('PRICE_DROP_MIN_PERCENT', 5),
];
