<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminOrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        $via = ['database'];
        if (\App\Support\MailHelper::mailIsAvailable()) {
            $via[] = 'mail';
        }

        return $via;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_created',
            'title' => __('New order placed'),
            'message' => __('Order #:id placed by :name', [
                'id' => $this->order->id,
                'name' => $this->order->user?->name ?? ''
            ]),
            'url' => route('admin.orders.show', $this->order->id),
            'icon' => 'shopping-cart',
        ];
    }
}
