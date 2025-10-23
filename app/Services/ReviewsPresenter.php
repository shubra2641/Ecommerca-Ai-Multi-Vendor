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
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $helpfulTotal = 0;
        foreach ($allApproved as $r) {
            $rating = (int) ($r->rating ?? 0);
            if ($rating >= 1 && $rating <= 5) {
                $distribution[$rating]++;
            }
            $helpfulTotal += (int) ($r->helpful_count ?? 0);
        }
        $average = $total ? round(collect($allApproved)->avg('rating'), 2) : 0;
        // Compute percentage per star
        $distributionPercent = [];
        foreach ($distribution as $star => $count) {
            $distributionPercent[$star] = $total ? round($count * 100 / $total, 2) : 0;
        }

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
