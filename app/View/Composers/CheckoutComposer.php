<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\View\Builders\CheckoutViewBuilder;
use Illuminate\View\View;

class CheckoutComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (isset($data['items']) && is_array($data['items'])) {
            $prepared = CheckoutViewBuilder::prepareItems($data['items']);
            $view->with('coItems', $prepared);
        }
        // Build config array if provided
        if (isset($data['displayDiscountedTotal']) || isset($data['total'])) {
            $base = [
                'baseTotal' => (float) ($data['displayDiscountedTotal'] ?? $data['total'] ?? 0),
            ];
            $view->with('checkoutConfigJson', json_encode($base));
        }
    }
}
