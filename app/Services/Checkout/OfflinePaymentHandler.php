<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentAttachment;
use Illuminate\Http\Request;

final class OfflinePaymentHandler
{
    /**
     * @return array<string, string>
     */
    public function handleOfflinePayment(Order $order, Request $request): array
    {
        $payment = $this->createOfflinePayment($order);
        $this->storeTransferAttachmentIfNeeded($payment, $request);

        return [
            'type' => 'offline',
            'redirect_url' => route('orders.show', $order),
        ];
    }

    private function createOfflinePayment(Order $order): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'offline',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);
    }

    private function storeTransferAttachmentIfNeeded(
        Payment $payment,
        Request $request
    ): void {
        if (! $request->hasFile('transfer_image')) {
            return;
        }

        $file = $request->file('transfer_image');
        $path = $file->store('payments', 'public');

        PaymentAttachment::create([
            'payment_id' => $payment->id,
            'path' => $path,
            'mime' => $file->getMimeType(),
            'user_id' => $payment->user_id,
        ]);
    }
}
