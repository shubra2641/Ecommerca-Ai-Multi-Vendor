<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

final class CartComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (isset($data['items'])) {
            $items = collect($data['items'])->map(function ($it) {
                $p = $it['product'];
                $onSale = ($p->sale_price ?? null) && $p->sale_price < ($p->price ?? 0);
                $salePercent = $onSale && $p->price ? round(($p->price - $p->sale_price) / $p->price * 100) : null;

                return $it + [
                    'cart_on_sale' => $onSale,
                    'cart_sale_percent' => $salePercent,
                ];
            })->all();
            $view->with('cartItemsPrepared', $items);
        }
    }
}
