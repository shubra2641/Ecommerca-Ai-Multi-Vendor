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
        $productStocks = [];

        if (! isset($data['products'])) {
            return $productStocks;
        }

        foreach ($data['products'] as $product) {
            if ($product->manage_stock) {
                $productStocks[$product->id] = $this->getStockInfo($product);
            }
        }

        return $productStocks;
    }

    private function getStockInfo($product): array
    {
        $available = 0;

        // Try to get available stock, fallback to 0 on error
        try {
            $available = (int) $product->availableStock();
        } catch (\Throwable $e) {
            $available = 0;
        }

        $stockQty = (int) ($product->stock_qty ?? 0);

        return [
            'available' => $available,
            'stock_qty' => $stockQty,
        ];
    }
}
