<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\View\Builders\OrderViewBuilder;
use Illuminate\View\View;

final class OrdersComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();

        if (isset($data['order'])) {
            $this->composeSingleOrder($view, $data['order']);
        } elseif (isset($data['orders'])) {
            $this->composeOrderList($view, $data['orders']);
        }
    }

    private function composeSingleOrder(View $view, $order): void
    {
        $addressText = OrderViewBuilder::buildAddressText($order);
        $shipment = OrderViewBuilder::shipmentStages($order->status);
        $variantLabels = $this->buildVariantLabels($order);

        $view->with([
            'ovbAddressText' => $addressText,
            'ovbShipmentStages' => $shipment,
            'ovbVariantLabels' => $variantLabels,
        ]);
    }

    private function buildVariantLabels($order): array
    {
        $variantLabels = [];
        foreach ($order->items as $item) {
            $variantLabels[$item->id] = OrderViewBuilder::variantLabel($item);
        }

        return $variantLabels;
    }

    private function composeOrderList(View $view, $orders): void
    {
        $firstSummaries = $this->buildOrderSummaries($orders);
        $view->with('ordersFirstSummaries', $firstSummaries);
    }

    private function buildOrderSummaries($orders): array
    {
        $firstSummaries = [];
        foreach ($orders as $order) {
            $firstSummaries[$order->id] = $this->getOrderFirstItemSummary($order);
        }

        return $firstSummaries;
    }

    private function getOrderFirstItemSummary($order): string
    {
        $firstItem = $order->items->first();
        $firstName = $firstItem?->name;

        if ($firstItem && is_array($firstItem->meta ?? null) && ! empty($firstItem->meta['variant_name'])) {
            $firstName .= ' - ' . $firstItem->meta['variant_name'];
        }

        return $firstName ?: __('Order');
    }
}
