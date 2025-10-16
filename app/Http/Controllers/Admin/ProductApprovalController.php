<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ProductRejected;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProductApprovalController extends Controller
{
    public function pending(Request $request)
    {
        $baseQuery = Product::query()->whereNotNull('vendor_id')->where('active', false);
        $overallTotal = (clone $baseQuery)->count();
        $query = (clone $baseQuery)->with('vendor');

        // Filter by vendor id
        $vendorId = null;
        if ($request->filled('vendor_id')) {
            $vendorId = $request->integer('vendor_id');
            $query->where('vendor_id', $vendorId);
        }
        // Search by name / id / rejection_reason
        if ($search = trim($request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhere('rejection_reason', 'like', "%{$search}%");
            });
        }
        $filteredTotal = (clone $query)->count();
        $products = $query->latest()->paginate(30)->appends($request->only('vendor_id', 'q'));

        // Vendors dropdown (limit for performance)
        $vendors = \App\Models\User::where('role', 'vendor')->orderBy('name')->limit(200)->get(['id', 'name']);
        if ($vendorId && ! $vendors->firstWhere('id', $vendorId)) {
            $singleVendor = \App\Models\User::where('role', 'vendor')->where('id', $vendorId)->first(['id', 'name']);
            if ($singleVendor) {
                $vendors->push($singleVendor);
            }
        }

        return view('admin.products.pending', [
            'products' => $products,
            'totalFiltered' => $filteredTotal,
            'totalOverall' => $overallTotal,
            'vendors' => $vendors,
            'selectedVendorId' => $vendorId,
        ]);
    }

    public function approve(Product $product)
    {
        $this->authorize('access-admin');
        $product->active = true;
        // record approval timestamp and clear any previous rejection reason
        $product->approved_at = now();
        $product->rejection_reason = null;
        $product->save();
        try {
            Mail::to($product->vendor->email)->queue(new \App\Mail\ProductApproved($product));
        } catch (\Throwable $e) {
        }
        // Notify vendor via in-app notification
        try {
            if ($product->vendor) {
                $product->vendor->notify(new \App\Notifications\VendorProductStatusNotification($product, 'approved'));
            }
        } catch (\Throwable $e) {
            logger()->warning('Vendor product approval notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Product approved');
    }

    public function reject(Request $r, Product $product, \App\Services\HtmlSanitizer $sanitizer)
    {
        $this->authorize('access-admin');
        $action = $r->input('mode', 'reject'); // reject | delete
        $reason = trim((string) $r->input('reason'));
        if ($reason !== '' && is_string($reason)) {
            $reason = $sanitizer->clean($reason);
        }
        if ($action === 'reject') {
            if ($reason === '') {
                return back()->withErrors(['reason' => __('Reason is required for rejection')]);
            }
            $product->active = false;
            $product->rejection_reason = $reason;
            $product->approved_at = null;
            $product->save();
        } elseif ($action === 'delete') {
            $productId = $product->id;
            $vendor = $product->vendor;
            $name = $product->name;
            $product->delete();
            // notify vendor of deletion (reuse rejected notification with special reason)
            try {
                if ($vendor) {
                    $fakeProduct = new Product(['id' => $productId, 'name' => $name]);
                    $notification = new \App\Notifications\VendorProductStatusNotification($fakeProduct, 'rejected', __('Deleted by admin'));
                    $vendor->notify($notification);
                }
            } catch (\Throwable $e) {
            }

            return back()->with('success', 'Product deleted');
        }
        try {
            Mail::to($product->vendor->email)->queue(new ProductRejected($product, $reason));
        } catch (\Throwable $e) {
        }
        // Notify vendor via in-app notification
        try {
            if ($product->vendor) {
                $product->vendor->notify(new \App\Notifications\VendorProductStatusNotification($product, 'rejected', $reason));
            }
        } catch (\Throwable $e) {
            logger()->warning('Vendor product rejection notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Product rejected');
    }
}
