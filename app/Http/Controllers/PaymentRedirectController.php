<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentRedirectController extends Controller
{
    /**
     * Show a simple simulated gateway page with success/fail buttons.
     * Or redirect to external gateway if external payment redirect is enabled.
     */
    public function show($paymentId)
    {
        $payment = Payment::find($paymentId);
        if (! $payment) {
            abort(404);
        }

        $gateway = null;
        if (isset($payment->payload['gateway_slug'])) {
            $gateway = \App\Models\PaymentGateway::where('slug', $payment->payload['gateway_slug'])->first();
        }

        $externalRedirectEnabled = false;
        try {
            $setting = \App\Models\Setting::first();
            $externalRedirectEnabled = $setting && ($setting->enable_external_payment_redirect ?? false);
        } catch (\Throwable $e) {
            // ignore settings failure
        }

        if ($gateway && ($gateway->enabled ?? false)) {
            $hasCreds = false;
            try {
                $hasCreds = method_exists($gateway, 'hasValidCredentials') ? $gateway->hasValidCredentials() : false;
            } catch (\Throwable) {
                $hasCreds = false;
            }

            if (! $hasCreds) {
                abort(404);
            }

            if ($externalRedirectEnabled || $hasCreds) {
                try {
                    // External driver invocation removed with legacy multi-gateway elimination
                    $order = $payment->order;
                    if ($order) {
                        $result = [];

                        // driver_error branch removed

                        if (! empty($result['redirect_url'])) { // no longer expected
                            $redirectUrl = $result['redirect_url'];
                            $currentUrl = url("/payments/redirect/{$payment->id}");

                            $isInternalRedirect = false;
                            try {
                                $parsed = parse_url($redirectUrl);
                                $path = $parsed['path'] ?? '';
                                if (preg_match('#/payments/redirect/(\d+)#', $path, $m)) {
                                    $otherId = (int) $m[1];
                                    if ($otherId !== (int) $payment->id) {
                                        $isInternalRedirect = true; // would create loop chain
                                    }
                                }
                            } catch (\Throwable) {
                                /* ignore */
                            }

                            if ($redirectUrl !== $currentUrl && ! $isInternalRedirect) {

                                return redirect()->away($redirectUrl);
                            }

                            // Loop or self redirect detected
                        }

                        if (! empty($result['html'])) {
                            session()->flash('driver_html', $result['html']);

                            return view('payments.redirect', ['payment' => $payment]);
                        }

                    } else {
                    }
                } catch (\Exception $e) {
                    Log::error('payment.redirect.exception', [
                        'payment_id' => $payment->id,
                        'gateway' => $gateway->driver ?? 'unknown',
                        'reason' => 'driver_exception',
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } else {
        }

        return view('payments.redirect', ['payment' => $payment]);
    }

    /**
     * Handle completion/cancellation from the gateway (simulated).
     * Query param: result=success|fail
     */
    public function complete(Request $request, $paymentId)
    {
        // Accept query param or POST/JSON payload from gateway
        $payload = $request->isJson() ? $request->json()->all() : $request->all();
        $result = $request->query('result', $payload['result'] ?? $payload['status'] ?? ($payload['success'] ?? null));

        $payment = Payment::find($paymentId);
        if (! $payment) {
            return redirect('/')->with('error', __('Payment not found'));
        }

        $orderId = data_get($payment->payload, 'order_reference') ?? data_get($payment->payload, 'order_id');
        $order = $orderId ? Order::find($orderId) : null;
        // Normalize result values to 'success', 'processing', or 'failed'
        $normalized = null;
        if (is_bool($result)) {
            $normalized = $result ? 'success' : 'failed';
        } elseif (is_string($result)) {
            $r = strtolower(trim((string) $result));
            if (in_array($r, ['success', 'ok', 'paid', 'completed'])) {
                $normalized = 'success';
            } elseif (in_array($r, ['processing', 'pending', 'in_progress', 'in_process'])) {
                $normalized = 'processing';
            } else {
                $normalized = in_array($r, ['fail', 'failed', 'cancel', 'cancelled', 'error', 'declined'])
                    ? 'failed'
                    : $r;
            }
        } elseif (is_null($result) && isset($payload['success'])) {
            $normalized = $payload['success'] ? 'success' : 'failed';
        }

        // If the gateway provided a transaction id, store it for mapping/idempotency
        if (! empty($payload['transaction_id'] ?? $payload['payment_id'] ?? $payload['id'])) {
            $tx = $payload['transaction_id'] ?? $payload['payment_id'] ?? $payload['id'];
            $payment->transaction_id = $tx;
            $payment->payload = array_merge($payment->payload ?? [], ['gateway_payload' => $payload]);
            $payment->save();
        }

        if ($normalized === 'success') {
            // mark payment completed and order paid
            $payment->status = 'completed';
            $payment->completed_at = now();
            $payment->save();

            if ($order) {
                $order->status = 'paid';
                $order->save();
            }

            // Redirect to success page
            return view('payments.success', compact('payment'))
                ->with('order', $order);
        }

        if ($normalized === 'processing') {
            // keep payment pending/processing and show processing page
            $payment->status = 'pending';
            $payment->payload = array_merge($payment->payload ?? [], ['gateway_payload' => $payload]);
            $payment->save();

            return view('payments.processing', compact('payment'))
                ->with('order', $order);
        }

        // Failure: mark payment and order as failed (preserve records)
        try {
            $payment->status = 'failed';
            $payment->failure_reason = 'user_cancelled_or_failed';
            $payment->failed_at = now();
            $payment->save();

            if ($order) {
                $order->payment_status = 'failed';
                $order->status = 'payment_failed';
                $order->save();
            }
        } catch (\Throwable $e) {
        }

        $errorMessage = $payment->failure_reason ?? __('Payment failed or cancelled');

        return view('payments.failure', compact('payment'))
            ->with('order', $order)
            ->with('error_message', $errorMessage);
    }
}
