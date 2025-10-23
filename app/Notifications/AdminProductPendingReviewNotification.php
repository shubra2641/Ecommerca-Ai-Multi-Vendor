<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminProductPendingReviewNotification extends Notification
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via($notifiable): array
    {
        return ['database']; // mail already sent separately
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'product_pending_review',
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'vendor_id' => $this->product->vendor_id,
            'created_at' => $this->product->created_at?->toDateTimeString(),
        ];
    }
}
