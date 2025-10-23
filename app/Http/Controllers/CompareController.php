<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductIdRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    protected function getList(): array
    {
        return session('compare', []);
    }

    protected function save(array $list): void
    {
        session(['compare' => $list]);
    }

    public function toggle(ProductIdRequest $r)
    {
        $data = $r->validated();
        $list = $this->getList();
        if (in_array($data['product_id'], $list)) {
            $list = array_values(array_diff($list, [$data['product_id']]));
            $state = 'removed';
        } else {
            if (count($list) >= 4) { // limit compare items
                if ($r->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('Maximum items reached'),
                        'count' => count($list),
                    ], 422);
                }

                return back()->with('error', __('Maximum compare items reached'));
            }
            $list[] = $data['product_id'];
            $state = 'added';
        }
        $this->save($list);
        if ($r->wantsJson()) {
            return response()->json(['status' => 'ok', 'state' => $state, 'count' => count($list)]);
        }

        return back()->with('success', __('Compare list updated'));
    }

    public function index(Request $request)
    {
        $ids = $this->getList();
        $items = collect();
        if (! empty($ids)) {
            $items = Product::with(['category', 'brand'])->whereIn('id', $ids)->get();
        }
        $currency_symbol = config('app.currency_symbol', '$');

        return view('front.products.compare', [
            'items' => $items,
            'compareIds' => $ids,
            'wishlistIds' => session('wishlist', []),
            'currency_symbol' => $currency_symbol,
        ]);
    }
}
