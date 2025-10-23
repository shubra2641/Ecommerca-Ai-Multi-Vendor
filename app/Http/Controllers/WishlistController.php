<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductIdRequest;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $ids = $this->getUserListIds($user) ?: $this->getSessionList();
        $items = Product::with('category')->whereIn('id', (array) $ids)->get();

        return view('front.products.wishlist', ['items' => $items, 'wishlistIds' => $ids, 'compareIds' => []]);
    }

    public function toggle(ProductIdRequest $r)
    {
        $data = $r->validated();
        $user = $r->user();
        $sessionList = $this->getSessionList();
        $state = 'added';
        if ($user) {
            $item = WishlistItem::where('user_id', $user->id)->where('product_id', $data['product_id'])->first();
            if ($item) {
                $item->delete();
                $state = 'removed';
            } else {
                WishlistItem::create(['user_id' => $user->id, 'product_id' => $data['product_id']]);
            }
            $count = WishlistItem::where('user_id', $user->id)->count();
        } else {
            if (in_array($data['product_id'], $sessionList)) {
                $sessionList = array_values(array_diff($sessionList, [$data['product_id']]));
                $state = 'removed';
            } else {
                $sessionList[] = $data['product_id'];
            }
            $this->saveSession($sessionList);
            $count = count($sessionList);
        }
        if ($r->wantsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => $state === 'added',
                'state' => $state,
                'message' => $state === 'added' ? __('Added to wishlist') : __('Removed from wishlist'),
                'count' => $count,
            ]);
        }

        return back()->with('success', __('Wishlist updated'));
    }

    protected function getSessionList(): array
    {
        return session('wishlist', []);
    }

    protected function saveSession(array $list): void
    {
        session(['wishlist' => $list]);
    }

    protected function getUserListIds($user)
    {
        if (! $user) {
            return [];
        }

        return WishlistItem::where('user_id', $user->id)->pluck('product_id')->all();
    }
}
