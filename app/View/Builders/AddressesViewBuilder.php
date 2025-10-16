<?php

namespace App\View\Builders;

class AddressesViewBuilder
{
    public static function splitAddresses($addresses): array
    {
        $collection = collect($addresses);
        $default = $collection->firstWhere('is_default', true);
        $others = $collection->filter(fn ($a) => ! $a->is_default);

        return [$default, $others];
    }
}
