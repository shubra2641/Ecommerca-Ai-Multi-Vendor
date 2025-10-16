<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GatewayReturnController extends Controller
{
    public function tapReturn(Payment $payment)
    {
        $slug = 'tap';
        $gateway = PaymentGateway::where('slug', $slug)->where('enabled', true)->first();
        if (! $gateway) {
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __($slug . ' gateway disabled'));
            }

            return redirect()->route('checkout.cancel')->with('error', __($slug . ' gateway disabled'));
        }
        try {
            $svc = app(\App\Services\Payments\PaymentGatewayService::class);
            $result = $svc->verifyTapCharge($payment, $gateway);
            $msgKey = match ($result['status']) {
                'paid' => 'Payment successful',
                'failed' => 'Payment failed',
                default => 'Payment pending confirmation'
            };
            if ($payment->order_id) {
                // If we have an order, on paid redirect to checkout.success and clear cart
                if ($result['status'] === 'paid') {
                    session()->forget('cart');

                    return redirect()->route('checkout.success', ['order' => $payment->order_id])->with('success', __($msgKey));
                }

                return redirect()->route('orders.show', $payment->order_id)->with(
                    $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info'),
                    __($msgKey)
                );
            }
            // No order yet: store pending cart in session and redirect to auth-protected cancel page (mirrors Stripe UX)
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['tap_pending_cart' => $cart]);
            }
            // Map status to flash level
            $level = $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info');

            return redirect()->route('checkout.cancel')->with($level, __($msgKey));
        } catch (\Throwable $e) {
            Log::error('tap.return.error', ['payment_id' => $payment->id, 'msg' => $e->getMessage()]);
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __('Tap verification error'));
            }
            // restore cart from snapshot and show failure page
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['cart' => $cart]);
            }

            return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', __('Tap verification error'));
        }
    }

    public function weacceptReturn(Payment $payment)
    {
        $slug = 'weaccept';
        $gateway = PaymentGateway::where('slug', $slug)->where('enabled', true)->first();
        if (! $gateway) {
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __($slug . ' gateway disabled'));
            }

            return redirect()->route('checkout.cancel')->with('error', __($slug . ' gateway disabled'));
        }
        try {
            // Handle mock return (local testing) - if ?mock=1 or payment payload marks mock
            $isMockReturn = request()->boolean('mock') || (! empty($payment->payload['weaccept_mock']));
            if ($isMockReturn) {
                $snap = $payment->payload['checkout_snapshot'] ?? null;
                $order = null;
                if ($snap) {
                    try {
                        $order = DB::transaction(function () use ($snap, $payment) {
                            $order = Order::create([
                                'user_id' => $snap['user_id'] ?? null,
                                'status' => 'completed',
                                'total' => $snap['total'] ?? 0,
                                'items_subtotal' => $snap['total'] ?? 0,
                                'currency' => $snap['currency'] ?? config('app.currency', 'USD'),
                                'shipping_address' => $snap['shipping_address'] ?? null,
                                'payment_method' => $payment->method,
                                'payment_status' => 'paid',
                            ]);
                            foreach ($snap['items'] ?? [] as $it) {
                                if (empty($it['product_id'])) {
                                    continue;
                                }
                                OrderItem::create([
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
                    } catch (\Throwable $_) {
                        $order = null;
                    }
                }
                $payment->status = 'paid';
                $payment->save();
                try {
                    session()->forget('cart');
                } catch (\Throwable $_) {
                }
                if ($order) {
                    return redirect()->route('checkout.success', ['order' => $order->id])->with('success', __('Payment successful'));
                }

                return redirect()->route('checkout.cancel')->with('info', __('Payment processed (mock)'));
            }

            $successParam = request()->query('success');
            $pendingParam = request()->query('pending');
            $hasExplicitResult = ! is_null($successParam) || ! is_null($pendingParam);
            if ($hasExplicitResult) {
                $isSuccess = filter_var($successParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                $isPending = filter_var($pendingParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($isSuccess === true) {
                    try {
                        if (! $payment->order_id) {
                            $snap = $payment->payload['checkout_snapshot'] ?? null;
                            if ($snap) {
                                $order = DB::transaction(function () use ($snap, $payment) {
                                    $order = Order::create([
                                        'user_id' => $snap['user_id'] ?? null,
                                        'status' => 'completed',
                                        'total' => $snap['total'] ?? 0,
                                        'items_subtotal' => $snap['total'] ?? 0,
                                        'currency' => $snap['currency'] ?? config('app.currency', 'USD'),
                                        'shipping_address' => $snap['shipping_address'] ?? null,
                                        'payment_method' => $payment->method,
                                        'payment_status' => 'paid',
                                    ]);
                                    foreach ($snap['items'] ?? [] as $it) {
                                        if (empty($it['product_id'])) {
                                            continue;
                                        }
                                        OrderItem::create([
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
                            }
                        }
                    } catch (\Throwable $_) {
                        /* ignore and continue */
                    }
                    $payment->status = 'paid';
                    $payment->save();
                    try {
                        session()->forget('cart');
                    } catch (\Throwable $_) {
                    }
                    if ($payment->order_id) {
                        return redirect()->route('checkout.success', ['order' => $payment->order_id])->with('success', __('Payment successful'));
                    }

                    return redirect()->route('checkout.cancel')->with('success', __('Payment successful'));
                }
                if ($isSuccess === false) {
                    $payment->status = 'failed';
                    $payment->save();

                    return redirect()->route('checkout.cancel')->with('error', __('Payment failed or cancelled'));
                }
                if ($isPending === true) {
                    $payment->status = 'processing';
                    $payment->save();

                    return redirect()->route('checkout.cancel')->with('info', __('Payment pending confirmation'));
                }
            }

            $svc = app(\App\Services\Payments\PaymentGatewayService::class);
            $result = $svc->verifyGenericGatewayCharge($payment, $gateway);
            $msgKey = match ($result['status']) {
                'paid' => 'Payment successful',
                'failed' => 'Payment failed',
                default => 'Payment pending confirmation'
            };
            if ($payment->order_id) {
                if ($result['status'] === 'paid') {
                    session()->forget('cart');

                    return redirect()->route('checkout.success', ['order' => $payment->order_id])->with('success', __($msgKey));
                }

                return redirect()->route('orders.show', $payment->order_id)->with(
                    $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info'),
                    __($msgKey)
                );
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['weaccept_pending_cart' => $cart]);
            }
            $level = $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info');

            return redirect()->route('checkout.cancel')->with($level, __($msgKey));
        } catch (\Throwable $e) {
            Log::error('weaccept.return.error', ['payment_id' => $payment->id, 'msg' => $e->getMessage()]);
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __('WeAccept verification error'));
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['cart' => $cart]);
            }

            return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', __('WeAccept verification error'));
        }
    }

    public function payeerReturn(Payment $payment)
    {
        $slug = 'payeer';
        $gateway = PaymentGateway::where('slug', $slug)->where('enabled', true)->first();
        if (! $gateway) {
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __($slug . ' gateway disabled'));
            }

            return redirect()->route('checkout.cancel')->with('error', __($slug . ' gateway disabled'));
        }
        try {
            $svc = app(\App\Services\Payments\PaymentGatewayService::class);
            $result = $svc->verifyGenericGatewayCharge($payment, $gateway);
            $msgKey = match ($result['status']) {
                'paid' => 'Payment successful',
                'failed' => 'Payment failed',
                default => 'Payment pending confirmation'
            };
            if ($payment->order_id) {
                if ($result['status'] === 'paid') {
                    session()->forget('cart');

                    return redirect()->route('checkout.success', ['order' => $payment->order_id])->with('success', __($msgKey));
                }

                return redirect()->route('orders.show', $payment->order_id)->with(
                    $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info'),
                    __($msgKey)
                );
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['payeer_pending_cart' => $cart]);
            }
            $level = $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info');

            return redirect()->route('checkout.cancel')->with($level, __($msgKey));
        } catch (\Throwable $e) {
            Log::error('payeer.return.error', ['payment_id' => $payment->id, 'msg' => $e->getMessage()]);
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __('Payeer verification error'));
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['cart' => $cart]);
            }

            return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', __('Payeer verification error'));
        }
    }

    public function iframeHost(Request $request)
    {
        return view('payments.iframe');
    }

    public function iframeForPayment(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }
        $payload = $payment->payload ?? [];
        $redirect = $payload['weaccept_iframe_url'] ?? null;
        $fallback = $payload['weaccept_order_url'] ?? null;
        if (! $redirect && ! empty($payload['weaccept_payment_token'])) {
            $base = rtrim($payload['weaccept_api_base'] ?? 'https://accept.paymob.com', '/');
            $iframeId = $payload['weaccept_iframe_id'] ?? ($payload['weaccept_integration_id'] ?? null);
            if ($iframeId) {
                $redirect = $base . '/api/acceptance/iframes/' . $iframeId . '?payment_token=' . $payload['weaccept_payment_token'];
            }
        }

        return view('payments.iframe')->with(['redirect' => $redirect, 'fallback' => $fallback]);
    }

    public function paytabsReturn(Payment $payment)
    {
        $slug = 'paytabs';
        $gateway = PaymentGateway::where('slug', $slug)->where('enabled', true)->first();
        if (! $gateway) {
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __($slug . ' gateway disabled'));
            }

            return redirect()->route('checkout.cancel')->with('error', __($slug . ' gateway disabled'));
        }
        try {
            $svc = app(\App\Services\Payments\PaymentGatewayService::class);
            $result = $svc->verifyGenericGatewayCharge($payment, $gateway);
            $msgKey = match ($result['status']) {
                'paid' => 'Payment successful',
                'failed' => 'Payment failed',
                default => 'Payment pending confirmation'
            };
            if ($payment->order_id) {
                if ($result['status'] === 'paid') {
                    session()->forget('cart');

                    return redirect()->route('checkout.success', ['order' => $payment->order_id])->with('success', __($msgKey));
                }

                return redirect()->route('orders.show', $payment->order_id)->with(
                    $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info'),
                    __($msgKey)
                );
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['paytabs_pending_cart' => $cart]);
            }
            $level = $result['status'] === 'failed' ? 'error' : ($result['status'] === 'paid' ? 'success' : 'info');

            return redirect()->route('checkout.cancel')->with($level, __($msgKey));
        } catch (\Throwable $e) {
            Log::error('paytabs.return.error', ['payment_id' => $payment->id, 'msg' => $e->getMessage()]);
            if ($payment->order_id) {
                return redirect()->route('orders.show', $payment->order_id)->with('error', __('Paytabs verification error'));
            }
            $snap = $payment->payload['checkout_snapshot'] ?? null;
            if ($snap && ! empty($snap['items'])) {
                $cart = [];
                foreach ($snap['items'] as $it) {
                    if (empty($it['product_id'])) {
                        continue;
                    }
                    $cart[$it['product_id']] = ['qty' => $it['qty'] ?? 1, 'price' => $it['price'] ?? 0];
                }
                session(['cart' => $cart]);
            }

            return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', __('Paytabs verification error'));
        }
    }
}
