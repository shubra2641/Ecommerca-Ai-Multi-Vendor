<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class PaymentGatewaysController extends Controller
{
    public function index()
    {
        $gws = cache()->remember(
            'public_enabled_payment_gateways',
            60,
            function () {
                return \App\Models\PaymentGateway::where('enabled', true)
                    ->get()
                    ->map(function ($g) {
                        return [
                            'slug' => $g->slug,
                            'name' => $g->name,
                            'driver' => $g->driver,
                            'offline' => $g->driver === 'offline'
                                ? [
                                    'requires_transfer_image' => (bool) $g->requires_transfer_image,
                                    'instructions' => $g->transfer_instructions,
                                ]
                                : null,
                            'online' => $g->driver === 'stripe'
                                ? ['mode' => $g->stripe_mode]
                                : null,
                        ];
                    });
            }
        );

        return response()->json($gws);
    }
}
