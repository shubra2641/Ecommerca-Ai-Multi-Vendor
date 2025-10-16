<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * PayPal webhook endpoint stub.
 *
 * PayPal gateway support has been removed from this application. Keep a small stub
 * that returns 404 so any external calls to the old webhook do not cause
 * unhandled exceptions. This file can be removed later after DNS/webhook owners
 * have been updated.
 */
class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        logger()->warning('Received request to deprecated PayPal webhook endpoint', ['ip' => $request->ip()]);

        return response()->json(['error' => 'paypal_removed'], 404);
    }
}
