<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AdminProductsIndexComposer
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

        $productStocks = [];
        $variationStocks = [];

        foreach ($products as $p) {
            // Product level
            if ($p->manage_stock) {
                $av = (int) $p->availableStock();
                $status = $this->classAndBadge($av, $low, $soon);
                $productStocks[$p->id] = [
                    'available' => $av,
                    'stock_qty' => (int) ($p->stock_qty ?? 0),
                    'class' => $status['class'],
                    'badge' => $status['badge'],
                    'backorder' => (bool) ($p->backorder ?? false),
                ];
            }

            if ($p->type === 'variable') {
                $vars = $p->relationLoaded('variations') ? $p->variations : $p->variations()->get();
                foreach ($vars as $v) {
                    if ($v->manage_stock) {
                        $avv = (int) (($v->stock_qty ?? 0) - ($v->reserved_qty ?? 0));
                        $status = $this->classAndBadge($avv, $low, $soon);
                        $variationStocks[$v->id] = [
                            'available' => $avv,
                            'stock_qty' => (int) ($v->stock_qty ?? 0),
                            'class' => $status['class'],
                            'badge' => $status['badge'],
                        ];
                    }
                }
            }
        }

        $view->with([
            'apiStockProducts' => $productStocks,
            'apiStockVariations' => $variationStocks,
            'apiStockLowThreshold' => $low,
            'apiStockSoonThreshold' => $soon,
        ]);
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
