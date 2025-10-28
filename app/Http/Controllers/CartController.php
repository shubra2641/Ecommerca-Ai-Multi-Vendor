<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\ApplyCouponRequest;
use App\Http\Requests\Cart\RemoveFromCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Requests\ProductIdRequest;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\WishlistItem;
use App\Services\CartViewBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class CartController
 *
 * Handles cart operations: listing, add/update/remove items, coupon handling
 * and wishlist moves.
 */
final class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        [$items, $total] = $this->buildCartItems($cart);
        [$coupon, $discount, $discounted_total] = $this->handleCoupon($total);
        [$displayTotal, $displayDiscount, $currency_symbol, $currentCurrency] = $this->handleCurrencyConversion($items, $total, $discount);

        // Build presentation items (variant labels, availability, sale percents) to remove inline Blade @php
        $cartVm = app(CartViewBuilder::class)->build($items, $currency_symbol);
        $presentedItems = $cartVm['items'];

        return view('front.cart.index', [
            'items' => $presentedItems,
            'rawItems' => $items,
            'total' => $total,
            'displayTotal' => $displayTotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'discounted_total' => $discounted_total,
            'displayDiscount' => $displayDiscount,
            'currency_symbol' => $currency_symbol,
            'currentCurrency' => $currentCurrency,
        ]);
    }

    public function add(AddToCartRequest $request)
    {
        $data = $request->validated();
        $product = Product::findOrFail($data['product_id']);
        $qty = $data['qty'] ?? 1;
        // enforce stock limits server-side
        $variation = null;
        if (! empty($data['variation_id'])) {
            $variation = ProductVariation::find($data['variation_id']);
            if ($variation && $variation->product_id === $product->id) {
                // determine available for variation
                if ($variation->manage_stock) {
                    $available = max(0, (int) $variation->stock_qty - (int) $variation->reserved_qty);
                    if ($available < $qty) {
                        if ($request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => __('Requested quantity exceeds available stock'),
                                'cart_count' => count($this->getCart()),
                            ], 400);
                        }

                        return back()->with('error', __('Requested quantity exceeds available stock'));
                    }
                }
            } else {
                $variation = null;
            }
        } else {
            // simple product availability
            if ($product->manage_stock) {
                $available = max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0));
                if ($available < $qty) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => __('Requested quantity exceeds available stock'),
                            'cart_count' => count($this->getCart()),
                        ], 400);
                    }

                    return back()->with('error', __('Requested quantity exceeds available stock'));
                }
            }
        }
        $cart = $this->getCart();
        // Use composite key productId[:variationId] so variants are separate lines
        $key = (string) $product->id;
        $variation = null;
        if (! empty($data['variation_id'])) {
            $variation = ProductVariation::find($data['variation_id']);
            if ($variation && $variation->product_id === $product->id) {
                $key .= ':' . $variation->id;
            } else {
                $variation = null;
            }
        }

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'qty' => $qty,
                'price' => $variation ? $variation->effectivePrice() : $product->effectivePrice(),
                // persist variant id if present so view rendering and checkout can resolve it
                'variant' => $variation ? $variation->id : null,
            ];
        }
        $this->putCart($cart);

        // If user clicked BUY NOW (we could add a hidden field), go directly to cart
        if ($request->has('buy_now')) {
            return redirect()
                ->route('cart.index')
                ->with('success', __('Product added to cart'));
        }

        // Always flash success into session so a subsequent full page reload will show server-side toast
        session()->flash('success', __('Product added to cart'));

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('Product added to cart'),
                'count' => count($this->getCart()),
            ]);
        }

        return back()->with('success', __('Product added to cart'));
    }

    public function update(UpdateCartRequest $request)
    {
        $data = $request->validated();
        $cart = $this->getCart();
        foreach ($data['lines'] as $line) {
            $key = (string) $line['cart_key'];
            if (! isset($cart[$key])) {
                continue;
            }
            $requested = (int) $line['qty'];
            // Determine product and variation from key: productId or productId:variationId
            $parts = explode(':', $key);
            $pid = (int) $parts[0];
            $vid = isset($parts[1]) ? (int) $parts[1] : null;
            $product = Product::find($pid);
            $available = null;
            if ($vid) {
                $variation = ProductVariation::find($vid);
                if ($variation && $variation->product_id === $product->id && $variation->manage_stock) {
                    $available = max(0, (int) $variation->stock_qty - (int) $variation->reserved_qty);
                }
            } else {
                if ($product && $product->manage_stock) {
                    $available = max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0));
                }
            }
            if (! is_null($available) && $requested > $available) {
                // clamp to available
                $cart[$key]['qty'] = $available;
            } else {
                $cart[$key]['qty'] = $requested;
            }
        }
        $this->putCart($cart);

        return redirect()->route('cart.index')->with('success', __('Cart updated'));
    }

    public function remove(RemoveFromCartRequest $request)
    {
        $data = $request->validated();
        $cart = $this->getCart();
        if (! empty($data['cart_key'])) {
            unset($cart[$data['cart_key']]);
        } elseif (! empty($data['product_id'])) {
            unset($cart[$data['product_id']]);
        }
        $this->putCart($cart);

        return redirect()->route('cart.index')->with('success', __('Item removed'));
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', __('Cart cleared'));
    }

    /**
     * Apply a coupon code to the cart.
     *
     * Recalculates the displayed total on the server using the selected currency
     * and validates the coupon against that value. A small tolerance is used to
     * allow minor rounding differences.
     */
    public function applyCoupon(ApplyCouponRequest $request)
    {
        $data = $request->validated();
        $code = strtoupper(trim($data['coupon']));
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            return back()->with('error', __('Invalid coupon code'));
        }

        $cart = $this->getCart();
        $total = 0;
        foreach ($cart as $row) {
            $total += $row['price'] * $row['qty'];
        }

        $serverDisplayedTotal = $this->calculateServerDisplayedTotal($total);

        if (! $this->isCouponValidForTotal($coupon, $serverDisplayedTotal)) {
            return back()->with('error', __('Coupon is not valid or expired'));
        }

        session(['applied_coupon_id' => $coupon->id]);

        [$displayTotal, $discounted_display, $discount_display, $currency_symbol] = $this->calculateDisplayTotals($total, $coupon);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('Coupon applied'),
                'coupon' => $coupon->code,
                'displayTotal' => $displayTotal,
                'discountedTotal' => $discounted_display,
                'discount' => $discount_display,
                'currency_symbol' => $currency_symbol,
            ]);
        }

        return back()->with('success', __('Coupon applied'));
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('applied_coupon_id');

        // recompute totals for AJAX clients
        $cart = $this->getCart();
        $total = 0;
        foreach ($cart as $row) {
            $total += $row['price'] * $row['qty'];
        }
        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        $displayTotal = GlobalHelper::convertCurrency($total, $defaultCurrency, $currentCurrency, 2);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('Coupon removed'),
                'displayTotal' => $displayTotal,
                'discountedTotal' => $displayTotal,
                'discount' => 0,
                'currency_symbol' => $currencyContext['currencySymbol'],
            ]);
        }

        return back()->with('success', __('Coupon removed'));
    }

    public function moveToWishlist(ProductIdRequest $request)
    {
        $data = $request->validated();
        $pid = $data['product_id'];
        // remove from cart
        $cart = $this->getCart();
        if (isset($cart[$pid])) {
            unset($cart[$pid]);
            $this->putCart($cart);
        }

        // add to wishlist via controller logic
        $user = $request->user();

        if ($user) {
            WishlistItem::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $pid,
            ]);
        } else {
            $list = session('wishlist', []);

            if (! in_array($pid, $list, true)) {
                $list[] = $pid;
                session(['wishlist' => $list]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'moved' => true]);
        }

        return back()->with('success', __('Moved to wishlist'));
    }

    protected function getCart(): array
    {
        return session()->get('cart', []);
    }

    protected function putCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    private function buildCartItems($cart): array
    {
        $items = [];
        $total = 0;
        foreach ($cart as $key => $row) {
            $parts = explode(':', (string) $key);
            $pid = (int) $parts[0];
            $vid = isset($parts[1]) ? (int) $parts[1] : null;

            $product = Product::find($pid);
            if (! $product) {
                continue;
            }

            $variation = null;
            if ($vid) {
                $variation = ProductVariation::find($vid);
                if ($variation && $variation->product_id !== $product->id) {
                    $variation = null;
                }
            }

            $qty = isset($row['qty']) ? (int) $row['qty'] : 1;
            $price = $row['price'] ?? ($variation ? $variation->effectivePrice() : $product->effectivePrice());
            $lineTotal = $price * $qty;
            $total += $lineTotal;

            $variantLabel = $this->buildVariantLabel($variation);
            $available = $this->calculateAvailableStock($product, $variation);
            $onSale = ($product->sale_price ?? null) && ($product->sale_price < ($product->price ?? 0));
            $salePercent = $onSale && $product->price ? (int) round(($product->price - $product->sale_price) / $product->price * 100) : null;

            $items[] = [
                'product' => $product,
                'variant' => $variation,
                'variant_label' => $variantLabel,
                'qty' => $qty,
                'price' => $price,
                'line_total' => $lineTotal,
                'cart_key' => $key,
                'available' => $available,
                'on_sale' => $onSale,
                'sale_percent' => $salePercent,
                'original_price' => $product->price,
                'display_original_price' => GlobalHelper::convertCurrency($product->price),
                'stock_display' => ($product->stock_qty ?? null) !== null ?
                    (($product->stock_qty ?? 0) > 0 ? $product->stock_qty . ' in stock' : 'Out of stock') : null,
                'seller_name' => method_exists($product, 'seller') && $product->seller ? $product->seller->name : null,
                'short_desc' => $product->short_description ? Str::limit($product->short_description, 120) : null,
            ];
        }

        return [$items, $total];
    }

    private function buildVariantLabel($variation)
    {
        if (! $variation) {
            return null;
        }
        $variantLabel = $variation->name ?? null;
        if (! $variantLabel && ! empty($variation->attribute_data)) {
            try {
                $variantLabel = collect($variation->attribute_data)
                    ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
                    ->values()
                    ->join(', ');
            } catch (\Throwable $e) {
                $variantLabel = null;
            }
        }

        return $variantLabel;
    }

    private function calculateAvailableStock($product, $variation)
    {
        if ($variation && $variation->manage_stock) {
            return max(0, (int) $variation->stock_qty - (int) $variation->reserved_qty);
        }
        if ($product->manage_stock) {
            return max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0));
        }

        return null;
    }

    private function handleCoupon($total): array
    {
        $coupon = null;
        $discount = 0;
        $discounted_total = $total;
        if (session()->has('applied_coupon_id')) {
            $coupon = Coupon::find(session('applied_coupon_id'));
            if (! $coupon || ! $coupon->isValidForTotal($total)) {
                session()->forget('applied_coupon_id');
                $coupon = null;
            } else {
                $discounted_total = $coupon->applyTo($total);
                $discount = round($total - $discounted_total, 2);
            }
        }

        return [$coupon, $discount, $discounted_total];
    }

    private function handleCurrencyConversion($items, $total, $discount): array
    {
        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        $currency_symbol = $currencyContext['currencySymbol'];

        try {
            $displayTotal = GlobalHelper::convertCurrency($total, $defaultCurrency, $currentCurrency, 2);
            foreach ($items as &$it) {
                $it['display_price'] = GlobalHelper::convertCurrency($it['price'], $defaultCurrency, $currentCurrency, 2);
                $it['display_line_total'] = GlobalHelper::convertCurrency($it['line_total'], $defaultCurrency, $currentCurrency, 2);
            }
        } catch (Throwable $e) {
            $displayTotal = $total;
            foreach ($items as &$it) {
                $it['display_price'] = $it['price'];
                $it['display_line_total'] = $it['line_total'];
            }
        }

        $displayDiscount = GlobalHelper::convertCurrency($discount, $defaultCurrency, $currentCurrency, 2);

        return [$displayTotal, $displayDiscount, $currency_symbol, $currentCurrency];
    }

    private function calculateServerDisplayedTotal($total)
    {
        return GlobalHelper::convertCurrency($total);
    }

    private function isCouponValidForTotal($coupon, $serverDisplayedTotal)
    {
        $tolerance = 0.01;
        $candidates = [
            $serverDisplayedTotal - $tolerance,
            $serverDisplayedTotal,
            $serverDisplayedTotal + $tolerance,
        ];

        foreach ($candidates as $candidate) {
            if ($coupon->isValidForTotal($candidate)) {
                return true;
            }
        }

        return false;
    }

    private function calculateDisplayTotals($total, $coupon)
    {
        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        $currency_symbol = $currencyContext['currencySymbol'];

        try {
            $displayTotal = GlobalHelper::convertCurrency($total, $defaultCurrency, $currentCurrency, 2);
            $discounted_display = GlobalHelper::convertCurrency($coupon->applyTo($total), $defaultCurrency, $currentCurrency, 2);
        } catch (Throwable $e) {
            $displayTotal = $total;
            $discounted_display = $coupon->applyTo($total);
        }

        $discount_display = round($displayTotal - $discounted_display, 2);

        return [$displayTotal, $discounted_display, $discount_display, $currency_symbol];
    }
}
