<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AdminNotifyTopProductsComposer
{
    public function compose(View $view): void
    {
        $rows = $view->getData()['rows'] ?? [];
        $products = $view->getData()['products'] ?? [];
        // Pre-resolve product lookup for each row id to avoid inline assignment
        $resolved = [];
        foreach ($rows as $i => $row) {
            $pid = $row->product_id ?? null;
            $resolved[$i] = $pid && isset($products[$pid]) ? $products[$pid] : null;
        }
        $view->with('ntpResolvedProducts', $resolved);
    }
}
