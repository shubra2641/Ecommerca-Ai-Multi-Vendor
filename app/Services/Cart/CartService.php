<?php

namespace App\Services\Cart;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\RemoveFromCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\Cart\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartService
{
    public function __construct(
        private CurrencyService $currencyService
    ) {}

    public function getCartView(): View
    {
        $cart = $this->getCart();
        $items = $this->buildCartItems($cart);
        $totals = $this->calculateTotals($items);
        
        return view('front.cart.index', [
            'items' => $items,
            'total' => $totals['total'],
            'displayTotal' => $totals['displayTotal'],
            'currency_symbol' => $totals['currency_symbol'],
        ]);
    }

    public function addToCart(AddToCartRequest $request)
    {
        $data = $request->validated();
        $product = Product::findOrFail($data['product_id']);
        $qty = $data['qty'] ?? 1;
        
        if (!$this->validateStock($product, $qty, $data['variation_id'] ?? null)) {
            return $this->stockErrorResponse($request);
        }

        $this->addItemToCart($product, $qty, $data['variation_id'] ?? null);
        
        return $this->successResponse($request, __('Product added to cart'));
    }

    public function updateCart(UpdateCartRequest $request)
    {
        $data = $request->validated();
        $cart = $this->getCart();
        
        foreach ($data['lines'] as $line) {
            $this->updateCartItem($cart, $line);
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
        } elseif (!empty($data['product_id'])) {
            unset($cart[$data['product_id']]);
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

    private function buildCartItems(array $cart): array
    {
        $items = [];
        
        foreach ($cart as $key => $row) {
            $item = $this->buildCartItem($key, $row);
            if ($item) {
                $items[] = $item;
            }
        }
        
        return $items;
    }

    private function buildCartItem(string $key, array $row): ?array
    {
        $parts = explode(':', $key);
        $productId = (int) $parts[0];
        $variationId = isset($parts[1]) ? (int) $parts[1] : null;

        $product = Product::find($productId);
        if (!$product) {
            return null;
        }

        $variation = $this->getVariation($variationId, $product);
        $qty = (int) ($row['qty'] ?? 1);
        $price = $this->getItemPrice($variation, $product);
        $lineTotal = $price * $qty;

        return [
            'product' => $product,
            'variant' => $variation,
            'qty' => $qty,
            'price' => $price,
            'line_total' => $lineTotal,
            'cart_key' => $key,
        ];
    }

    private function getVariation(?int $variationId, Product $product): ?ProductVariation
    {
        if (!$variationId) {
            return null;
        }

        $variation = ProductVariation::find($variationId);
        return ($variation && $variation->product_id === $product->id) ? $variation : null;
    }

    private function getItemPrice(?ProductVariation $variation, Product $product): float
    {
        return $variation ? $variation->effectivePrice() : $product->effectivePrice();
    }

    private function calculateTotals(array $items): array
    {
        $total = array_sum(array_column($items, 'line_total'));
        $displayTotal = $this->currencyService->convertToDisplayCurrency($total);
        $currencySymbol = $this->currencyService->getCurrentCurrencySymbol();

        return [
            'total' => $total,
            'displayTotal' => $displayTotal,
            'currency_symbol' => $currencySymbol,
        ];
    }

    private function validateStock(Product $product, int $qty, ?int $variationId): bool
    {
        if ($variationId) {
            return $this->validateVariationStock($variationId, $product, $qty);
        }
        
        return $this->validateProductStock($product, $qty);
    }

    private function validateVariationStock(int $variationId, Product $product, int $qty): bool
    {
        $variation = ProductVariation::find($variationId);
        if (!$variation || $variation->product_id !== $product->id) {
            return false;
        }

        if (!$variation->manage_stock) {
            return true;
        }

        $available = max(0, (int) $variation->stock_qty - (int) $variation->reserved_qty);
        return $available >= $qty;
    }

    private function validateProductStock(Product $product, int $qty): bool
    {
        if (!$product->manage_stock) {
            return true;
        }

        $available = max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0));
        return $available >= $qty;
    }

    private function addItemToCart(Product $product, int $qty, ?int $variationId): void
    {
        $cart = $this->getCart();
        $key = $this->generateCartKey($product->id, $variationId);
        
        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $variation = $variationId ? ProductVariation::find($variationId) : null;
            $cart[$key] = [
                'qty' => $qty,
                'price' => $this->getItemPrice($variation, $product),
                'variant' => $variationId,
            ];
        }
        
        $this->putCart($cart);
    }

    private function generateCartKey(int $productId, ?int $variationId): string
    {
        return $variationId ? "{$productId}:{$variationId}" : (string) $productId;
    }

    private function updateCartItem(array &$cart, array $line): void
    {
        $key = (string) $line['cart_key'];
        if (!isset($cart[$key])) {
            return;
        }

        $requestedQty = (int) $line['qty'];
        $available = $this->getAvailableQuantity($key);
        
        $cart[$key]['qty'] = $available ? min($requestedQty, $available) : $requestedQty;
    }

    private function getAvailableQuantity(string $key): ?int
    {
        $parts = explode(':', $key);
        $productId = (int) $parts[0];
        $variationId = isset($parts[1]) ? (int) $parts[1] : null;

        $product = Product::find($productId);
        if (!$product) {
            return null;
        }

        if ($variationId) {
            return $this->getVariationAvailableQuantity($variationId, $product);
        }

        return $this->getProductAvailableQuantity($product);
    }

    private function getVariationAvailableQuantity(int $variationId, Product $product): ?int
    {
        $variation = ProductVariation::find($variationId);
        if (!$variation || $variation->product_id !== $product->id || !$variation->manage_stock) {
            return null;
        }

        return max(0, (int) $variation->stock_qty - (int) $variation->reserved_qty);
    }

    private function getProductAvailableQuantity(Product $product): ?int
    {
        if (!$product->manage_stock) {
            return null;
        }

        return max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0));
    }

    private function stockErrorResponse(Request $request)
    {
        $message = __('Requested quantity exceeds available stock');
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'cart_count' => count($this->getCart())
            ], 400);
        }

        return back()->with('error', $message);
    }

    private function successResponse(Request $request, string $message)
    {
        if ($request->has('buy_now')) {
            return redirect()->route('cart.index')->with('success', $message);
        }

        session()->flash('success', $message);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => $message,
                'count' => count($this->getCart()),
            ]);
        }

        return back()->with('success', $message);
    }
}
