<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Shipping\ShippingResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewShippingController extends Controller
{
    public function quote(Request $request)
    {
        try {
            $country = $request->input('country') ? (int) $request->input('country') : null;
            $gov = $request->input('governorate') ? (int) $request->input('governorate') : null;
            $city = $request->input('city') ? (int) $request->input('city') : null;
            $zoneId = $request->input('zone') ? (int) $request->input('zone') : null; // optional filter by zone
            $resolver = new ShippingResolver();
            $all = $request->input('all');
            if ($all) {
                $resolved = $resolver->resolveAll($country, $gov, $city, $zoneId);

                return response()->json(['data' => $resolved]);
            }
            $resolved = $resolver->resolve($country, $gov, $city, $zoneId);

            return response()->json(['data' => $resolved]);
        } catch (\Exception $e) {
            Log::error('Shipping quote error: ' . $e->getMessage(), [
                'country' => $request->input('country'),
                'governorate' => $request->input('governorate'),
                'city' => $request->input('city'),
                'all' => $request->input('all'),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
}
