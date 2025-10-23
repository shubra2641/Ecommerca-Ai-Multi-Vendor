<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentWebhookReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $payment;

    public $gateway;

    public $webhookData;

    public $status;

    /**
     * Create a new event instance.
     */
    public function __construct(Payment $payment, PaymentGateway $gateway, array $webhookData, string $status)
    {
        $this->payment = $payment;
        $this->gateway = $gateway;
        $this->webhookData = $webhookData;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('payment.' . $this->payment->id),
            new PrivateChannel('order.' . $this->payment->order_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'payment_id' => $this->payment->payment_id,
            'order_id' => $this->payment->order_id,
            'status' => $this->status,
            'gateway' => $this->gateway,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'updated_at' => $this->payment->updated_at->toISOString(),
        ];
    }
}
