<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notifications Dropdown Limit
    |--------------------------------------------------------------------------
    | How many recent notifications to return for the header dropdown. This
    | does not affect the full notifications index page pagination size.
    */
    'dropdown_limit' => (int) env('NOTIFICATIONS_DROPDOWN_LIMIT', 10),

    /*
    |--------------------------------------------------------------------------
    | Notifications Poll Interval (ms)
    |--------------------------------------------------------------------------
    | Frontend scripts will poll the `latest` endpoint every this many
    | milliseconds after the initial preflight unread-count fetch.
    */
    'poll_interval_ms' => (int) env('NOTIFICATIONS_POLL_INTERVAL_MS', 30000),
];
