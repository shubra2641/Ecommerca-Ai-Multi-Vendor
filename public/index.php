<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request. Wrap in try/catch so we can
// return a simple static HTML fallback if something goes fatally wrong
// before the framework can render the configured error pages.
try {
    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    // If a simple static fallback exists, serve it (prevents raw stack dumps in browser)
    $fallback = __DIR__ . '/500.html';
    if (is_file($fallback)) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
        echo file_get_contents($fallback);
        exit(1);
    }
    // If no fallback, rethrow so CLI/debugging can still handle it
    throw $e;
}
