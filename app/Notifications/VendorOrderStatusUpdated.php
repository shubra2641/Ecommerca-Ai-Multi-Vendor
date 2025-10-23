<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorOrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;

    protected string $status;

    public function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Order status updated') . ' #' . $this->order->id)
            ->line(__('Order #:id status changed to :status', ['id' => $this->order->id, 'status' => $this->status]))
            ->action(__('View order items'), route('vendor.orders.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'vendor_order_status_updated',
            'title' => __('Order update'),
            'message' => __('Order #:id status changed to :status', [
                'id' => $this->order->id,
                'status' => $this->status,
            ]),
            'url' => route('vendor.orders.index'),
            'order_id' => $this->order->id,
            'status' => $this->status,
        ];
    }
}
