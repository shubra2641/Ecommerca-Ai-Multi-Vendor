<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesErrors;
use App\Http\Controllers\Controller;
use App\Models\ShippingGroupLocation;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    use HandlesErrors;

    public function options(Request $request)
    {
        $country = $request->input('country');
        $governorate = $request->input('governorate');
        $city = $request->input('city');

        // Try most specific -> city -> governorate -> country -> default group price
        $query = ShippingGroupLocation::with('group')
            ->when($city, fn ($q) => $q->where('city_id', $city))
            ->when(! $city && $governorate, fn ($q) => $q->where('governorate_id', $governorate))
            ->when(! $city && ! $governorate && $country, fn ($q) => $q->where('country_id', $country));

        $results = $query->get()->map(fn ($r) => [
            'group_id' => $r->shipping_group_id,
            'name' => $r->group->name ?? 'Shipping',
            'price' => $r->price ?? $r->group->default_price,
            'estimated_days' => $r->estimated_days ?? $r->group->estimated_days,
        ]);

        return response()->json(['data' => $results]);
    }
}
