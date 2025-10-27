<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = \App\Models\User::where('role', 'admin')->first();

if ($admin) {
    $admin->notify(new \App\Notifications\AdminBroadcastNotification(
        ['en' => 'Test Notification'],
        ['en' => 'This is a test notification for admin']
    ));

    echo 'Notification sent to admin: ' . $admin->name . PHP_EOL;
    echo 'Unread count: ' . $admin->unreadNotifications()->count() . PHP_EOL;
    echo 'Total notifications: ' . $admin->notifications()->count() . PHP_EOL;
} else {
    echo 'No admin user found' . PHP_EOL;
}
