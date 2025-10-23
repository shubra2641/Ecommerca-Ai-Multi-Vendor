<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

class ReviewsPresenter
{
    public function build(Product $product): array
    {
        // Fetch approved reviews with user relation
        $query = $product->reviews()->with('user')->where('approved', true);
        $reviews = $query->latest()->paginate(10);

        // Aggregate stats (avoid re-querying rows multiple times)
        $allApproved = $query->get(['id', 'rating', 'helpful_count']);
        $total = $allApproved->count();
        $distribution = collect([1, 2, 3, 4, 5])->mapWithKeys(fn ($star) => [$star => $allApproved->where('rating', $star)->count()])->toArray();
        $helpfulTotal = $allApproved->sum('helpful_count');
        $average = $total ? round($allApproved->avg('rating'), 2) : 0;
        // Compute percentage per star
        $distributionPercent = collect($distribution)->map(fn ($count) => $total ? round($count * 100 / $total, 2) : 0)->toArray();

        return [
            'reviews' => $reviews,
            'stats' => [
                'total' => $total,
                'average' => $average,
                'distribution' => $distribution,
                'distribution_percent' => $distributionPercent,
                'helpful_total' => $helpfulTotal,
            ],
        ];
    }
}
