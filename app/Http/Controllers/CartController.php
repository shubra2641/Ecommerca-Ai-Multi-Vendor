<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\ApplyCouponRequest;
use App\Http\Requests\Cart\RemoveFromCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Requests\ProductIdRequest;
use App\Services\Cart\CartService;
use App\Services\Cart\CouponService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CouponService $couponService
    ) {}

    public function index()
    {
        return $this->cartService->getCartView();
    }

    public function add(AddToCartRequest $request)
    {
        return $this->cartService->addToCart($request);
    }

    public function update(UpdateCartRequest $request)
    {
        return $this->cartService->updateCart($request);
    }

    public function remove(RemoveFromCartRequest $request)
    {
        return $this->cartService->removeFromCart($request);
    }

    public function clear()
    {
        return $this->cartService->clearCart();
    }

    public function applyCoupon(ApplyCouponRequest $request)
    {
        return $this->couponService->applyCoupon($request);
    }

    public function removeCoupon(Request $request)
    {
        return $this->couponService->removeCoupon($request);
    }

    public function moveToWishlist(ProductIdRequest $request)
    {
        $data = $request->validated();
        $productId = $data['product_id'];
        
        // Remove from cart
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        }

        // Add to wishlist
        $user = $request->user();
        if ($user) {
            \App\Models\WishlistItem::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
        } else {
            $wishlist = session('wishlist', []);
            if (!in_array($productId, $wishlist, true)) {
                $wishlist[] = $productId;
                session(['wishlist' => $wishlist]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'moved' => true]);
        }

        return back()->with('success', __('Moved to wishlist'));
    }
}