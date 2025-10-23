<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReviewRequest;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    // store a review (requires auth)
    public function store(ProductReviewRequest $r, $productId)
    {
        $product = Product::findOrFail($productId);
        $data = $r->validated();

        $userId = Auth::id();

        // Optional: verify ownership of order_id if provided
        if (! empty($data['order_id'])) {
            // we can't reliably check orders table if it doesn't exist;
            // best-effort: check product_serials for order_id belonging to user
            // If no verification possible, record order_id but still require admin approval
        }

        $autoPublish = optional(\App\Models\Setting::first())->auto_publish_reviews ? true : false;
        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'approved' => $autoPublish,
        ]);

        // If review not auto-published, notify admins for moderation
        if (! $autoPublish) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            if ($admins && $admins->count()) {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $admins,
                    new \App\Notifications\AdminReviewSubmittedNotification($review)
                );
            }
        }

        try {
            session()->flash('refresh_admin_notifications', true);
        } catch (\Throwable $e) {
            logger()->warning('Failed to flash admin notification refresh: '.$e->getMessage());
        }

        return redirect()->back()->with('status', 'Review submitted and awaits moderation.');
    }

    // public listing of approved reviews for a product
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $reviews = $product->reviews()->where('approved', true)->latest()->get();

        return view('front.products.partials.reviews', compact('product', 'reviews'));
    }
}
