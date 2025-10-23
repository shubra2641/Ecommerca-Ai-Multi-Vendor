<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Shipping\ShippingResolver;
use Illuminate\Http\Request;

class NewShippingController extends Controller
{
    public function quote(Request $request)
    {
        $country = $request->input('country');
        $gov = $request->input('governorate');
        $city = $request->input('city');
        $zoneId = $request->input('zone'); // optional filter by zone
        $resolver = new ShippingResolver();
        $all = $request->input('all');
        if ($all) {
            $resolved = $resolver->resolveAll($country, $gov, $city, $zoneId);

            return response()->json(['data' => $resolved]);
        }
        $resolved = $resolver->resolve($country, $gov, $city, $zoneId);

        return response()->json(['data' => $resolved]);
    }
}
