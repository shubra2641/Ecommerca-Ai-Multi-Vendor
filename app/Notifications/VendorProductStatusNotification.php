<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorProductStatusNotification extends Notification
{
    use Queueable;

    protected Product $product;

    protected string $action;

    protected ?string $reason;

    public function __construct(Product $product, string $action = 'approved', ?string $reason = null)
    {
        $this->product = $product;
        $this->action = $action;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->action === 'approved') {
            $subject = __('Your product has been approved');
            $line = __('Your product :name was approved and is now visible on the store.', [
                'name' => $this->product->name
            ]);
        } else {
            $subject = __('Your product was rejected');
            $line = __('Your product :name was rejected.', ['name' => $this->product->name]);
            if ($this->reason) {
                $line .= '\n' . $this->reason;
            }
        }

        return (new MailMessage())
            ->subject($subject)
            ->line($line)
            ->action(__('View product'), route('vendor.products.edit', $this->product->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'product_' . $this->action,
            'title' => $this->action === 'approved' ? __('Product approved') : __('Product rejected'),
            'message' => $this->action === 'approved'
                ? __('Your product :name was approved', ['name' => $this->product->name])
                : __('Your product :name was rejected', ['name' => $this->product->name]),
            'url' => route('vendor.products.edit', $this->product->id),
            'product_id' => $this->product->id,
            'reason' => $this->reason,
        ];
    }
}
