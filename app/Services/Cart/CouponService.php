<?php

namespace App\Services\Cart;

use App\Http\Requests\Cart\ApplyCouponRequest;
use App\Models\Coupon;
use App\Services\Cart\CurrencyService;
use Illuminate\Http\Request;

class CouponService
{
    public function __construct(
        private CurrencyService $currencyService
    ) {}

    public function applyCoupon(ApplyCouponRequest $request)
    {
        $data = $request->validated();
        $code = strtoupper(trim($data['coupon']));
        
        $coupon = $this->findCouponByCode($code);
        if (!$coupon) {
            return back()->with('error', __('Invalid coupon code'));
        }

        $cartTotal = $this->calculateCartTotal();
        $displayedTotal = $this->currencyService->convertToDisplayCurrency($cartTotal);
        
        if (!$this->validateCoupon($coupon, $displayedTotal)) {
            return back()->with('error', __('Coupon is not valid or expired'));
        }

        session(['applied_coupon_id' => $coupon->id]);
        
        $responseData = $this->prepareResponseData($coupon, $cartTotal);
        return $this->sendResponse($request, $responseData, __('Coupon applied'));
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('applied_coupon_id');
        
        $cartTotal = $this->calculateCartTotal();
        $displayTotal = $this->currencyService->convertToDisplayCurrency($cartTotal);
        
        $responseData = [
            'displayTotal' => $displayTotal,
            'discountedTotal' => $displayTotal,
            'discount' => 0,
            'currency_symbol' => $this->currencyService->getCurrentCurrencySymbol(),
        ];
        
        return $this->sendResponse($request, $responseData, __('Coupon removed'));
    }

    private function findCouponByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)->first();
    }

    private function calculateCartTotal(): float
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $row) {
            $total += ($row['price'] * $row['qty']);
        }

        return $total;
    }

    private function validateCoupon(Coupon $coupon, float $total): bool
    {
        $tolerance = 0.01;
        $candidates = [
            $total - $tolerance,
            $total,
            $total + $tolerance,
        ];

        foreach ($candidates as $candidate) {
            if ($coupon->isValidForTotal($candidate)) {
                return true;
            }
        }

        return false;
    }

    private function prepareResponseData(Coupon $coupon, float $baseTotal): array
    {
        $displayTotal = $this->currencyService->convertToDisplayCurrency($baseTotal);
        $discountedTotal = $this->currencyService->convertToDisplayCurrency($coupon->applyTo($baseTotal));
        $discount = round($displayTotal - $discountedTotal, 2);

        return [
            'coupon' => $coupon->code,
            'displayTotal' => $displayTotal,
            'discountedTotal' => $discountedTotal,
            'discount' => $discount,
            'currency_symbol' => $this->currencyService->getCurrentCurrencySymbol(),
        ];
    }

    private function sendResponse(Request $request, array $data, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => $message,
                ...$data
            ]);
        }

        return back()->with('success', $message);
    }
}
