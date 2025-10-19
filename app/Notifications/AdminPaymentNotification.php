<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Payment $payment;

    protected string $event;

    public function __construct(Payment $payment, string $event = 'created')
    {
        $this->payment = $payment;
        $this->event = $event;
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
        $order = $this->payment->order;

        return [
            'type' => 'payment',
            'payment_id' => $this->payment->id,
            'order_id' => $order?->id ?? null,
            'amount' => $this->payment->amount,
            'status' => $this->payment->status,
            'event' => $this->event,
            'url' => url('/admin/orders/' . ($order?->id ?? '')),
            'title' => __('Payment') . ' #' . ($this->payment->id ?? ''),
            'message' => __('Payment :id for order :order is :status', [
                'id' => $this->payment->id,
                'order' => $order?->id ?? '-',
                'status' => $this->payment->status
            ]),
        ];
    }
}
