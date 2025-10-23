<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewReturnRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected OrderItem $item;

    public function __construct(OrderItem $item)
    {
        $this->item = $item;
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
        // prefer HTML mail templates
        $view = $locale === 'ar'
            ? 'emails.returns.admin_new_request_ar_html'
            : 'emails.returns.admin_new_request_en_html';

        return (new MailMessage)
            ->subject(__('returns.admin_new_request_subject', [
                'order' => $this->item->order_id,
            ]))
            ->view($view, [
                'product' => $this->item->name,
                'order_id' => $this->item->order_id,
                'url' => url(route('admin.returns.show', $this->item)),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'return_request',
            'title' => __('Return request'),
            'message' => __('Return requested for :product (order :order)', [
                'product' => $this->item->name,
                'order' => $this->item->order_id,
            ]),
            'url' => route('admin.returns.show', $this->item->id),
            'icon' => 'undo',
            'item_id' => $this->item->id,
            'order_id' => $this->item->order_id,
            'product' => $this->item->name,
        ];
    }
}
