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
            ->filter(fn ($product) => $product->manage_stock ?? false)
            ->mapWithKeys(function ($product) {
                $stockQty = (int) ($product->stock_qty ?? 0);
                $available = (int) $product->availableStock();

                return [
                    $product->id => [
                        'available' => $available,
                        'stock_qty' => $stockQty,
                    ],
                ];
            })
            ->toArray();
    }
}
