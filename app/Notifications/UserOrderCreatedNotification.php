<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserOrderCreatedNotification extends Notification implements ShouldQueue
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

    public function toMail(object $notifiable): MailMessage
    {
        $locale = app()->getLocale();
        $view = $locale === 'ar' ? 'emails.orders.created_ar' : 'emails.orders.created_en';

        return (new MailMessage())
            ->subject(__('Order confirmation') . ' #' . $this->order->id)
            ->view($view, ['order' => $this->order]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_created',
            'title' => __('Order created'),
            'message' => __('Your order #:id was created', ['id' => $this->order->id]),
            'url' => route('orders.show', $this->order->id),
            'icon' => 'shopping-cart',
            'order_id' => $this->order->id,
        ];
    }
}
