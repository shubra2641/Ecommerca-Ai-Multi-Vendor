<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\View\View;

class AdminOrdersIndexComposer
{
    public function compose(View $view): void
    {
        $orders = $view->getData()['orders'] ?? collect();
        $prepared = [];
        foreach ($orders as $order) {
            $first = $order->items->first();
            $variantLabel = null;
            if ($first && is_array($first->meta)) {
                if (! empty($first->meta['variant_name'])) {
                    $variantLabel = $first->meta['variant_name'];
                } elseif (! empty($first->meta['attribute_data']) && is_array($first->meta['attribute_data'])) {
                    $variantLabel = collect($first->meta['attribute_data'])
                        ->map(fn ($v, $k) => ucfirst($k).': '.$v)
                        ->join(', ');
                }
            }
            $ship = $order->shipping_address;
            $shipText = '';
            if (is_array($ship)) {
                // resolve ids to names
                try {
                    $countryId = $ship['country_id'] ?? $ship['country'] ?? null;
                    $govId = $ship['governorate_id'] ?? $ship['governorate'] ?? null;
                    $cityId = $ship['city_id'] ?? $ship['city'] ?? null;
                    if ($countryId && is_numeric($countryId) && ($c = Country::find($countryId))) {
                        $ship['country'] = $c->name;
                    }
                    if ($govId && is_numeric($govId) && ($g = Governorate::find($govId))) {
                        $ship['governorate'] = $g->name;
                    }
                    if ($cityId && is_numeric($cityId) && ($ci = City::find($cityId))) {
                        $ship['city'] = $ci->name;
                    }
                } catch (\Throwable $e) {
                    logger()->warning('Failed to resolve address components: '.$e->getMessage());
                }
                $shipParts = [];
                foreach (['name', 'line1', 'city', 'governorate', 'country', 'phone'] as $k) {
                    if (! empty($ship[$k])) {
                        $shipParts[] = $ship[$k];
                    }
                }
                $shipText = implode(', ', array_slice($shipParts, 0, 4));
            } else {
                $shipText = (string) $ship;
            }
            $prepared[$order->id] = [
                'firstItem' => $first,
                'variantLabel' => $variantLabel,
                'shipText' => $shipText,
            ];
        }
        $view->with('ordersPrepared', $prepared);
    }
}
