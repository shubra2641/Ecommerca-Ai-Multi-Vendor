<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalController extends Controller
{
    public function return(Payment $payment)
    {
        if ($payment->method !== 'paypal' || $payment->status !== 'pending') {
            return redirect('/')->with('error', __('Invalid payment state'));
        }
        $cfg = $payment->paymentGateway?->config ?? [];
        $clientId = $cfg['paypal_client_id'] ?? null;
        $secret = $cfg['paypal_secret'] ?? null;
        $mode = ($cfg['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        $base = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        try {
            // Reuse token if still valid to reduce chance of timeout
            $payload = $payment->payload ?? [];
            $accessToken = null;
            $storedToken = $payload['paypal_access_token'] ?? null;
            $storedExpiry = isset($payload['paypal_token_expires_at']) ? \Carbon\Carbon::parse($payload['paypal_token_expires_at']) : null;
            if ($storedToken && $storedExpiry && now()->lt($storedExpiry)) {
                $accessToken = $storedToken;
            }
            if (! $accessToken) {
                $tokenResp = Http::withBasicAuth($clientId, $secret)
                    ->asForm()
                    ->timeout(20)
                    ->retry(2, 300)
                    ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);
                if (! $tokenResp->ok()) {
                    throw new \Exception('token_http_' . $tokenResp->status());
                }
                $accessToken = $tokenResp->json('access_token');
                $expiresIn = (int) ($tokenResp->json('expires_in') ?? 0);
                $payload['paypal_access_token'] = $accessToken;
                $payload['paypal_token_expires_at'] = now()->addSeconds(max(0, $expiresIn - 60))->toIso8601String();
                $payment->payload = $payload;
                $payment->save();
            }
            $orderId = $payment->payload['paypal_order_id'] ?? null;
            if (! $orderId) {
                throw new \Exception('missing_order');
            }
            $captureUrl = $payment->payload['paypal_capture_url'] ?? ($base . '/v2/checkout/orders/' . $orderId . '/capture');
            try {
                $captureResp = Http::withToken($accessToken)
                    ->asJson()
                    ->acceptJson()
                    ->timeout(25)
                    ->retry(2, 400)
                    ->post($captureUrl, (object) []);
                $statusCode = $captureResp->status();
                $body = $captureResp->json();
                Log::info('paypal.capture.raw', ['code' => $statusCode, 'id' => $orderId, 'body_status' => $body['status'] ?? null]);
            } catch (\Illuminate\Http\Client\RequestException $reqEx) {
                $resp = $reqEx->response;
                $respBody = null;
                $respStatus = null;
                if ($resp) {
                    try {
                        $respBody = $resp->json();
                    } catch (\Throwable $_) {
                        $respBody = $resp->body();
                    }
                    $respStatus = $resp->status();
                }
                Log::error('paypal.capture.request_exception', ['order_id' => $orderId, 'status' => $respStatus, 'response' => $respBody]);
                // Mark payment failed and return
                $payment->status = 'failed';
                $payment->failure_reason = 'capture_request_exception';
                $payment->failed_at = now();
                $payment->payload = array_merge($payment->payload ?? [], ['paypal_capture_error' => $respBody]);
                $payment->save();
                if ($payment->order_id) {
                    return redirect()->route('orders.show', $payment->order_id)->with('error', __('Payment processing error'));
                }
                // No order created yet: restore cart from checkout snapshot and show failure page
                $snap = $payment->payload['checkout_snapshot'] ?? null;
                if ($snap && ! empty($snap['items'])) {
                    $cart = [];
                    foreach ($snap['items'] as $it) {
                        if (empty($it['product_id'])) {
                            continue;
                        }
                        $cart[$it['product_id']] = [
                            'qty' => $it['qty'] ?? 1,
                            'price' => $it['price'] ?? 0,
                        ];
                    }
                    session(['cart' => $cart]);
                }
                $errorMessage = __('Payment processing error');

                return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', $errorMessage);
            }
            if ($statusCode === 409) {
                // Already captured or duplicate call: fetch order details
                $details = Http::withToken($accessToken)->acceptJson()->get($base . '/v2/checkout/orders/' . $orderId);
                $body = $details->json();
                Log::warning('paypal.capture.duplicate', ['order_id' => $orderId]);
            }
            if ($statusCode >= 200 && $statusCode < 300) {
                // Determine final state
                $paypalStatus = $body['status'] ?? null; // COMPLETED, APPROVED, etc.
                // Try to extract capture status
                $captureStatus = $body['purchase_units'][0]['payments']['captures'][0]['status'] ?? null;
                $finalStatus = null;
                if (in_array($paypalStatus, ['COMPLETED']) || in_array($captureStatus, ['COMPLETED'])) {
                    $finalStatus = 'completed';
                } elseif (in_array($paypalStatus, ['APPROVED']) && $captureStatus === null) {
                    // Not yet captured but approved; attempt one more capture request (idempotent)
                    $second = Http::withToken($accessToken)
                        ->asJson()
                        ->acceptJson()
                        ->post($base . '/v2/checkout/orders/' . $orderId . '/capture', (object) []);
                    if ($second->status() >= 200 && $second->status() < 300) {
                        $body = $second->json();
                        $captureStatus = $body['purchase_units'][0]['payments']['captures'][0]['status'] ?? null;
                        if ($captureStatus === 'COMPLETED') {
                            $finalStatus = 'completed';
                        }
                    }
                }
                if ($finalStatus === 'completed') {
                    // If no order exists yet (we used checkout snapshot), create it now
                    if (! $payment->order_id) {
                        $snap = $payment->payload['checkout_snapshot'] ?? null;
                        if ($snap) {
                            try {
                                $order = \DB::transaction(function () use ($snap) {
                                    $order = \App\Models\Order::create([
                                        'user_id' => $snap['user_id'] ?? null,
                                        'status' => 'completed',
                                        'total' => $snap['total'] ?? 0,
                                        'items_subtotal' => $snap['total'] ?? 0,
                                        'currency' => $snap['currency'] ?? config('app.currency', 'USD'),
                                        'shipping_address' => $snap['shipping_address'] ?? null,
                                        'payment_method' => 'paypal',
                                        'payment_status' => 'paid',
                                    ]);
                                    foreach ($snap['items'] ?? [] as $it) {
                                        \App\Models\OrderItem::create([
                                            'order_id' => $order->id,
                                            'product_id' => $it['product_id'] ?? null,
                                            'name' => $it['name'] ?? null,
                                            'qty' => $it['qty'] ?? 1,
                                            'price' => $it['price'] ?? 0,
                                        ]);
                                    }

                                    return $order;
                                });
                                $payment->order_id = $order->id;
                            } catch (\Throwable $e) {
                                Log::error('paypal.order.create_from_snapshot_failed', ['error' => $e->getMessage()]);
                            }
                        }
                    }
                    $payment->status = 'completed';
                    $payment->completed_at = now();
                    $payment->payload = array_merge($payment->payload ?? [], ['paypal_capture' => $body]);
                    $payment->save();
                    if ($payment->order) {
                        $payment->order->payment_status = 'paid';
                        $payment->order->status = 'completed';
                        $payment->order->save();
                    }
                    // Clear cart on successful payment
                    session()->forget('cart');

                    return redirect()->route('orders.show', $payment->order_id)->with('success', __('Payment completed'));
                }
                // Not completed yet -> treat as pending / processing
                $payment->status = 'processing';
                $payment->payload = array_merge($payment->payload ?? [], ['paypal_capture_attempt' => $body]);
                $payment->save();
                if ($payment->order_id) {
                    return redirect()->route('orders.show', $payment->order_id)->with('info', __('Payment pending confirmation'));
                }
                $snap = $payment->payload['checkout_snapshot'] ?? null;
                if ($snap && ! empty($snap['items'])) {
                    $cart = [];
                    foreach ($snap['items'] as $it) {
                        if (empty($it['product_id'])) {
                            continue;
                        }
                        $cart[$it['product_id']] = [
                            'qty' => $it['qty'] ?? 1,
                            'price' => $it['price'] ?? 0,
                        ];
                    }
                    session(['cart' => $cart]);
                }
                $msg = __('Payment pending confirmation');

                return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', $msg);
            }
            if ($statusCode === 400) {
                Log::error('paypal.capture_failed_invalid_request', [
                    'order_id' => $orderId,
                    'response_json' => $body,
                ]);
            } else {
                Log::error('paypal.capture_failed', ['status' => $statusCode, 'body' => $captureResp->body()]);
            }
            $payment->status = 'failed';
            $payment->failure_reason = 'capture_failed_http_' . $statusCode;
            $payment->failed_at = now();
            $payment->save();
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __('Payment capture failed'));
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = [
                        'qty' => $it['qty'] ?? 1,
                        'price' => $it['price'] ?? 0,
                    ];
                }
                session(['cart' => $cart]);
            }
            $errorMessage = __('Payment capture failed');

            return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', $errorMessage);
        } catch (\Throwable $e) {
            Log::error('paypal.return.exception', ['error' => $e->getMessage()]);
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __('Payment processing error'));
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = [
                        'qty' => $it['qty'] ?? 1,
                        'price' => $it['price'] ?? 0,
                    ];
                }
                session(['cart' => $cart]);
            }
            $errorMessage = __('Payment processing error');

            return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', $errorMessage);
        }
    }

    public function cancel(Payment $payment)
    {
        if ($payment->method === 'paypal' && $payment->status === 'pending') {
            $payment->status = 'failed';
            $payment->failure_reason = 'user_cancelled';
            $payment->failed_at = now();
            $payment->save();
        }
        if ($payment->order_id) {
            return redirect()->route('orders.show', $payment->order_id)->with('error', __('Payment cancelled'));
        }
        $snap = $payment->payload['checkout_snapshot'] ?? null;
        if ($snap && ! empty($snap['items'])) {
            $cart = [];
            foreach ($snap['items'] as $it) {
                if (empty($it['product_id'])) {
                    continue;
                }
                $cart[$it['product_id']] = [
                    'qty' => $it['qty'] ?? 1,
                    'price' => $it['price'] ?? 0,
                ];
            }
            session(['cart' => $cart]);
        }
        $errorMessage = __('Payment cancelled');

        return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', $errorMessage);
    }
}
