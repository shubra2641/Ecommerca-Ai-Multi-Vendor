<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\PushSubscription;
use App\Support\Performance\PerformanceRecorder;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.webpush.vapid_public_key'),
                'privateKey' => config('services.webpush.vapid_private_key'),
            ],
        ]);
    }

    public function sendToAll(array $payload): int
    {
        $count = 0;
        PushSubscription::query()->chunk(500, function ($subs) use (&$count, $payload): void {
            foreach ($subs as $sub) {
                $report = $this->webPush->sendOneNotification(new Subscription(
                    $sub->endpoint,
                    $sub->p256dh,
                    $sub->auth
                ), json_encode($payload));
                if ($report && $report->isSuccess()) {
                    $count++;
                    PerformanceRecorder::increment('push_sent');
                } elseif ($report && $report->isSubscriptionExpired()) {
                    $sub->delete();
                }
            }
        });

        return $count;
    }
}
