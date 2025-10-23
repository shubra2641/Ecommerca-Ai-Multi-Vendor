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
            $stock = $this->processProductStock($p, $low, $soon);
            if ($stock) {
                $productStocks[$p->id] = $stock;
            }
        }

        return $productStocks;
    }

    private function processProductStock($product, int $low, int $soon)
    {
        if (! $product->manage_stock) {
            return null;
        }

        $available = (int) $product->availableStock();
        $status = $this->classAndBadge($available, $low, $soon);

        return [
            'available' => $available,
            'stock_qty' => (int) ($product->stock_qty ?? 0),
            'class' => $status['class'],
            'badge' => $status['badge'],
            'backorder' => (bool) ($product->backorder ?? false),
        ];
    }

    private function calculateVariationStocks($products, int $low, int $soon): array
    {
        $variationStocks = [];

        foreach ($products as $p) {
            if ($p->type !== 'variable') {
                continue;
            }

            $variations = $this->getProductVariations($p);

            foreach ($variations as $v) {
                $stock = $this->processVariation($v, $low, $soon);
                if ($stock) {
                    $variationStocks[$v->id] = $stock;
                }
            }
        }

        return $variationStocks;
    }

    private function processVariation($variation, int $low, int $soon)
    {
        if (! $variation->manage_stock) {
            return null;
        }

        $available = (int) (($variation->stock_qty ?? 0) - ($variation->reserved_qty ?? 0));
        $status = $this->classAndBadge($available, $low, $soon);

        return [
            'available' => $available,
            'stock_qty' => (int) ($variation->stock_qty ?? 0),
            'class' => $status['class'],
            'badge' => $status['badge'],
        ];
    }

    private function getProductVariations($product)
    {
        return $product->relationLoaded('variations') ? $product->variations : $product->variations()->get();
    }

    private function classAndBadge(int $available, int $low, int $soon): array
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
