<?php

namespace App\Services\Cart;

use App\Http\Requests\Cart\ApplyCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponService
{
    public function applyCoupon(ApplyCouponRequest $request)
    {
        $data = $request->validated();
        $coupon = Coupon::where('code', strtoupper(trim($data['coupon'])))->first();
        
        if (!$coupon) {
            return back()->with('error', __('Invalid coupon code'));
        }

        session(['applied_coupon_id' => $coupon->id]);
        
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'message' => __('Coupon applied')]);
        }
        
        return back()->with('success', __('Coupon applied'));
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('applied_coupon_id');
        
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'message' => __('Coupon removed')]);
        }
        
        return back()->with('success', __('Coupon removed'));
    }
}