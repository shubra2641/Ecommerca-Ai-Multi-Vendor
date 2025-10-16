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
}
