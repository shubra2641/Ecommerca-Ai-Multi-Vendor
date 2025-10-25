<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Checkout\CheckoutProcessor;
use App\Services\CheckoutViewBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Show checkout form
     */
    public function showForm()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', __('Your cart is empty'));
        }
        $vm = app(CheckoutViewBuilder::class)->build(
            $cart,
            GlobalHelper::getCurrencyContext()['currentCurrency']->id,
            session('applied_coupon_id'),
            auth()->user()
        );

        if (! $vm['coupon'] && session()->has('applied_coupon_id')) {
            session()->forget('applied_coupon_id');
        }

        return view('front.checkout.index', $vm);
    }

    /**
     * Handle checkout form submission
     */
    public function submitForm(CheckoutRequest $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', __('Your cart is empty'));
        }

        // Calculate totals
        $subtotal = $this->computeSubtotal($cart);
        $discount = $this->computeDiscount($subtotal);

        try {
            $checkoutProcessor = app(CheckoutProcessor::class);
            $checkoutData = $checkoutProcessor->processCheckout($request, $cart, $request->validated(), $discount);
            $order = $checkoutProcessor->createOrder($checkoutData, $request);
            $paymentResult = $checkoutProcessor->processPayment($order, $checkoutData['gateway'], $request);

            return $this->handlePaymentResult($paymentResult, $request);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create order via API
     */
    public function create(CreateOrderRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $total = 0;
        $items = [];

        foreach ($data['items'] as $it) {
            $product = Product::findOrFail($it['product_id']);
            $qty = (int) $it['qty'];
            $price = $product->price ?? 0;
            $total += $price * $qty;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'variant' => $it['variant_id'] ?? null,
            ];
        }

        return DB::transaction(function () use ($user, $data, $total, $items) {
            $currencyContext = GlobalHelper::getCurrencyContext();
            $currentCurrency = $currencyContext['currentCurrency'];
            $orderCurrency = $currentCurrency ? $currentCurrency->code : config('app.currency', 'USD');

            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'status' => 'pending',
                'total' => $total,
                'items_subtotal' => $total,
                'currency' => $orderCurrency,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'name' => $item['product']->name,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'purchased_at' => now(),
                ]);
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => $order->payment_method,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);

            return response()->json(['order_id' => $order->id, 'payment_id' => $payment->id], 201);
        });
    }

    /**
     * Submit offline payment proof
     */
    public function submitOfflinePayment(SubmitOfflinePaymentRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $data = $request->validated();

        return DB::transaction(function () use ($order, $data) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'offline',
                'amount' => $data['amount'],
                'currency' => $order->currency,
                'status' => 'received',
                'payload' => ['note' => $data['note'] ?? null],
            ]);

            $order->payment_status = 'paid';
            $order->status = 'completed';
            $order->save();

            // Dispatch OrderPaid event to trigger stock deduction
            event(new OrderPaid($order));

            return response()->json(['ok' => true, 'payment_id' => $payment->id]);
        });
    }

    /**
     * Gateway callback
     */
    public function gatewayCallback(Request $request)
    {
        $data = $request->all();
        $orderId = $data['order_id'] ?? $request->query('order_id');
        $status = $data['status'] ?? 'failed';

        if (! $orderId) {
            return response()->json(['error' => 'order_id required'], 400);
        }

        $order = Order::find($orderId);
        if (! $order) {
            return response()->json(['error' => 'order not found'], 404);
        }

        if ($status === 'paid') {
            return $this->markOrderPaid($order, $data);
        }

        return response()->json(['ok' => false]);
    }

    /**
     * Start gateway payment
     */
    public function startGatewayPayment(StartGatewayPaymentRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $data = $request->validated();

        $gateway = $this->findEnabledGateway($data['gateway'] ?? null);
        if (! $gateway) {
            return response()->json(['error' => 'no_enabled_gateway'], 422);
        }

        if ($gateway->driver === 'offline') {
            return $this->startOfflinePayment($order, $gateway);
        }

        if ($gateway->driver === 'stripe') {
            return $this->startStripePayment($order, $gateway);
        }

        return response()->json(['error' => 'unsupported_gateway_driver'], 422);
    }

    /**
     * Stripe webhook handler
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload, true);

        if (! $event) {
            return response()->json(['error' => 'invalid_payload'], 400);
        }

        $type = $event['type'] ?? '';
        if ($type === 'checkout.session.completed') {
            $session = $event['data']['object'] ?? [];
            $this->handleStripeSessionCompleted($session);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Checkout success handler
     */
    public function checkoutSuccess(Request $request)
    {
        $orderId = $request->query('order');
        $order = $orderId ? Order::find($orderId) : null;

        if (! $order) {
            $this->restoreStripePendingCart();

            return view('payments.failure')
                ->with('order', null)
                ->with('payment', null)
                ->with('error_message', __('Payment was canceled. Your cart has been restored.'));
        }

        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        return redirect()->route('orders.show', $order)->with(
            'info',
            __('Payment completed but verification is pending. Check your order for updates.')
        );
    }

    /**
     * Checkout cancel handler
     */
    public function checkoutCancel(Request $request)
    {
        $orderId = $request->query('order');
        $order = $orderId ? Order::find($orderId) : null;

        if ($order) {
            if (auth()->id() !== $order->user_id) {
                abort(403);
            }

            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'cancelled';
            }
            if (! in_array($order->status, ['completed', 'refunded'])) {
                $order->status = 'cancelled';
            }
            $order->save();
        }

        $this->restoreStripePendingCart();

        $errorMessage = __('Payment was canceled. Your cart has been restored.');

        return view('payments.failure')
            ->with('order', $order)
            ->with('payment', null)
            ->with('error_message', $errorMessage);
    }

    /** Helpers extracted to reduce complexity and duplication */
    private function computeSubtotal(array $cart): float
    {
        $subtotal = 0.0;
        foreach ($cart as $pid => $row) {
            $product = Product::find($pid);
            if (! $product) {
                continue;
            }
            $qty = (int) ($row['qty'] ?? 0);
            $price = (float) ($row['price'] ?? 0);
            $subtotal += $price * $qty;
        }
        return $subtotal;
    }

    private function computeDiscount(float $subtotal): float
    {
        $discount = 0.0;
        $couponId = session('applied_coupon_id');
        if (! $couponId) {
            return $discount;
        }

        $coupon = Coupon::find($couponId);
        if ($coupon && $coupon->isValid($subtotal)) {
            if ($coupon->type === 'percentage') {
                $discount = $subtotal * ((float) $coupon->value) / 100.0;
            } else {
                $discount = (float) min((float) $coupon->value, $subtotal);
            }
        }
        return (float) $discount;
    }

    private function findEnabledGateway(?string $slug)
    {
        $query = PaymentGateway::query()->where('enabled', true);
        if (! empty($slug)) {
            $query->where('slug', $slug);
        }
        return $query->first();
    }

    private function startOfflinePayment(Order $order, PaymentGateway $gateway)
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'offline',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        return response()->json([
            'type' => 'offline',
            'payment_id' => $payment->id,
            'instructions' => $gateway->transfer_instructions,
            'requires_transfer_image' => $gateway->requires_transfer_image,
        ]);
    }

    private function startStripePayment(Order $order, PaymentGateway $gateway)
    {
        $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
        $secret = $stripeCfg['secret_key'] ?? null;
        $publishable = $stripeCfg['publishable_key'] ?? null;

        if (! $secret || ! $publishable) {
            return response()->json(['error' => 'stripe_not_configured'], 422);
        }

        try {
            if (! class_exists(\Stripe\Stripe::class)) {
                return response()->json(['error' => 'stripe_library_missing'], 500);
            }

            \Stripe\Stripe::setApiKey($secret);
            $currency = strtolower($order->currency ?? 'usd');

            $session = \Stripe\Checkout\Session::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency,
                            'product_data' => ['name' => 'Order #' . $order->id],
                            'unit_amount' => GlobalHelper::toCents($order->total ?? 0),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'success_url' => url('/checkout/success?order=' . $order->id),
                'cancel_url' => url('/checkout/cancel?order=' . $order->id),
                'metadata' => ['order_id' => $order->id],
            ]);

            $payment = Payment::where('order_id', $order->id)
                ->where('method', 'stripe')
                ->where('status', 'pending')
                ->first();

            if (! $payment) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'method' => 'stripe',
                    'amount' => $order->total,
                    'currency' => $order->currency,
                    'status' => 'pending',
                    'payload' => [],
                ]);
            }

            $payload = $payment->payload ? $payment->payload : [];
            $payload['stripe_session_id'] = $session->id;
            $payment->payload = $payload;
            $payment->save();

            return response()->json([
                'type' => 'stripe',
                'publishable_key' => $publishable,
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'stripe_error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleStripeSessionCompleted(array $session): void
    {
        $orderId = $session['metadata']['order_id'] ?? null;
        if (! $orderId) {
            return;
        }

        $order = Order::find($orderId);
        if (! $order) {
            return;
        }

        $payment = Payment::where('order_id', $order->id)
            ->where('method', 'stripe')
            ->where('status', 'pending')
            ->orderBy('id')
            ->first();

        $amount = GlobalHelper::fromCents($session['amount_total'] ?? $order->total * 100);
        $currency = strtolower($session['currency'] ?? $order->currency);
        $tx = $session['payment_intent'] ?? ($session['id'] ?? null);

        if ($payment) {
            $payment->status = 'completed';
            $payment->amount = $amount;
            $payment->currency = $currency;
            $payment->transaction_id = $tx;
            $payment->save();
        } else {
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'stripe',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'transaction_id' => $session['payment_intent'] ?? null,
                'payload' => $session,
            ]);
        }

        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->save();
    }

    private function restoreStripePendingCart(): void
    {
        if (session()->has('stripe_pending_cart')) {
            session()->put('cart', session('stripe_pending_cart'));
            session()->forget('stripe_pending_cart');
        }
    }

    private function markOrderPaid(Order $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => $data['method'] ?? 'gateway',
                'amount' => $data['amount'] ?? $order->total,
                'currency' => $data['currency'] ?? $order->currency,
                'status' => 'completed',
                'transaction_id' => $data['transaction_id'] ?? null,
                'payload' => $data,
            ]);

            $order->payment_status = 'paid';
            $order->status = 'completed';
            $order->save();

            // Dispatch OrderPaid event to trigger stock deduction
            event(new OrderPaid($order));

            return response()->json(['ok' => true]);
        });
    }

    /**
     * Handle payment result
     */
    private function handlePaymentResult(array $paymentResult, Request $request)
    {
        switch ($paymentResult['type']) {
            case 'redirect':
                session()->forget('cart');
                session()->flash('refresh_admin_notifications', true);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect_url' => $paymentResult['redirect_url'],
                        'payment_id' => $paymentResult['payment']?->id ?? null,
                    ]);
                }

                return redirect()->away($paymentResult['redirect_url']);

            case 'offline':
                session()->forget('cart');

                return redirect($paymentResult['redirect_url'])
                    ->with('success', __('Order created. Follow the payment instructions.'))
                    ->with('refresh_admin_notifications', true);

            case 'stripe':
                session()->put('stripe_pending_cart', session('cart'));
                session()->forget('cart');
                session()->flash('refresh_admin_notifications', true);

                return redirect()->away($paymentResult['redirect_url']);

            default:
                return back()->with('error', __('Unsupported payment method'));
        }
    }
}
