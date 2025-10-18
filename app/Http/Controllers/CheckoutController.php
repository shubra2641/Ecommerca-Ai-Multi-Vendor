<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Checkout\CheckoutProcessor;
use App\Services\SerialAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        $vm = app(\App\Services\CheckoutViewBuilder::class)->build(
            $cart,
            session('currency_id'),
            session('applied_coupon_id'),
            auth()->user()
        );
        
        // Remove invalid coupon id if builder invalidated it
        if (!$vm['coupon'] && session()->has('applied_coupon_id')) {
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

        try {
            // Process checkout using the new service
            $checkoutProcessor = app(CheckoutProcessor::class);
            $checkoutData = $checkoutProcessor->processCheckout($request, $cart, $request->validated());

            // Create order
            $order = $checkoutProcessor->createOrder($checkoutData, $request);

            // Process payment
            $paymentResult = $checkoutProcessor->processPayment($order, $checkoutData['gateway'], $request, $checkoutData['couponData']['coupon']);

            // Handle different payment types
            return $this->handlePaymentResult($paymentResult, $request);

        } catch (\Exception $e) {
            logger()->error('Checkout processing failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle payment result based on payment type
     */
    private function handlePaymentResult(array $paymentResult, Request $request)
    {
        switch ($paymentResult['type']) {
            case 'redirect':
                // Clear cart for redirect payments
                session()->forget('cart');
                session()->flash('refresh_admin_notifications', true);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect_url' => $paymentResult['redirect_url'],
                        'payment_id' => $paymentResult['payment']?->id ?? null
                    ]);
                }
                
                return redirect()->away($paymentResult['redirect_url']);

            case 'offline':
                // Clear cart for offline payments
                session()->forget('cart');
                
                return redirect($paymentResult['redirect_url'])
                    ->with('success', __('Order created. Follow the payment instructions.'))
                    ->with('refresh_admin_notifications', true);

            case 'stripe':
                // Store cart backup for Stripe
                session()->put('stripe_pending_cart', session('cart'));
                session()->forget('cart');
                session()->flash('refresh_admin_notifications', true);
                
                return redirect()->away($paymentResult['redirect_url']);

            default:
                return back()->with('error', __('Unsupported payment method'));
        }
    }

    /**
     * Create order via API
     */
    public function create(\App\Http\Requests\CreateOrderRequest $request)
    {
        $data = $request->validated();
        $shippingData = $request->only([
            'shipping_zone_id', 'shipping_price', 'shipping_country', 'shipping_governorate', 'shipping_city',
        ]);

        $user = $request->user();
        $total = 0;
        $items = [];

        foreach ($data['items'] as $it) {
            $product = \App\Models\Product::findOrFail($it['product_id']);
            $qty = (int) $it['qty'];
            $price = $product->price ?? 0;
            $total += $price * $qty;
            
            $committedNow = false;
            $isBackorder = false;
            $variantModel = null;
            $variantId = $it['variant_id'] ?? null;
            
            if ($variantId) {
                $variantModel = \App\Models\ProductVariation::find($variantId);
                if (!$variantModel || $variantModel->product_id !== $product->id) {
                    abort(422, __('Invalid variant'));
                }
            }

            // Handle stock management
            if ($variantModel && $variantModel->manage_stock) {
                $available = $variantModel->stock_qty - $variantModel->reserved_qty;
                $isBackorder = ($available < $qty && $variantModel->backorder);
                if (!\App\Services\StockService::consumeVariation($variantModel, $qty)) {
                    abort(422, __('out_of_stock', ['name' => $product->name]));
                }
                $committedNow = true;
            } elseif ($product->manage_stock) {
                $available = $product->stock_qty - $product->reserved_qty;
                $isBackorder = ($available < $qty && $product->backorder);
                if (!\App\Services\StockService::consume($product, $qty)) {
                    abort(422, __('out_of_stock', ['name' => $product->name]));
                }
                $committedNow = true;
            }

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'committed_now' => $committedNow,
                'is_backorder' => $isBackorder,
                'variant' => $variantModel,
            ];
        }

        $verifiedShipping = $this->verifyShippingSelection($shippingData);
        $finalShippingPrice = $verifiedShipping['price'] ?? null;
        $finalShippingZoneId = $verifiedShipping['zone_id'] ?? null;
        $finalShippingEta = $verifiedShipping['estimated_days'] ?? null;
        $grand = $total + ($finalShippingPrice ?? 0);

        return DB::transaction(function () use ($user, $data, $total, $items, $grand, $finalShippingPrice, $finalShippingZoneId, $finalShippingEta) {
            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'status' => 'pending',
                'total' => $grand,
                'items_subtotal' => $total,
                'currency' => config('app.currency', 'USD'),
                'shipping_address' => null,
                'shipping_price' => $finalShippingPrice,
                'shipping_zone_id' => $finalShippingZoneId,
                'shipping_estimated_days' => $finalShippingEta,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
            ]);

            foreach ($items as $it) {
                $variant = $it['variant'] ?? null;
                $variantName = null;
                $variantId = null;
                
                if ($variant) {
                    if (is_object($variant)) {
                        $variantName = $variant->name ?? null;
                        $variantId = $variant->id ?? null;
                    } else {
                        try {
                            $v = \App\Models\ProductVariation::find($variant);
                            if ($v) {
                                $variantName = $v->name ?? null;
                                $variantId = $v->id;
                            }
                        } catch (\Exception $e) {
                            // Handle error silently
                        }
                    }
                }
                
                $itemName = $it['product']->name_translations['en'] ?? $it['product']->name ?? '';
                if ($variantName) {
                    $itemName = trim($itemName . ' - ' . $variantName);
                }
                
                $meta = null;
                if ($variantId || $variantName) {
                    $meta = ['variant_id' => $variantId, 'variant_name' => $variantName];
                }

                $purchasedAt = now();
                $refundDays = (int) ($it['product']->refund_days ?? 0);
                $refundExpires = $refundDays > 0 ? $purchasedAt->clone()->addDays($refundDays) : null;
                $commissionData = ['rate' => null, 'commission' => null, 'vendor_earnings' => null];
                
                if ($it['product']->vendor_id) {
                    $commissionData = \App\Services\CommissionService::breakdown($it['product'], (int) $it['qty'], (float) $it['price']);
                }
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['product']->id,
                    'sku' => $it['product']->sku ?? null,
                    'name' => $itemName,
                    'qty' => $it['qty'],
                    'price' => $it['price'],
                    'vendor_commission_rate' => $commissionData['rate'],
                    'vendor_commission_amount' => $commissionData['commission'],
                    'vendor_earnings' => $commissionData['vendor_earnings'],
                    'meta' => $meta,
                    'is_backorder' => $it['is_backorder'] ?? false,
                    'committed' => $it['committed_now'] ?? false,
                    'purchased_at' => $purchasedAt,
                    'refund_expires_at' => $refundExpires,
                    'restocked' => false,
                ]);
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => $order->payment_method,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);

            // Notify admins about created payment
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminPaymentNotification($payment, 'created'));
                }
            } catch (\Throwable $e) {
                logger()->warning('Admin payment notification failed: ' . $e->getMessage());
            }

            // Notify admins and user
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNewOrderNotification($order));
                }
                if ($order->user) {
                    $order->user->notify(new \App\Notifications\UserOrderCreatedNotification($order));
                }
            } catch (\Throwable $e) {
                logger()->warning('Order notification failed: ' . $e->getMessage());
            }

            return response()->json(['order_id' => $order->id, 'payment_id' => $payment->id], 201);
        });
    }

    /**
     * Submit offline payment proof
     */
    public function submitOfflinePayment(\App\Http\Requests\SubmitOfflinePaymentRequest $request, $orderId)
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

            // Notify admins that an offline payment proof was submitted
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminPaymentNotification($payment, 'received'));
                }
            } catch (\Throwable $e) {
                logger()->warning('Admin payment notification failed: ' . $e->getMessage());
            }

            // Handle optional transfer image
            if ($request->hasFile('transfer_image')) {
                $file = $request->file('transfer_image');
                $path = $file->store('payment-attachments', 'public');
                $payment->attachments()->create([
                    'path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'user_id' => $order->user_id,
                ]);
            }

            $order->payment_status = 'paid';
            $order->status = 'completed';
            $order->save();

            // Assign serials for order items if needed
            try {
                app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
            } catch (\Exception $e) {
                logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
            }

            return response()->json(['ok' => true, 'payment_id' => $payment->id]);
        });
    }

    /**
     * Generic gateway callback
     */
    public function gatewayCallback(Request $request)
    {
        $data = $request->all();
        $orderId = $data['order_id'] ?? $request->query('order_id');
        $status = $data['status'] ?? 'failed';

        if (!$orderId) {
            return response()->json(['error' => 'order_id required'], 400);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'order not found'], 404);
        }

        if ($status === 'paid') {
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

                try {
                    app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
                } catch (\Exception $e) {
                    logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
                }

                return response()->json(['ok' => true]);
            });
        }

        return response()->json(['ok' => false]);
    }

    /**
     * Start gateway payment
     */
    public function startGatewayPayment(\App\Http\Requests\StartGatewayPaymentRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $data = $request->validated();

        // Determine gateway
        $gatewayQuery = PaymentGateway::query()->where('enabled', true);
        if (!empty($data['gateway'])) {
            $gatewayQuery->where('slug', $data['gateway']);
        }
        $gateway = $gatewayQuery->first();
        if (!$gateway) {
            return response()->json(['error' => 'no_enabled_gateway'], 422);
        }

        if ($gateway->driver === 'offline') {
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

        if ($gateway->driver === 'stripe') {
            $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
            $secret = $stripeCfg['secret_key'] ?? null;
            $publishable = $stripeCfg['publishable_key'] ?? null;
            
            if (!$secret || !$publishable) {
                return response()->json(['error' => 'stripe_not_configured'], 422);
            }
            
            try {
                if (!class_exists(\Stripe\Stripe::class)) {
                    return response()->json(['error' => 'stripe_library_missing'], 500);
                }
                
                \Stripe\Stripe::setApiKey($secret);
                $currency = strtolower($order->currency ?? 'usd');
                
                $lineItems = [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => ['name' => 'Order #' . $order->id],
                        'unit_amount' => (int) round(($order->total ?? 0) * 100),
                    ],
                    'quantity' => 1,
                ]];
                
                $session = \Stripe\Checkout\Session::create([
                    'mode' => 'payment',
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'success_url' => url('/checkout/success?order=' . $order->id),
                    'cancel_url' => url('/checkout/cancel?order=' . $order->id),
                    'metadata' => ['order_id' => $order->id],
                ]);

                $payment = Payment::where('order_id', $order->id)
                    ->where('method', 'stripe')
                    ->where('status', 'pending')
                    ->first();
                    
                if (!$payment) {
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
                
                $payload = $payment->payload ?: [];
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

        return response()->json(['error' => 'unsupported_gateway_driver'], 422);
    }

    /**
     * Stripe webhook handler
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        $event = json_decode($payload, true);
        
        if (!$event) {
            return response()->json(['error' => 'invalid_payload'], 400);
        }

        if (($event['type'] ?? '') === 'checkout.session.completed') {
            $session = $event['data']['object'];
            $orderId = $session['metadata']['order_id'] ?? null;
            
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $gateway = PaymentGateway::where('driver', 'stripe')->where('enabled', true)->first();
                    
                    // Verify signature if webhook secret exists
                    if ($gateway && $gateway->stripe_webhook_secret && class_exists(\Stripe\Webhook::class)) {
                        try {
                            \Stripe\Webhook::constructEvent($payload, $sig, $gateway->stripe_webhook_secret);
                        } catch (\Exception $e) {
                            return response()->json(['error' => 'signature_verification_failed'], 400);
                        }
                    }

                    // Update existing pending payment
                    $payment = Payment::where('order_id', $order->id)
                        ->where('method', 'stripe')
                        ->where('status', 'pending')
                        ->orderBy('id')
                        ->first();
                        
                    $amount = ($session['amount_total'] ?? ($order->total * 100)) / 100;
                    
                    if ($payment) {
                        $payment->status = 'completed';
                        $payment->amount = $amount;
                        $payment->currency = strtolower($session['currency'] ?? $order->currency);
                        $payment->transaction_id = $session['payment_intent'] ?? ($session['id'] ?? null);
                        $payload = $payment->payload ?: [];
                        $payload['webhook_event'] = $session;
                        $payment->payload = $payload;
                        $payment->save();
                    } else {
                        Payment::create([
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'method' => 'stripe',
                            'amount' => $amount,
                            'currency' => strtolower($session['currency'] ?? $order->currency),
                            'status' => 'completed',
                            'transaction_id' => $session['payment_intent'] ?? null,
                            'payload' => $session,
                        ]);
                    }

                    $order->payment_status = 'paid';
                    $order->status = 'completed';
                    $order->save();

                    // Fire OrderPaid event
                    try {
                        event(new \App\Events\OrderPaid($order));
                    } catch (\Throwable $e) {
                        logger()->error('OrderPaid event dispatch failed for order ' . ($order->id ?? 'n/a') . ': ' . $e->getMessage());
                    }

                    try {
                        app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
                    } catch (\Exception $e) {
                        logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
                    }
                }
            }
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
        
        if (!$order) {
            // Restore cart backup
            if (session()->has('stripe_pending_cart')) {
                session()->put('cart', session('stripe_pending_cart'));
                session()->forget('stripe_pending_cart');
            } elseif (session()->has('tap_pending_cart')) {
                session()->put('cart', session('tap_pending_cart'));
                session()->forget('tap_pending_cart');
            }

            return view('payments.failure')
                ->with('order', null)
                ->with('payment', null)
                ->with('error_message', __('Payment was canceled. Your cart has been restored.'));
        }
        
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        // Verify payment status
        try {
            $payment = Payment::where('order_id', $order->id)->where('method', 'stripe')->where('status', 'pending')->first();
            if ($payment && !empty($payment->payload['stripe_session_id']) && class_exists(\Stripe\Checkout\Session::class)) {
                $gateway = PaymentGateway::where('driver', 'stripe')->where('enabled', true)->first();
                if ($gateway) {
                    $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
                    if (!empty($stripeCfg['secret_key'])) {
                        \Stripe\Stripe::setApiKey($stripeCfg['secret_key']);
                    }
                    $session = \Stripe\Checkout\Session::retrieve($payment->payload['stripe_session_id']);
                    if ($session && ($session->payment_status ?? '') === 'paid') {
                        $amount = ($session->amount_total ?? ($order->total * 100)) / 100;
                        $payment->status = 'completed';
                        $payment->amount = $amount;
                        $payment->transaction_id = $session->payment_intent ?? ($session->id ?? null);
                        $payload = $payment->payload ?: [];
                        $payload['session'] = $session;
                        $payment->payload = $payload;
                        $payment->save();

                        $order->payment_status = 'paid';
                        $order->status = 'processing';
                        $order->save();
                        event(new \App\Events\OrderPaid($order));

                        // Commit reserved stock
                        try {
                            $order->loadMissing('items.product');
                            foreach ($order->items as $item) {
                                $qty = (int) $item->qty;
                                $product = $item->product;
                                if (!$product) continue;
                                
                                $variantId = null;
                                if (is_array($item->meta) && !empty($item->meta['variant_id'])) {
                                    $variantId = $item->meta['variant_id'];
                                }
                                
                                if ($variantId) {
                                    $variation = \App\Models\ProductVariation::find($variantId);
                                    if ($variation) {
                                        \App\Services\StockService::commitVariation($variation, $qty);
                                    }
                                } else {
                                    \App\Services\StockService::commit($product, $qty);
                                }
                            }
                        } catch (\Exception $e) {
                            logger()->error('Stripe commit stock failed for order ' . $order->id . ': ' . $e->getMessage());
                        }

                        try {
                            app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
                        } catch (\Exception $e) {
                            logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
                        }

                        session()->forget('stripe_pending_cart');

                        return view('payments.success')
                            ->with('order', $order)
                            ->with('payment', $payment);
                    }
                }
            }
        } catch (\Exception $e) {
            logger()->warning('Stripe success verification failed for order ' . ($order->id ?? 'n/a') . ': ' . $e->getMessage());
        }

        return redirect()->route('orders.show', $order)->with('info', __('Payment completed but verification is pending. Check your order for updates.'));
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
            if (!in_array($order->status, ['completed', 'refunded'])) {
                $order->status = 'cancelled';
            }
            $order->save();
            
            // Mark pending payments as cancelled
            try {
                foreach ($order->payments()->whereIn('status', ['pending', 'processing']) as $p) {
                    $p->status = 'cancelled';
                    $p->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed cancelling payments for order ' . $order->id . ': ' . $e->getMessage());
            }
            
            // Fire cancellation event
            try {
                event(new \App\Events\OrderCancelled($order));
            } catch (\Throwable $e) {
                logger()->error('Order cancel event failed: ' . $e->getMessage());
            }

            // Restore cart backup
            if (session()->has('stripe_pending_cart')) {
                session()->put('cart', session('stripe_pending_cart'));
                session()->forget('stripe_pending_cart');
            }

            $payment = $order->payments()->whereIn('status', ['cancelled', 'failed'])->latest()->first();
            $errorMessage = __('Payment was canceled. Your cart has been restored.');

            return view('payments.failure')
                ->with('order', $order)
                ->with('payment', $payment)
                ->with('error_message', $errorMessage);
        }

        // No order present: restore cart backups
        if (session()->has('stripe_pending_cart')) {
            session()->put('cart', session('stripe_pending_cart'));
            session()->forget('stripe_pending_cart');
        }
        if (session()->has('tap_pending_cart')) {
            session()->put('cart', session('tap_pending_cart'));
            session()->forget('tap_pending_cart');
        }

        $errorMessage = __('Payment was canceled. Your cart has been restored.');

        return view('payments.failure')
            ->with('order', null)
            ->with('payment', null)
            ->with('error_message', $errorMessage);
    }

    /**
     * Verify shipping selection
     */
    private function verifyShippingSelection(array $shippingData): ?array
    {
        $zoneId = $shippingData['shipping_zone_id'] ?? null;
        $country = $shippingData['shipping_country'] ?? null;
        $gov = $shippingData['shipping_governorate'] ?? null;
        $city = $shippingData['shipping_city'] ?? null;

        if (!$country) return null;

        $resolver = new \App\Services\Shipping\ShippingResolver();
        $resolved = $resolver->resolve($country, $gov, $city, $zoneId);

        if (!$resolved) return null;

        return [
            'zone_id' => $resolved['zone_id'],
            'price' => $resolved['price'],
            'estimated_days' => $resolved['estimated_days'],
        ];
    }
}