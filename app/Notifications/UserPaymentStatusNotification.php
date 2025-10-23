<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Payment $payment;

    protected string $status;

    public function __construct(Payment $payment, string $status)
    {
        $this->payment = $payment;
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
        $order = $this->payment->order;
        $locale = app()->getLocale();
        $view = $locale === 'ar' ? 'emails.payments.payment_status_ar' : 'emails.payments.payment_status_en';

        return (new MailMessage())
            ->subject(__('Payment update').' #'.($order?->id ?? ''))
            ->view($view, ['order' => $order, 'payment' => $this->payment, 'status' => $this->status]);
    }

    public function toArray(object $notifiable): array
    {
        $order = $this->payment->order;

        return [
            'type' => 'payment_status',
            'title' => __('Payment').' #'.($this->payment->id ?? ''),
            'message' => __('Payment :id for order :order is :status', [
                'id' => $this->payment->id,
                'order' => $order?->id ?? '-',
                'status' => $this->status,
            ]),
            'url' => $order ? route('orders.show', $order->id) : null,
            'icon' => 'credit-card',
            'payment_id' => $this->payment->id,
            'status' => $this->status,
        ];
    }
}
