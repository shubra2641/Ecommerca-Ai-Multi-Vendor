<?php

return [
    // Enable/disable demo mode from environment
    'enabled' => env('DEMO_MODE', false),

    // HTTP methods allowed in demo mode without restriction
    'allow_methods' => [
        'GET',
        'HEAD',
        'OPTIONS',
    ],

    // Route names (supports wildcards) allowed to perform mutations in demo
    // Keep login/logout/password reset accessible
    'allow_route_names' => [
        // Auth: allow login/logout only in demo
        'login',
        'logout',
        'admin.login',
        'admin.login.store',
        'admin.logout',
        // Payments: allow webhooks to avoid provider retries
        'payment.webhook',
    ],

    // API paths that should be allowed in demo (prefix matching)
    'allow_api_paths' => [
        // Example: 'api/auth/*'
    ],
];
