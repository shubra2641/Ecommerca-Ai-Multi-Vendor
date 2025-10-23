<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\View\Builders\OrderViewBuilder;
use Illuminate\View\View;

class OrdersComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (isset($data['order'])) {
            $order = $data['order'];
            $addressText = OrderViewBuilder::buildAddressText($order);
            $shipment = OrderViewBuilder::shipmentStages($order->status);
            // Per-item variant labels
            $variantLabels = [];
            foreach ($order->items as $it) {
                $variantLabels[$it->id] = OrderViewBuilder::variantLabel($it);
            }
            $view->with([
                'ovbAddressText' => $addressText,
                'ovbShipmentStages' => $shipment,
                'ovbVariantLabels' => $variantLabels,
            ]);
        } elseif (isset($data['orders'])) {
            // Build first item name+variant for each order
            $orders = $data['orders'];
            $firstSummaries = [];
            foreach ($orders as $o) {
                $firstItem = $o->items->first();
                $firstName = $firstItem?->name;
                if ($firstItem && is_array($firstItem->meta ?? null) && ! empty($firstItem->meta['variant_name'])) {
                    $firstName .= ' - '.$firstItem->meta['variant_name'];
                }
                $firstSummaries[$o->id] = $firstName ? $firstName : __('Order');
            }
            $view->with('ordersFirstSummaries', $firstSummaries);
        }
    }
}
