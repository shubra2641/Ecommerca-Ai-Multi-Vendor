<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminStockLowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;

    protected $available;

    public function __construct($product, $available)
    {
        $this->product = $product;
        $this->available = $available;
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
            'type' => 'stock_low',
            'title' => __('Product stock low'),
            'message' => __(':product has :count items available', [
                'product' => $this->product->name,
                'count' => $this->available,
            ]),
            'url' => route('admin.products.show', $this->product->id),
            'icon' => 'exclamation-triangle',
        ];
    }
}
