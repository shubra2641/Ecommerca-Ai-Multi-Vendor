<?php

namespace App\Services\Checkout;

use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Order;
use App\Services\Payments\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Handle offline payment creation
     */
    public function createOfflinePayment(Order $order, Request $request): Payment
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'offline',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        // Handle transfer image upload
        $this->handleTransferImageUpload($payment, $request);

        return $payment;
    }

    /**
     * Handle Stripe payment creation
     */
    public function createStripePayment(Order $order, PaymentGateway $gateway, $coupon = null): array
    {
        $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
        $secret = $stripeCfg['secret_key'] ?? null;
        $publishable = $stripeCfg['publishable_key'] ?? null;

        if (!$secret || !$publishable || !class_exists(\Stripe\Stripe::class)) {
            throw new \Exception(__('Stripe not configured'));
        }

        \Stripe\Stripe::setApiKey($secret);

        // Build Stripe checkout session
        $builder = new \App\Services\Stripe\StripeCheckoutBuilder($order, $coupon);
        $payload = $builder->build();

        // Handle Stripe coupon creation if needed
        $this->handleStripeCoupon($coupon, $order, $secret, $payload);

        // Create checkout session
        $session = \Stripe\Checkout\Session::create($payload);

        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'stripe',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
            'payload' => ['stripe_session_id' => $session->id],
        ]);

        return [
            'payment' => $payment,
            'session' => $session,
            'redirect_url' => $session->url
        ];
    }

    /**
     * Handle external gateway payments (PayPal, Tap, etc.)
     */
    public function createExternalGatewayPayment(Order $order, PaymentGateway $gateway, array $snapshot): array
    {
        $svc = app(PaymentGatewayService::class);
        $initKey = $gateway->driver ?? $gateway->slug;

        Log::info('gateway.init.start', [
            'driver' => $gateway->driver,
            'gateway_id' => $gateway->id,
            'snapshot_items' => count($snapshot['items'] ?? []),
        ]);

        $result = match ($initKey) {
            'paypal' => $svc->initPayPalFromSnapshot($snapshot, $gateway),
            'tap' => $svc->initTapFromSnapshot($snapshot, $gateway),
            'paytabs' => $svc->initPaytabsFromSnapshot($snapshot, $gateway),
            'weaccept' => $svc->initWeacceptFromSnapshot($snapshot, $gateway),
            'payeer' => $svc->initPayeerFromSnapshot($snapshot, $gateway),
            default => $this->handleGenericGateway($svc, $snapshot, $gateway)
        };

        Log::info('gateway.init.success', [
            'driver' => $gateway->driver,
            'payment_id' => $result['payment']->id ?? null,
        ]);

        return $result;
    }

    /**
     * Handle generic gateway initialization
     */
    private function handleGenericGateway($svc, array $snapshot, PaymentGateway $gateway): array
    {
        if (method_exists($svc, 'initGenericRedirectGatewayFromSnapshot')) {
            return $svc->initGenericRedirectGatewayFromSnapshot($snapshot, $gateway, $gateway->slug);
        }

        throw new \RuntimeException('Unsupported redirect gateway: ' . $gateway->driver);
    }

    /**
     * Handle Stripe coupon creation
     */
    private function handleStripeCoupon($coupon, Order $order, string $secret, array &$payload): void
    {
        if (!$coupon || !empty($coupon->stripe_coupon_id) || !class_exists(\Stripe\Coupon::class)) {
            return;
        }

        try {
            $stripeCoupon = null;

            if ($coupon->type === 'percent' && $coupon->value > 0) {
                $stripeCoupon = \Stripe\Coupon::create([
                    'percent_off' => (float) $coupon->value,
                    'duration' => 'once',
                    'name' => $coupon->code,
                ]);
            } elseif ($coupon->type === 'fixed' && $coupon->value > 0) {
                $stripeCoupon = \Stripe\Coupon::create([
                    'amount_off' => (int) round($coupon->value * 100),
                    'currency' => strtolower($order->currency ?? 'usd'),
                    'duration' => 'once',
                    'name' => $coupon->code,
                ]);
            }

            if ($stripeCoupon && !empty($stripeCoupon->id)) {
                $coupon->stripe_coupon_id = $stripeCoupon->id;
                $coupon->save();
                $payload['metadata']['stripe_coupon_id'] = $stripeCoupon->id;
            }
        } catch (\Exception $e) {
            logger()->warning('Failed creating stripe coupon for local coupon ' . ($coupon->id ?? 'n/a') . ': ' . $e->getMessage());
        }
    }

    /**
     * Handle transfer image upload
     */
    private function handleTransferImageUpload(Payment $payment, Request $request): void
    {
        try {
            if ($request->hasFile('transfer_image') && $request->file('transfer_image')->isValid()) {
                $file = $request->file('transfer_image');
                $path = $file->store('payments', 'public');
                
                \App\Models\PaymentAttachment::create([
                    'payment_id' => $payment->id,
                    'path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'user_id' => $payment->user_id,
                ]);
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed storing transfer image for payment ' . ($payment->id ?? 'n/a') . ': ' . $e->getMessage());
        }
    }

    /**
     * Process Payeer payment
     */
    public function createPayeerPayment(Order $order, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $merchant = $cfg['payeer_merchant_id'] ?? null;
        $secret = $cfg['payeer_secret_key'] ?? null;
        $currency = strtoupper($order->currency ?? 'USD');

        if (!$merchant || !$secret) {
            throw new \Exception('Missing merchant credentials');
        }

        return DB::transaction(function () use ($order, $merchant, $secret, $currency) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'payeer',
                'amount' => $order->total,
                'currency' => $currency,
                'status' => 'pending',
                'payload' => ['order_reference' => $order->id],
            ]);

            $m_shop = $merchant;
            $m_orderid = $payment->id;
            $m_amount = number_format($order->total, 2, '.', '');
            $m_curr = $currency;
            $m_desc = base64_encode('Order #' . $order->id);
            $sign = strtoupper(hash('sha256', implode(':', [$m_shop, $m_orderid, $m_amount, $m_curr, $m_desc, $secret])));

            return [
                'payment' => $payment,
                'form_data' => [
                    'm_shop' => $m_shop,
                    'm_orderid' => $m_orderid,
                    'm_amount' => $m_amount,
                    'm_curr' => $m_curr,
                    'm_desc' => $m_desc,
                    'm_sign' => $sign,
                ]
            ];
        });
    }
}
