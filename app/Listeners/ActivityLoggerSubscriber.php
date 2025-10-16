<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Events\OrderPaid;
use App\Events\OrderRefunded;
use App\Models\Activity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;

class ActivityLoggerSubscriber
{
    public function handleUserLogin(Login $event)
    {
        Activity::log('auth.login', 'User logged in', ['id' => $event->user->id], $event->user->id);
    }

    public function handleUserLogout(Logout $event)
    {
        Activity::log('auth.logout', 'User logged out', ['id' => $event->user->id], $event->user->id);
    }

    public function handleUserRegistered(Registered $event)
    {
        Activity::log('auth.register', 'User registered', ['id' => $event->user->id], $event->user->id);
    }

    public function handleOrderPaid(OrderPaid $event)
    {
        Activity::log('order.paid', 'Order paid: #' . $event->order->id, ['order_id' => $event->order->id], $event->order->user_id);
    }

    public function handleOrderCancelled(OrderCancelled $event)
    {
        Activity::log('order.cancelled', 'Order cancelled: #' . $event->order->id, ['order_id' => $event->order->id], $event->order->user_id);
    }

    public function handleOrderRefunded(OrderRefunded $event)
    {
        Activity::log('order.refunded', 'Order refunded: #' . $event->order->id, ['order_id' => $event->order->id], $event->order->user_id);
    }

    public function subscribe($events)
    {
        $events->listen(Login::class, [ActivityLoggerSubscriber::class, 'handleUserLogin']);
        $events->listen(Logout::class, [ActivityLoggerSubscriber::class, 'handleUserLogout']);
        $events->listen(Registered::class, [ActivityLoggerSubscriber::class, 'handleUserRegistered']);

        $events->listen(OrderPaid::class, [ActivityLoggerSubscriber::class, 'handleOrderPaid']);
        $events->listen(OrderCancelled::class, [ActivityLoggerSubscriber::class, 'handleOrderCancelled']);
        $events->listen(OrderRefunded::class, [ActivityLoggerSubscriber::class, 'handleOrderRefunded']);
    }
}
