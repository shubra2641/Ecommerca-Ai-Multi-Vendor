<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\OrderCancelled;
use App\Events\OrderPaid;
use App\Events\OrderRefunded;
use App\Events\PaymentWebhookReceived;
use App\Listeners\HandlePaymentWebhook;
use App\Listeners\StockAdjustmentListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPaid::class => [
            StockAdjustmentListener::class.'@handleOrderPaid',
            \App\Listeners\DistributeOrderProceedsListener::class.'@handle',
        ],
        OrderCancelled::class => [StockAdjustmentListener::class.'@handleOrderCancelled'],
        OrderRefunded::class => [StockAdjustmentListener::class.'@handleOrderRefunded'],
        PaymentWebhookReceived::class => [
            HandlePaymentWebhook::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     */
    protected $subscribe = [
    ];
}
