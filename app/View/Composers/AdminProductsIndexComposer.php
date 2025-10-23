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
        return collect($products)
            ->filter(fn ($p) => $p->manage_stock ?? false)
            ->mapWithKeys(function ($p) use ($low, $soon) {
                $available = (int) $p->availableStock();
                $status = $this->getStockStatus($available, $low, $soon);
                return [
                    $p->id => [
                        'available' => $available,
                        'stock_qty' => (int) ($p->stock_qty ?? 0),
                        'class' => $status['class'],
                        'badge' => $status['badge'],
                        'backorder' => (bool) ($p->backorder ?? false),
                    ],
                ];
            })
            ->toArray();
    }

    private function calculateVariationStocks($products, int $low, int $soon): array
    {
        return collect($products)
            ->filter(fn ($p) => ($p->type ?? null) === 'variable')
            ->flatMap(function ($p) use ($low, $soon) {
                $variations = $p->relationLoaded('variations') ? $p->variations : $p->variations()->get();
                return collect($variations)
                    ->filter(fn ($v) => $v->manage_stock ?? false)
                    ->mapWithKeys(function ($v) use ($low, $soon) {
                        $available = (int) (($v->stock_qty ?? 0) - ($v->reserved_qty ?? 0));
                        $status = $this->getStockStatus($available, $low, $soon);
                        return [
                            $v->id => [
                                'available' => $available,
                                'stock_qty' => (int) ($v->stock_qty ?? 0),
                                'class' => $status['class'],
                                'badge' => $status['badge'],
                            ],
                        ];
                    });
            })
            ->toArray();
    }

    private function getStockStatus(int $available, int $low, int $soon): array
    {
        if ($available <= $low) {
            return ['class' => 'text-danger', 'badge' => 'low'];
        }

        if ($available <= $soon) {
            return ['class' => 'text-warning', 'badge' => 'soon'];
        }

        return ['class' => '', 'badge' => null];
    }
}
