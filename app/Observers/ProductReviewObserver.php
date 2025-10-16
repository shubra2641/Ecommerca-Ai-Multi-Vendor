<?php

namespace App\Observers;

use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

class ProductReviewObserver
{
    protected function recalc(ProductReview $review): void
    {
        $pid = $review->product_id;
        if (! $pid) {
            return;
        }
        $row = DB::table('product_reviews')
            ->selectRaw('COUNT(*) as c, COALESCE(AVG(rating),0) as a')
            ->where('product_id', $pid)
            ->where('approved', true)
            ->first();
        DB::table('products')->where('id', $pid)->update([
            'approved_reviews_count' => (int) ($row->c ?? 0),
            'approved_reviews_avg' => number_format((float) ($row->a ?? 0), 2, '.', ''),
        ]);
    }

    public function created(ProductReview $review): void
    {
        if ($review->approved) {
            $this->recalc($review);
        }
    }

    public function updated(ProductReview $review): void
    {
        $this->recalc($review);
    }

    public function deleted(ProductReview $review): void
    {
        $this->recalc($review);
    }
}
