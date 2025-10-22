<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function governorates(Request $request)
    {
        $country = $request->input('country');
        if (! $country) {
            return response()->json(['data' => []]);
        }
        $items = Governorate::where('country_id', $country)->where('active', 1)->get(['id', 'name']);
        // If no active governorates found, fall back to returning any governorates for that country
        if ($items->isEmpty()) {
            $items = Governorate::where('country_id', $country)->get(['id', 'name']);
        }

        return response()->json(['data' => $items]);
    }

    public function cities(Request $request)
    {
        $governorate = $request->input('governorate');
        if (! $governorate) {
            return response()->json(['data' => []]);
        }
        $items = City::where('governorate_id', $governorate)->where('active', 1)->get(['id', 'name']);
        // If no active cities found, fall back to returning any cities for that governorate
        if ($items->isEmpty()) {
            $items = City::where('governorate_id', $governorate)->get(['id', 'name']);
        }

        return response()->json(['data' => $items]);
    }

    public function shipping(Request $request)
    {
        $country = $request->input('country');
        $governorate = $request->input('governorate');
        $city = $request->input('city');

        if (!$country) {
            return response()->json(['data' => []]);
        }

        $query = \App\Models\ShippingRule::where('active', 1)
            ->with('zone')
            ->where('country_id', $country);

        if ($city) {
            $query->where('city_id', $city);
        } elseif ($governorate) {
            $query->where('governorate_id', $governorate)->whereNull('city_id');
        } else {
            $query->whereNull('governorate_id')->whereNull('city_id');
        }

        $rules = $query->orderBy('city_id', 'desc')
            ->orderBy('governorate_id', 'desc')
            ->orderBy('country_id', 'desc')
            ->get();

        $data = $rules->map(function ($rule) {
            return [
                'id' => $rule->id,
                'zone_id' => $rule->zone_id,
                'company_name' => $rule->zone->name,
                'price' => $rule->price,
                'estimated_days' => $rule->estimated_days,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
