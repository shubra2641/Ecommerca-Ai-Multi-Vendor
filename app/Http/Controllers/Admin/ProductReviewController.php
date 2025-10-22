<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;

class ProductReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with('product', 'user')->latest()->paginate(30);

        return view('admin.products.reviews.index', compact('reviews'));
    }

    public function approve(ProductReview $review)
    {
        $review->approved = true;
        $review->save();

        return redirect()->back()->with('status', 'Review approved');
    }

    public function unapprove(ProductReview $review)
    {
        $review->approved = false;
        $review->save();

        return redirect()->back()->with('status', 'Review unapproved');
    }

    public function show(ProductReview $review)
    {
        $review->load('product', 'user');

        return view('admin.products.reviews.show', compact('review'));
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return redirect()->back()->with('status', 'Review deleted');
    }
}
