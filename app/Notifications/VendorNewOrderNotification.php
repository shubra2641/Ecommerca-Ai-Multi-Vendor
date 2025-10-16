<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorNewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;

    protected array $items;

    public function __construct(Order $order, array $items)
    {
        $this->order = $order;
        $this->items = $items; // array of OrderItem models or arrays
    }

    public function via(object $notifiable): array
    {
        // Vendor notifications should be database-only to avoid unsolicited emails
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = __('New order for your products (#:id)', ['id' => $this->order->id]);
        $body = __('An order including your product(s) was placed. Order #:id', ['id' => $this->order->id]);

        return (new MailMessage())
            ->subject($subject)
            ->line($body)
            ->action(__('View order'), route('vendor.orders.show', $this->items[0]->id ?? $this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        // Build per-item payloads with direct links where possible
        $items = array_map(function ($it) {
            $productId = is_object($it) ? ($it->product_id ?? $it->product?->id ?? null) : ($it['product_id'] ?? null);
            $itemId = is_object($it) ? ($it->id ?? null) : ($it['id'] ?? null);

            return [
                'item_id' => $itemId,
                'product_id' => $productId,
                'url' => $itemId ? route('vendor.orders.show', $itemId) : null,
            ];
        }, $this->items);

        return [
            'type' => 'vendor_new_order',
            'title' => __('New order'),
            'message' => __('Order #:id includes your products', ['id' => $this->order->id]),
            'url' => route('vendor.orders.show', $this->order->id),
            'order_id' => $this->order->id,
            'items' => $items,
        ];
    }
}
