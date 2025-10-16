<?php

namespace App\View\Composers;

use App\Models\ProductCategory;
use Illuminate\View\View;

class VendorProductsIndexComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $categories = [];
        try {
            $categories = ProductCategory::orderBy('name')->get();
        } catch (\Throwable $e) {
            $categories = collect();
        }

        $productStocks = [];
        if (isset($data['products'])) {
            foreach ($data['products'] as $p) {
                if ($p->manage_stock) {
                    try {
                        $av = (int) $p->availableStock();
                    } catch (\Throwable $e) {
                        $av = 0;
                    }
                    $productStocks[$p->id] = [
                        'available' => $av,
                        'stock_qty' => (int) ($p->stock_qty ?? 0),
                    ];
                }
            }
        }

        $view->with('vendorProductCategories', $categories);
        $view->with('vendorProductStocks', $productStocks);
    }
}
