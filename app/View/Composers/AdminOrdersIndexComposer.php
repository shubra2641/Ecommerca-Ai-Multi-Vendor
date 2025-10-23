<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\View\View;

final class AdminOrdersIndexComposer
{
    public function compose(View $view): void
    {
        $orders = $view->getData()['orders'] ?? collect();
        $prepared = $this->prepareOrdersData($orders);

        $view->with('ordersPrepared', $prepared);
    }

    private function prepareOrdersData($orders): array
    {
        $prepared = [];

        foreach ($orders as $order) {
            $prepared[$order->id] = [
                'firstItem' => $order->items->first(),
                'variantLabel' => $this->getVariantLabel($order),
                'shipText' => $this->getShippingText($order),
            ];
        }

        return $prepared;
    }

    private function getVariantLabel($order): ?string
    {
        $first = $order->items->first();

        if (! $first || ! is_array($first->meta)) {
            return null;
        }

        $meta = $first->meta;

        return match (true) {
            ! empty($meta['variant_name']) => $meta['variant_name'],
            ! empty($meta['attribute_data']) && is_array($meta['attribute_data']) => collect($meta['attribute_data'])
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->join(', '),
            default => null,
        };
    }

    private function getShippingText($order): string
    {
        $ship = $order->shipping_address;

        if (! is_array($ship)) {
            return (string) $ship;
        }

        $resolvedShip = $this->resolveAddressComponents($ship);
        $shipParts = $this->buildShippingParts($resolvedShip);

        return implode(', ', array_slice($shipParts, 0, 4));
    }

    private function resolveAddressComponents(array $ship): array
    {
        try {
            $ship = $this->resolveCountry($ship);
            $ship = $this->resolveGovernorate($ship);
            $ship = $this->resolveCity($ship);
        } catch (\Throwable $e) {
            logger()->warning('Failed to resolve address components: ' . $e->getMessage());
        }

        return $ship;
    }

    private function resolveCountry(array $ship): array
    {
        $countryId = $ship['country_id'] ?? $ship['country'] ?? null;

        $ship['country'] = ($countryId && is_numeric($countryId) && $country = Country::find($countryId)) ? $country->name : $ship['country'];

        return $ship;
    }

    private function resolveGovernorate(array $ship): array
    {
        $govId = $ship['governorate_id'] ?? $ship['governorate'] ?? null;

        $ship['governorate'] = ($govId && is_numeric($govId) && $governorate = Governorate::find($govId)) ? $governorate->name : $ship['governorate'];

        return $ship;
    }

    private function resolveCity(array $ship): array
    {
        $cityId = $ship['city_id'] ?? $ship['city'] ?? null;

        $ship['city'] = ($cityId && is_numeric($cityId) && $city = City::find($cityId)) ? $city->name : $ship['city'];

        return $ship;
    }

    private function buildShippingParts(array $ship): array
    {
        $shipParts = [];

        foreach (['name', 'line1', 'city', 'governorate', 'country', 'phone'] as $key) {
            if (! empty($ship[$key])) {
                $shipParts[] = $ship[$key];
            }
        }

        return $shipParts;
    }
}
