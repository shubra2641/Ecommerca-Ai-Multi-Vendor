<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;

class PaymentGatewayApiController extends Controller
{
    public function enabled()
    {
        $g = PaymentGateway::where('enabled', true)->get();

        return response()->json($g->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'driver' => $item->driver,
                'requires_transfer_image' => (bool) $item->requires_transfer_image,
                'transfer_instructions' => $item->transfer_instructions,
                // PayPal support removed; historic records may still exist but do not expose paypal config
                'paypal' => null,
            ];
        }));
    }
}
