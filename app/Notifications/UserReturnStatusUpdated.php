<?php

namespace App\Notifications;

use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserReturnStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected OrderItem $item;

    protected string $status;

    public function __construct(OrderItem $item, string $status)
    {
        $this->item = $item;
        $this->status = $status;
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
        $view = $locale === 'ar' ? 'emails.returns.user_status_ar_html' : 'emails.returns.user_status_en_html';

        $url = null;
        try {
            if (\Illuminate\Support\Facades\Route::has('returns.index')) {
                $url = url(route('returns.index'));
            } else {
                $url = url('/returns');
            }
        } catch (\Throwable $e) {
            $url = url('/returns');
        }

        return (new MailMessage())
            ->subject(__('returns.user_status_subject'))
            ->view($view, [
                'product' => $this->item->name,
                'status' => $this->status,
                'url' => $url,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $url = null;
        try {
            if (\Illuminate\Support\Facades\Route::has('returns.index')) {
                $url = route('returns.index');
            } else {
                $url = '/returns';
            }
        } catch (\Throwable $e) {
            $url = '/returns';
        }

        return [
            'type' => 'return_status_updated',
            'title' => __('Return status updated'),
            'message' => __('Return for :product is now :status', [
                'product' => $this->item->name,
                'status' => $this->status,
            ]),
            'url' => $url,
            'icon' => 'undo',
            'item_id' => $this->item->id,
            'status' => $this->status,
        ];
    }
}
