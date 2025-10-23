<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminProductsIndexComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (! isset($data['products'])) {
            return;
        }

        $products = $data['products'];
        $low = (int) config('catalog.stock_low_threshold');
        $soon = (int) config('catalog.stock_soon_threshold');

        $productStocks = $this->calculateProductStocks($products, $low, $soon);
        $variationStocks = $this->calculateVariationStocks($products, $low, $soon);

        $view->with([
            'apiStockProducts' => $productStocks,
            'apiStockVariations' => $variationStocks,
            'apiStockLowThreshold' => $low,
            'apiStockSoonThreshold' => $soon,
        ]);
    }

    private function calculateProductStocks($products, int $low, int $soon): array
    {
        $productStocks = [];

        foreach ($products as $p) {
            if (! $p->manage_stock) {
                continue;
            }

            $available = (int) $p->availableStock();
            $status = $this->classAndBadge($available, $low, $soon);

            $productStocks[$p->id] = [
                'available' => $available,
                'stock_qty' => (int) ($p->stock_qty ?? 0),
                'class' => $status['class'],
                'badge' => $status['badge'],
                'backorder' => (bool) ($p->backorder ?? false),
            ];
        }

        return $productStocks;
    }

    private function calculateVariationStocks($products, int $low, int $soon): array
    {
        $variationStocks = [];

        foreach ($products as $p) {
            if ($p->type !== 'variable') {
                continue;
            }

            $variations = $p->relationLoaded('variations') ? $p->variations : $p->variations()->get();

            foreach ($variations as $v) {
                if (! $v->manage_stock) {
                    continue;
                }

                $available = (int) (($v->stock_qty ?? 0) - ($v->reserved_qty ?? 0));
                $status = $this->classAndBadge($available, $low, $soon);

                $variationStocks[$v->id] = [
                    'available' => $available,
                    'stock_qty' => (int) ($v->stock_qty ?? 0),
                    'class' => $status['class'],
                    'badge' => $status['badge'],
                ];
            }
        }

        return $variationStocks;
    }

    private function classAndBadge(int $available, int $low, int $soon): array
    {
        return match (true) {
            $available <= $low => ['class' => 'text-danger', 'badge' => 'low'],
            $available <= $soon => ['class' => 'text-warning', 'badge' => 'soon'],
            default => ['class' => '', 'badge' => null],
        };
    }
}
