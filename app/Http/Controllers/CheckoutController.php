<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Events\OrderPaid;
use App\Models\Coupon;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\CheckoutViewBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Nafezly\Payments\Factories\PaymentFactory;

class CheckoutController extends Controller
{
    /**
     * Show checkout form
     */
    public function showForm()
    {
        $cart = session()->get('cart', []);
        try {
            $this->validateCartNotEmpty($cart);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
        $vm = app(CheckoutViewBuilder::class)->build(
            $cart,
            GlobalHelper::getCurrencyContext()['currentCurrency']->id,
            session('applied_coupon_id'),
            Auth::user()
        );

        if (! $vm['coupon'] && session()->has('applied_coupon_id')) {
            session()->forget('applied_coupon_id');
        }

        // Enabled payment gateways based on required env keys
        $vm['gateways'] = $this->allowedGateways();

        return view('front.checkout.index', $vm);
    }

    /**
     * Handle checkout form submission
     */
    public function submitForm(CheckoutRequest $request)
    {
        $cart = session()->get('cart', []);
        try {
            $this->validateCartNotEmpty($cart);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        // Calculate totals
        $subtotal = $this->computeSubtotal($cart);
        $discount = $this->computeDiscount($subtotal);
        $shippingPrice = (float) ($request->shipping_price ?? 0);
        $total = $subtotal + $shippingPrice - $discount;

        $gateway = $request->gateway;
        $allowed = array_keys($this->allowedGateways());
        if (!in_array($gateway, $allowed, true)) {
            return back()->with('error', __('Selected payment method is not available'));
        }

        if ($gateway === 'cod') {
            // Cash on Delivery - create order directly
            try {
                $order = $this->createOrder($request, $cart, $total, $subtotal, $shippingPrice, $discount, 'cod', 'paid');

                // Create payment record for COD
                Payment::create([
                    'order_id' => $order->id,
                    'payment_id' => null,
                    'amount' => $order->total,
                    'currency' => $order->currency,
                    'method' => 'cod',
                    'status' => 'completed',
                    'data' => null,
                ]);

                session()->forget('cart');
                return redirect()->route('orders.show', $order)->with('success', __('Order created successfully.'));
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        } else {
            // Online payment - create pending order and redirect to payment
            try {
                $order = $this->createOrder($request, $cart, $total, $subtotal, $shippingPrice, $discount, $gateway, 'pending');
                session()->put('pending_order_id', $order->id);

                // Redirect to payment gateway
                return $this->redirectToPayment($gateway, $order);
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
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
                'payment_method' => $data['payment_method'] ?? 'cod',
                'payment_status' => 'paid',
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

            return response()->json(['order_id' => $order->id], 201);
        });
    }

    /** Helpers extracted to reduce complexity and duplication */
    private function validateCartNotEmpty(array $cart): void
    {
        if (empty($cart)) {
            throw new \Exception(__('Your cart is empty'));
        }
    }

    private function createOrder(Request $request, array $cart, float $total, float $subtotal, float $shippingPrice, float $discount, string $paymentMethod = 'cod', string $paymentStatus = 'paid'): Order
    {
        $validated = $request->validated();
        $user = $request->user();

        return DB::transaction(function () use ($user, $validated, $cart, $total, $subtotal, $shippingPrice, $discount, $paymentMethod, $paymentStatus) {
            $currencyContext = GlobalHelper::getCurrencyContext();
            $currentCurrency = $currencyContext['currentCurrency'];
            $orderCurrency = $currentCurrency ? $currentCurrency->code : config('app.currency', 'USD');

            // Resolve readable shipping parts for legacy JSON column
            $country = ($validated['country'] ?? null) ? Country::find($validated['country']) : null;
            $governorate = ($validated['governorate'] ?? null) ? Governorate::find($validated['governorate']) : null;
            $city = ($validated['city'] ?? null) ? City::find($validated['city']) : null;

            $shippingAddressPayload = [
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                // store both ids and readable names for robustness
                'country_id' => $validated['country'] ?? null,
                'governorate_id' => $validated['governorate'] ?? null,
                'city_id' => $validated['city'] ?? null,
                'country' => $country?->name,
                'governorate' => $governorate?->name,
                'city' => $city?->name,
            ];

            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'status' => 'pending',
                'total' => $total,
                'items_subtotal' => $subtotal,
                'shipping_price' => $shippingPrice,
                'discount' => $discount,
                'currency' => $orderCurrency,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'country_id' => $validated['country'] ?? null,
                'governorate_id' => $validated['governorate'] ?? null,
                'city_id' => $validated['city'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'shipping_zone_id' => $validated['shipping_zone_id'] ?? null,
                'shipping_estimated_days' => $validated['shipping_estimated_days'] ?? null,
            ]);

            // Persist normalized legacy payload for display paths expecting shipping_address only
            $order->shipping_address = $shippingAddressPayload;
            // Do not touch shipping_address_id here (requested to save address only in shipping_address column)
            $order->save();

            foreach ($cart as $productId => $row) {
                $product = Product::find($productId);
                if (! $product) continue;

                $qty = (int) ($row['qty'] ?? 1);
                $price = (float) ($row['price'] ?? 0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'qty' => $qty,
                    'price' => $price,
                    'purchased_at' => now(),
                ]);
            }

            return $order;
        });
    }

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

    /**
     * Determine enabled payment gateways by checking required env keys.
     * Returns [slug => label]. 'cod' is always enabled.
     */
    private function allowedGateways(): array
    {
        $map = [
            'cod' => ['label' => Lang::get('Cash on Delivery'), 'require' => []],
            'paypal' => ['label' => Lang::get('PayPal'), 'require' => ['PAYPAL_CLIENT_ID', 'PAYPAL_SECRET']],
            'stripe' => ['label' => Lang::get('Stripe'), 'require' => ['STRIPE_SECRET']],
            'tap' => ['label' => Lang::get('Tap'), 'require' => ['TAP_SECRET_KEY', 'TAP_PUBLIC_KEY']],
            'paymob' => ['label' => Lang::get('PayMob'), 'require' => ['PAYMOB_API_KEY', 'PAYMOB_INTEGRATION_ID', 'PAYMOB_HMAC']],
            'hyperpay' => ['label' => Lang::get('HyperPay'), 'require' => ['HYPERPAY_TOKEN']],
            'kashier' => ['label' => Lang::get('Kashier'), 'require' => ['KASHIER_ACCOUNT_KEY', 'KASHIER_TOKEN', 'KASHIER_IFRAME_KEY']],
            'fawry' => ['label' => Lang::get('Fawry'), 'require' => ['FAWRY_SECRET', 'FAWRY_MERCHANT']],
            'thawani' => ['label' => Lang::get('Thawani'), 'require' => ['THAWANI_API_KEY', 'THAWANI_PUBLISHABLE_KEY']],
            'opay' => ['label' => Lang::get('OPay'), 'require' => ['OPAY_SECRET_KEY', 'OPAY_PUBLIC_KEY', 'OPAY_MERCHANT_ID']],
            'paymob_wallet' => ['label' => Lang::get('PayMob Wallet'), 'require' => ['PAYMOB_WALLET_INTEGRATION_ID', 'PAYMOB_API_KEY']],
            'paytabs' => ['label' => Lang::get('PayTabs'), 'require' => ['PAYTABS_PROFILE_ID', 'PAYTABS_SERVER_KEY']],
            'binance' => ['label' => Lang::get('Binance'), 'require' => ['BINANCE_API', 'BINANCE_SECRET']],
            'nowpayments' => ['label' => Lang::get('NowPayments'), 'require' => ['NOWPAYMENTS_API_KEY']],
            'payeer' => ['label' => Lang::get('Payeer'), 'require' => ['PAYEER_MERCHANT_ID', 'PAYEER_API_KEY']],
            'perfect_money' => ['label' => Lang::get('Perfect Money'), 'require' => ['PERFECT_MONEY_ID', 'PERFECT_MONEY_PASSPHRASE']],
            'telr' => ['label' => Lang::get('Telr'), 'require' => ['TELR_MERCHANT_ID', 'TELR_API_KEY']],
            'clickpay' => ['label' => Lang::get('ClickPay'), 'require' => ['CLICKPAY_SERVER_KEY', 'CLICKPAY_PROFILE_ID']],
        ];

        $enabled = [];
        foreach ($map as $slug => $def) {
            $req = $def['require'] ?? [];
            $ok = true;
            foreach ($req as $var) {
                if (! env($var)) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                $enabled[$slug] = $def['label'];
            }
        }
        return $enabled;
    }

    private function redirectToPayment(string $gateway, Order $order)
    {
        $factory = new PaymentFactory();
        $payment = $factory->get(ucfirst($gateway));

        // Send full name for both first and last name
        $customerName = $order->customer_name ?? 'Customer';
        $customerEmail = $order->customer_email ?? 'customer@example.com';
        $customerPhone = $order->customer_phone ?? '0000000000';

        $response = $payment->pay(
            $order->total,
            $order->user_id,
            $customerName, // Full name as first name
            $customerName, // Full name as last name
            $customerEmail,
            $customerPhone,
            'order_' . $order->id
        );

        // Handle the payment response
        if (isset($response['redirect_url']) && $response['redirect_url']) {
            return redirect($response['redirect_url']);
        }

        if (isset($response['html']) && $response['html']) {
            return view('payment.form', ['html' => $response['html']]);
        }

        // If no redirect or html, return error
        return back()->with('error', 'Payment gateway error');
    }

    public function verifyPayment(Request $request, $payment = null)
    {
        $factory = new PaymentFactory();
        $paymentInstance = $factory->get(ucfirst($payment));

        $result = $paymentInstance->verify($request);

        if ($result['success']) {
            // Update order status to paid using session
            $orderId = session('pending_order_id');
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update(['payment_status' => 'paid']);

                    // Create payment record
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_id' => $result['payment_id'] ?? null,
                        'amount' => $order->total,
                        'currency' => $order->currency,
                        'method' => $payment,
                        'status' => 'completed',
                        'data' => $result['process_data'] ?? null,
                    ]);

                    // Fire OrderPaid event
                    event(new OrderPaid($order));

                    session()->forget('pending_order_id');
                    return redirect()->route('orders.show', $orderId)->with('success', $result['message']);
                }
            }
            // If order not found, redirect to home
            return redirect()->route('home')->with('error', 'Order not found');
        } else {
            return redirect()->route('checkout.form')->with('error', $result['message']);
        }
    }

    public function paymentWebhook(Request $request)
    {
        // Handle webhook if needed
        return response()->json(['status' => 'ok']);
    }
}
