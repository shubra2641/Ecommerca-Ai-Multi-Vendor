<?php

declare(strict_types=1);

namespace App\Services\Shipping;

use App\Models\ShippingRule;

/**
 * Determine appropriate shipping rules for a given destination.
 * Provides single best match (resolve) or all viable zone options (resolveAll) with precedence logic.
 */
class ShippingResolver
{
    /**
     * Resolve best matching shipping rule by precedence: city > governorate > country.
     * Optionally filter by zone id.
     *
     * @return array|null [zone_id, price, estimated_days, level]
     */
    public function resolve(
        ?int $countryId,
        ?int $governorateId = null,
        ?int $cityId = null,
        ?int $zoneId = null
    ): ?array {
        if (!$countryId) {
            return null;
        }
        $base = ShippingRule::with('zone')->where('active', true)
            ->where('country_id', $countryId)
            ->when($zoneId, fn($q) => $q->where('zone_id', $zoneId));

        $attempts = [
            [['city_id' => $cityId], 'city'],
            [['city_id' => null, 'governorate_id' => $governorateId], 'governorate'],
            [['governorate_id' => null, 'city_id' => null], 'country'],
        ];

        foreach ($attempts as [$conditions, $level]) {
            $result = $this->findRule($base, $conditions, $level);
            if ($result) {
                $rule = $result['rule'];
                return [
                    'zone_id' => $rule->zone_id,
                    'zone_name' => $rule->zone?->name,
                    'price' => $rule->price,
                    'estimated_days' => $rule->estimated_days,
                    'level' => $result['level'],
                ];
            }
        }

        return null;
    }

    private function findRule($base, array $conditions, string $level): ?array
    {
        $query = clone $base;
        foreach ($conditions as $column => $value) {
            if ($value === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $value);
            }
        }
        $rule = $query->first();
        return $rule ? ['rule' => $rule, 'level' => $level] : null;
    }

    /**
     * Return all matching shipping options (one per zone) for the given location.
     * Picks the best rule per zone using precedence city > governorate > country.
     *
     * @return array<array> empty array if none
     */
    public function resolveAll(
        ?int $countryId,
        ?int $governorateId = null,
        ?int $cityId = null,
        ?int $zoneId = null
    ): array {
        if (! $countryId) {
            return [];
        }
        $rules = ShippingRule::with('zone')->where('active', true)
            ->where('country_id', $countryId)
            ->when($zoneId, fn($q) => $q->where('zone_id', $zoneId))
            ->get();

        $bestPerZone = [];
        foreach ($rules as $r) {
            $zoneKey = $r->zone_id;
            // determine priority: 3=city match,2=governorate match,1=country level
            $priority = 0;
            if ($cityId && $r->city_id && $r->city_id === $cityId) {
                $priority = 3;
            } elseif (! $r->city_id && $governorateId && $r->governorate_id && $r->governorate_id === $governorateId) {
                $priority = 2;
            } elseif (! $r->city_id && ! $r->governorate_id) {
                $priority = 1;
            } else {
                continue;
            } // rule doesn't match this location

            if (! isset($bestPerZone[$zoneKey]) || $priority > $bestPerZone[$zoneKey]['priority']) {
                $bestPerZone[$zoneKey] = ['rule' => $r, 'priority' => $priority];
            }
        }

        $options = [];
        foreach ($bestPerZone as $entry) {
            $rule = $entry['rule'];
            $level = $entry['priority'] === 3 ? 'city' : ($entry['priority'] === 2 ? 'governorate' : 'country');
            $options[] = [
                'zone_id' => $rule->zone_id,
                'zone_name' => $rule->zone?->name,
                'price' => $rule->price,
                'estimated_days' => $rule->estimated_days,
                'level' => $level,
            ];
        }

        // sort options by price ascending (optional) so customers see cheaper options first
        usort($options, function ($a, $b) {
            $pa = $a['price'] === null ? 0 : $a['price'];
            $pb = $b['price'] === null ? 0 : $b['price'];

            return $pa <=> $pb;
        });

        return $options;
    }
}
