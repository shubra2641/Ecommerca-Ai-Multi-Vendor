<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CheckoutViewBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        try {
            $order = $this->createOrder($request, $cart, $total, $subtotal, $shippingPrice, $discount);
            session()->forget('cart');
            return redirect()->route('orders.show', $order)->with('success', __('Order created successfully.'));
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
                'status' => 'completed',
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

    private function createOrder(Request $request, array $cart, float $total, float $subtotal, float $shippingPrice, float $discount): Order
    {
        $validated = $request->validated();
        $user = $request->user();

        return DB::transaction(function () use ($user, $validated, $cart, $total, $subtotal, $shippingPrice, $discount) {
            $currencyContext = GlobalHelper::getCurrencyContext();
            $currentCurrency = $currencyContext['currentCurrency'];
            $orderCurrency = $currentCurrency ? $currentCurrency->code : config('app.currency', 'USD');

            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'status' => 'completed',
                'total' => $total,
                'items_subtotal' => $subtotal,
                'shipping_price' => $shippingPrice,
                'discount' => $discount,
                'currency' => $orderCurrency,
                'payment_method' => 'cod', // Default to COD for now
                'payment_status' => 'paid',
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
}
