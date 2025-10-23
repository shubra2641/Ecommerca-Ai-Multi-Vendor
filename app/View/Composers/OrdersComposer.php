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
        return $order->items->mapWithKeys(function ($item) {
            return [$item->id => OrderViewBuilder::variantLabel($item)];
        })->toArray();
    }

    private function composeOrderList(View $view, $orders): void
    {
        $firstSummaries = $this->buildOrderSummaries($orders);
        $view->with('ordersFirstSummaries', $firstSummaries);
    }

    private function buildOrderSummaries($orders): array
    {
        return $orders->mapWithKeys(function ($order) {
            return [$order->id => $this->getOrderFirstItemSummary($order)];
        })->toArray();
    }

    private function getOrderFirstItemSummary($order): string
    {
        $firstItem = $order->items->first();

        if (! $firstItem) {
            return __('Order');
        }

        $itemName = $firstItem->name ?? __('Order');
        $variantName = data_get($firstItem->meta, 'variant_name');

        return $variantName ? $itemName . ' - ' . $variantName : $itemName;
    }
}
