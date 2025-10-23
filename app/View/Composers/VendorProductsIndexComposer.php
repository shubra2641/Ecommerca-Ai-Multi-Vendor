<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\ProductCategory;
use Illuminate\View\View;

final class VendorProductsIndexComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();

        $view->with('vendorProductCategories', $this->getProductCategories());
        $view->with('vendorProductStocks', $this->calculateProductStocks($data));
    }

    private function getProductCategories()
    {
        try {
            return ProductCategory::orderBy('name')->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function calculateProductStocks(array $data): array
    {
        if (! isset($data['products'])) {
            return [];
        }

        return collect($data['products'])
            ->filter(fn ($product) => $product->manage_stock)
            ->mapWithKeys(fn ($product) => [$product->id => $this->calculateStockInfo($product)])
            ->toArray();
    }

    private function calculateStockInfo($product): array
    {
        $available = 0;
        $stockQty = (int) ($product->stock_qty ?? 0);

        try {
            $available = (int) $product->availableStock();
        } catch (\Throwable $e) {
            $available = 0;
        }

        return [
            'available' => $available,
            'stock_qty' => $stockQty,
        ];
    }
}
