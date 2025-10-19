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
    private $paymentGatewayService;

    public function __construct()
    {
        $this->paymentGatewayService = app(\App\Services\Payments\PaymentGatewayService::class);
    }

    public function tapReturn(Payment $payment)
    {
        return $this->handleGatewayReturn($payment, 'tap');
    }

    public function weacceptReturn(Payment $payment)
    {
        return $this->handleGatewayReturn($payment, 'weaccept');
    }

    public function payeerReturn(Payment $payment)
    {
        return $this->handleGatewayReturn($payment, 'payeer');
    }

    public function paytabsReturn(Payment $payment)
    {
        return $this->handleGatewayReturn($payment, 'paytabs');
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

        if (!$redirect && !empty($payload['weaccept_payment_token'])) {
            $base = rtrim($payload['weaccept_api_base'] ?? 'https://accept.paymob.com', '/');
            $iframeId = $payload['weaccept_iframe_id'] ?? ($payload['weaccept_integration_id'] ?? null);
            if ($iframeId) {
                $redirect = $base . '/api/acceptance/iframes/' . $iframeId . '?payment_token=' . $payload['weaccept_payment_token'];
            }
        }

        return view('payments.iframe')->with(['redirect' => $redirect, 'fallback' => $fallback]);
    }

    private function handleGatewayReturn(Payment $payment, string $gatewaySlug)
    {
        $gateway = $this->getEnabledGateway($gatewaySlug);
        
        if (!$gateway) {
            return $this->redirectWithError($payment, $gatewaySlug . ' gateway disabled');
        }

        try {
            // Handle special cases for weaccept
            if ($gatewaySlug === 'weaccept') {
                return $this->handleWeacceptReturn($payment, $gateway);
            }

            return $this->processStandardReturn($payment, $gateway, $gatewaySlug);
        } catch (\Throwable $e) {
            Log::error("{$gatewaySlug}.return.error", [
                'payment_id' => $payment->id, 
                'msg' => $e->getMessage()
            ]);
            
            return $this->handleReturnError($payment, $gatewaySlug, $e->getMessage());
        }
    }

    private function handleWeacceptReturn(Payment $payment, PaymentGateway $gateway)
    {
        // Handle mock return
        if ($this->isMockReturn($payment)) {
            return $this->handleMockPayment($payment);
        }

        // Handle explicit success/pending parameters
        $explicitResult = $this->getExplicitResult();
        if ($explicitResult) {
            return $this->handleExplicitResult($payment, $explicitResult);
        }

        return $this->processStandardReturn($payment, $gateway, 'weaccept');
    }

    private function processStandardReturn(Payment $payment, PaymentGateway $gateway, string $gatewaySlug)
    {
        $result = $this->paymentGatewayService->verifyGenericGatewayCharge($payment, $gateway);
        $message = $this->getStatusMessage($result['status']);

            if ($payment->order_id) {
            return $this->handleOrderPayment($payment, $result['status'], $message);
        }

        return $this->handleCartPayment($payment, $result['status'], $message, $gatewaySlug);
    }

    private function handleMockPayment(Payment $payment)
    {
        $snapshot = $payment->payload['checkout_snapshot'] ?? null;
        $order = null;

        if ($snapshot) {
            $order = $this->createOrderFromSnapshot($snapshot, $payment);
            if ($order) {
                $payment->order_id = $order->id;
            }
        }

        $payment->status = 'paid';
        $payment->save();
        $this->clearCart();

        if ($order) {
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', __('Payment successful'));
        }

        return redirect()->route('checkout.cancel')
            ->with('info', __('Payment processed (mock)'));
    }

    private function handleExplicitResult(Payment $payment, array $explicitResult)
    {
        if ($explicitResult['success'] === true) {
            return $this->handleSuccessfulPayment($payment);
        }

        if ($explicitResult['success'] === false) {
            $payment->status = 'failed';
            $payment->save();
            return redirect()->route('checkout.cancel')
                ->with('error', __('Payment failed or cancelled'));
        }

        if ($explicitResult['pending'] === true) {
            $payment->status = 'processing';
            $payment->save();
            return redirect()->route('checkout.cancel')
                ->with('info', __('Payment pending confirmation'));
        }

        return $this->processStandardReturn($payment, $this->getEnabledGateway('weaccept'), 'weaccept');
    }

    private function handleSuccessfulPayment(Payment $payment)
    {
        if (!$payment->order_id) {
            $snapshot = $payment->payload['checkout_snapshot'] ?? null;
            if ($snapshot) {
                $order = $this->createOrderFromSnapshot($snapshot, $payment);
                if ($order) {
                    $payment->order_id = $order->id;
                }
            }
        }

        $payment->status = 'paid';
        $payment->save();
        $this->clearCart();

        if ($payment->order_id) {
            return redirect()->route('checkout.success', ['order' => $payment->order_id])
                ->with('success', __('Payment successful'));
        }

        return redirect()->route('checkout.cancel')
            ->with('success', __('Payment successful'));
    }

    private function handleOrderPayment(Payment $payment, string $status, string $message)
    {
        if ($status === 'paid') {
            $this->clearCart();
            return redirect()->route('checkout.success', ['order' => $payment->order_id])
                ->with('success', $message);
        }

        $level = $status === 'failed' ? 'error' : ($status === 'paid' ? 'success' : 'info');
        return redirect()->route('orders.show', $payment->order_id)
            ->with($level, $message);
    }

    private function handleCartPayment(Payment $payment, string $status, string $message, string $gatewaySlug)
    {
        $this->restoreCartFromSnapshot($payment, $gatewaySlug);
        
        $level = $status === 'failed' ? 'error' : ($status === 'paid' ? 'success' : 'info');
        return redirect()->route('checkout.cancel')->with($level, $message);
    }

    private function handleReturnError(Payment $payment, string $gatewaySlug, string $errorMessage)
    {
        if ($payment->order_id) {
            return redirect()->route('orders.show', $payment->order_id)
                ->with('error', __(ucfirst($gatewaySlug) . ' verification error'));
        }

        $this->restoreCartFromSnapshot($payment, $gatewaySlug);
        
        return view('payments.failure')
            ->with('order', null)
            ->with('payment', $payment)
            ->with('error_message', __(ucfirst($gatewaySlug) . ' verification error'));
    }

    private function getEnabledGateway(string $slug): ?PaymentGateway
    {
        return PaymentGateway::where('slug', $slug)->where('enabled', true)->first();
    }

    private function redirectWithError(Payment $payment, string $errorMessage)
    {
        if ($payment->order_id) {
            return redirect()->route('orders.show', $payment->order_id)
                ->with('error', __($errorMessage));
        }

        return redirect()->route('checkout.cancel')->with('error', __($errorMessage));
    }

    private function isMockReturn(Payment $payment): bool
    {
        return request()->boolean('mock') || !empty($payment->payload['weaccept_mock']);
    }

    private function getExplicitResult(): ?array
    {
        $successParam = request()->query('success');
        $pendingParam = request()->query('pending');
        
        if (is_null($successParam) && is_null($pendingParam)) {
            return null;
        }

        return [
            'success' => filter_var($successParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'pending' => filter_var($pendingParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
        ];
    }

    private function getStatusMessage(string $status): string
    {
        return match ($status) {
                'paid' => 'Payment successful',
                'failed' => 'Payment failed',
                default => 'Payment pending confirmation'
            };
    }

    private function createOrderFromSnapshot(array $snapshot, Payment $payment): ?Order
    {
        try {
            return DB::transaction(function () use ($snapshot, $payment) {
                $order = Order::create([
                    'user_id' => $snapshot['user_id'] ?? null,
                    'status' => 'completed',
                    'total' => $snapshot['total'] ?? 0,
                    'items_subtotal' => $snapshot['total'] ?? 0,
                    'currency' => $snapshot['currency'] ?? config('app.currency', 'USD'),
                    'shipping_address' => $snapshot['shipping_address'] ?? null,
                    'payment_method' => $payment->method,
                    'payment_status' => 'paid',
                ]);

                foreach ($snapshot['items'] ?? [] as $item) {
                    if (empty($item['product_id'])) {
                        continue;
                    }
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'name' => $item['name'] ?? null,
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                    ]);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            Log::error('order.creation.error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function restoreCartFromSnapshot(Payment $payment, string $gatewaySlug): void
    {
        $snapshot = $payment->payload['checkout_snapshot'] ?? null;
        
        if (!$snapshot || empty($snapshot['items'])) {
            return;
        }

                $cart = [];
        foreach ($snapshot['items'] as $item) {
            if (empty($item['product_id'])) {
                        continue;
                    }
            $cart[$item['product_id']] = [
                'qty' => $item['qty'] ?? 1,
                'price' => $item['price'] ?? 0
            ];
        }

        if (!empty($cart)) {
            session(["{$gatewaySlug}_pending_cart" => $cart]);
        }
    }

    private function clearCart(): void
    {
        try {
            session()->forget('cart');
        } catch (\Throwable $e) {
            // Ignore cart clearing errors
        }
    }
}