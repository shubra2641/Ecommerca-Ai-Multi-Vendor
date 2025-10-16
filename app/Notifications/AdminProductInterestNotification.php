<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminProductInterestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $interest;

    public function __construct($interest)
    {
        $this->interest = $interest;
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
            'type' => 'product_interest',
            'title' => __('Product interest'),
            'message' => __(':who requested notify for :product', ['who' => $this->interest->email ?? 'Guest', 'product' => $this->interest->product?->name ?? '']),
            'url' => route('admin.notify.index'),
            'icon' => 'bell',
        ];
    }
}
