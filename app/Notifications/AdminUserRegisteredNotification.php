<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminUserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        // admin quick notifications - database required; mail optional
        $via = ['database'];
        if (\App\Support\MailHelper::mailIsAvailable()) {
            $via[] = 'mail';
        }

        return $via;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'user_registered',
            'title' => __('New user registered'),
            'message' => $this->user->name . ' (' . ($this->user->email ?? '') . ') ' . __('registered'),
            'url' => route('admin.users.show', $this->user->id),
            'icon' => 'user-plus',
        ];
    }
}
