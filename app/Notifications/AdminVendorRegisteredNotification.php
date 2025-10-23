<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminVendorRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
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
            'type' => 'vendor_registered',
            'title' => __('New vendor registered'),
            'message' => $this->vendor->name.' ('.($this->vendor->email ?? '').') '.__('registered'),
            'url' => route('admin.users.show', $this->vendor->id),
            'icon' => 'store',
        ];
    }
}
