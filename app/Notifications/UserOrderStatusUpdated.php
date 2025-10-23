<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserOrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;

    protected string $status;

    protected ?array $tracking;

    public function __construct(Order $order, string $status, ?array $tracking = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->tracking = $tracking;
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
        $view = $locale === 'ar' ? 'emails.orders.status_ar' : 'emails.orders.status_en';

        return (new MailMessage())
            ->subject(__('Order update') . ' #' . $this->order->id)
            ->view($view, ['order' => $this->order, 'status' => $this->status, 'tracking' => $this->tracking]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_status_updated',
            'title' => __('Order update'),
            'message' => __('Order #:id status changed to :status', [
                'id' => $this->order->id,
                'status' => $this->status,
            ]),
            'url' => route('orders.show', $this->order->id),
            'icon' => 'truck',
            'order_id' => $this->order->id,
            'status' => $this->status,
        ];
    }
}
