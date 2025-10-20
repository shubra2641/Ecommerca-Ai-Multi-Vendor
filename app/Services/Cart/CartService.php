<?php

namespace App\Services\Cart;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\RemoveFromCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartService
{
    public function getCartView(): View
    {
        $cart = $this->getCart();
        $items = $this->buildItems($cart);
        $total = $this->calculateTotal($items);
        
        return view('front.cart.index', [
            'items' => $items,
            'total' => $total,
            'displayTotal' => $total,
            'displayDiscount' => 0,
            'currency_symbol' => '$',
        ]);
    }

    public function addToCart(AddToCartRequest $request)
    {
        $data = $request->validated();
        $product = Product::findOrFail($data['product_id']);

        if (!$this->hasStock($product, $data['qty'] ?? 1, $data['variation_id'] ?? null)) {
            return $this->errorResponse($request, __('Out of stock'));
        }

        $this->addItem($product, $data['qty'] ?? 1, $data['variation_id'] ?? null);
        return $this->successResponse($request, __('Product added to cart'));
    }

    public function updateCart(UpdateCartRequest $request)
    {
        $data = $request->validated();
        $cart = $this->getCart();

        foreach ($data['lines'] as $line) {
            $cart[$line['cart_key']]['qty'] = $line['qty'];
        }

        $this->putCart($cart);
        return redirect()->route('cart.index')->with('success', __('Cart updated'));
    }

    public function removeFromCart(RemoveFromCartRequest $request)
    {
        $data = $request->validated();
        $cart = $this->getCart();

        if (!empty($data['cart_key'])) {
            unset($cart[$data['cart_key']]);
        }

        $this->putCart($cart);
        return redirect()->route('cart.index')->with('success', __('Item removed'));
    }

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', __('Cart cleared'));
    }

    private function getCart(): array
    {
        return session()->get('cart', []);
    }

    private function putCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    private function buildItems(array $cart): array
    {
        $items = [];
        
        foreach ($cart as $key => $row) {
            $product = Product::find(explode(':', $key)[0]);
            if ($product) {
                $lineTotal = $row['price'] * $row['qty'];
                $items[] = [
                    'product' => $product,
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'display_price' => $row['price'],
                    'line_total' => $lineTotal,
                    'display_line_total' => $lineTotal,
                    'cart_key' => $key,
                    'available' => $this->getAvailableStock($product, $key),
                ];
            }
        }
        
        return $items;
    }

    private function getAvailableStock(Product $product, string $key): ?int
    {
        if (!$product->manage_stock) {
            return null;
        }

        $parts = explode(':', $key);
        if (isset($parts[1])) {
            $variation = ProductVariation::find($parts[1]);
            if ($variation && $variation->manage_stock) {
                return max(0, (int) $variation->stock_qty - (int) $variation->reserved_qty);
            }
        }

        return max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0));
    }

    private function calculateTotal(array $items): float
    {
        return array_sum(array_column($items, 'line_total'));
    }

    private function hasStock(Product $product, int $qty, ?int $variationId): bool
    {
        if ($variationId) {
            $variation = ProductVariation::find($variationId);
            return $variation && $variation->product_id === $product->id;
        }

        return true;
    }

    private function addItem(Product $product, int $qty, ?int $variationId): void
    {
        $cart = $this->getCart();
        $key = $variationId ? "{$product->id}:{$variationId}" : (string) $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'qty' => $qty,
                'price' => $product->effectivePrice(),
            ];
        }

        $this->putCart($cart);
    }

    private function errorResponse(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 400);
        }
        return back()->with('error', $message);
    }

    private function successResponse(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'message' => $message]);
        }
        return back()->with('success', $message);
    }
}
