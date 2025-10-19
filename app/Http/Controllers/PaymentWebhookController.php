<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    // Legacy third-party payment manager removed

    /**
     * Generic webhook receiver for named driver (e.g. /webhooks/paymob)
     * Verifies signature if provided, enforces idempotency via transaction id.
     */
    public function handle(Request $request, string $driver)
    {
        // Read raw payload and signature
        $payload = (string) $request->getContent();
        $signature = $request->header('X-Signature') ??
            $request->header('X-Signature-Sha256') ??
            $request->header('Signature') ??
            $request->input('signature');

        // Try to load gateway config to obtain webhook secret
        $gateway = PaymentGateway::where('driver', $driver)->first();
        $secret = null;
        if ($gateway) {
            $cfg = $gateway->config ?? [];
            $secret = $cfg['webhook_secret'] ?? $cfg['secret_key'] ?? $cfg['signature_secret'] ?? null;
        }

        if ($secret && $signature) {
            $expected = hash_hmac('sha256', $payload, $secret);
            if (! hash_equals($expected, $signature)) {
                Log::warning('Webhook signature mismatch for driver ' . $driver);

                return response('invalid signature', 400);
            }
        }

        $data = $request->all();

        // Identify transaction id
        $tx = $data['transaction_id'] ??
            $data['payment_id'] ??
            $data['id'] ??
            $data['reference'] ??
            $data['order_reference'] ??
            null;
        if (! $tx) {
            Log::warning('Webhook missing transaction identifier for driver ' . $driver);

            return response('missing id', 400);
        }

        // Idempotency: already processed?
        $existing = Payment::where('transaction_id', $tx)->first();
        if ($existing && in_array($existing->status, ['completed', 'refunded', 'cancelled'])) {
            return response('already processed', 200);
        }

        // Map to payment record
        $payment = $existing;
        if (! $payment) {
            $orderId = $data['order_id'] ?? $data['order_reference'] ?? null;
            if ($orderId) {
                $payment = Payment::where('order_id', $orderId)->where('method', $driver)->orderByDesc('id')->first();
            }
        }

        if (! $payment) {
            Log::warning('Webhook could not map to an existing payment for driver ' . $driver . ' tx=' . $tx);

            return response('not found', 404);
        }

        // Reconcile
        $status = $this->determineStatusFromPayload($data);

        if (in_array($status, ['completed', 'success', 'paid'])) {
            $payment->transaction_id = $tx;
            $payment->status = 'completed';
            $payment->payload = array_merge($payment->payload ?? [], ['webhook' => $data]);
            $payment->completed_at = now();
            $payment->save();

            $order = $payment->order;
            if ($order && ($order->payment_status ?? null) !== 'paid') {
                $order->payment_status = 'paid';
                $order->status = $order->status === 'pending' ? 'processing' : $order->status;
                $order->save();
                try {
                    event(new \App\Events\OrderPaid($order));
                } catch (\Throwable $e) {
                    Log::error('OrderPaid event error: ' . $e->getMessage());
                }
            }

            return response('ok', 200);
        }

        // Failure: mark failed but keep record (idempotency preserved)
        $payment->transaction_id = $tx;
        $payment->status = 'failed';
        $payment->failure_reason = $data['reason'] ?? ($data['message'] ?? 'failure');
        $payment->failed_at = now();
        $payment->payload = array_merge($payment->payload ?? [], ['webhook' => $data]);
        $payment->save();

        // Mark order as payment_failed to preserve audit trail
        $order = $payment->order;
        if ($order) {
            $order->payment_status = 'failed';
            $order->status = 'payment_failed';
            $order->save();
        }

        return response('failed', 200);
    }

    private function determineStatusFromPayload(array $data): string
    {
        $status = null;
        if (isset($data['status'])) {
            $status = $data['status'];
        }
        if (isset($data['payment_status'])) {
            $status = $data['payment_status'];
        }
        if (isset($data['success'])) {
            $status = $data['success'] ? 'success' : 'failed';
        }
        if (isset($data['paid'])) {
            $status = $data['paid'] ? 'paid' : 'failed';
        }

        if (! $status) {
            return 'failed';
        }

        return strtolower((string) $status);
    }

    private function isValidGateway(string $driver): bool
    {
        // Use configured available gateways if present
        // Basic whitelist (static) after removal
        $validGateways = ['stripe', 'offline'];

        return in_array($driver, $validGateways, true);
    }
}
