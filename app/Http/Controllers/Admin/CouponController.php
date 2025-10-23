<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'uses_total' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            // checkbox 'active' comes as 'on' when checked; we'll normalize it below via $request->has('active')
            'active' => 'nullable',
            'min_order_total' => 'nullable|numeric|min:0',
        ]);
        if (isset($data['code']) && is_string($data['code'])) {
            $data['code'] = strtoupper($sanitizer->clean($data['code']));
        }
        $data['active'] = $request->has('active');
        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon created'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:coupons,code,'.$coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'uses_total' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            // normalize 'active' checkbox manually
            'active' => 'nullable',
            'min_order_total' => 'nullable|numeric|min:0',
        ]);
        if (isset($data['code']) && is_string($data['code'])) {
            $data['code'] = strtoupper($sanitizer->clean($data['code']));
        }
        $data['active'] = $request->has('active');
        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon updated'));
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', __('Coupon deleted'));
    }
}
