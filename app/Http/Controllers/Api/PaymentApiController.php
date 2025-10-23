<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentApiController extends Controller
{
    /**
     * List enabled payment gateways (basic info only).
     */
    public function getGateways(): JsonResponse
    {
        try {
            $gateways = PaymentGateway::where('enabled', true)
                ->select(['id', 'name', 'slug', 'logo', 'description'])
                ->orderBy('name')
                ->get()
                ->map(fn ($g) => [
                    'id' => $g->id,
                    'name' => $g->name,
                    'slug' => $g->slug,
                    'logo' => $g->logo ? asset('storage/'.$g->logo) : null,
                    'description' => $g->description,
                    'is_available' => (bool) $g->enabled,
                ]);

            return response()->json([
                'success' => true,
                'data' => $gateways,
                'message' => 'Payment gateways retrieved',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gateways',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal error',
            ], 500);
        }
    }

    /**
     * Initialize a payment record for an order without external processing.
     */
    public function initializePayment(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'gateway_slug' => 'required|string|exists:payment_gateways,slug',
            'return_url' => 'required|url',
            'cancel_url' => 'required|url',
            'customer_info' => 'sometimes|array',
            'customer_info.name' => 'required_with:customer_info|string|max:255',
            'customer_info.email' => 'required_with:customer_info|email|max:255',
            'customer_info.phone' => 'sometimes|string|max:20',
        ]);
        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $v->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            $order = Order::findOrFail($request->order_id);
            $gateway = PaymentGateway::where('slug', $request->gateway_slug)->where('enabled', true)->firstOrFail();

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_gateway_id' => $gateway->id ?? null,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'initialized',
                'transaction_id' => null,
                'payload' => [
                    'customer_info' => $request->customer_info ?? [
                        'name' => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone,
                    ],
                    'metadata' => [
                        'return_url' => $request->return_url,
                        'cancel_url' => $request->cancel_url,
                        'user_agent' => $request->userAgent(),
                        'ip_address' => $request->ip(),
                    ],
                ],
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'status' => $payment->status,
                    'payment_url' => null,
                    'transaction_id' => null,
                    'gateway_slug' => $gateway->slug,
                    'expires_at' => now()->addMinutes(30)->toISOString(),
                ],
                'message' => 'Payment initialized',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Initialization failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal error',
            ], 500);
        }
    }

    /**
     * Get payment status.
     */
    public function getPaymentStatus(string $paymentId): JsonResponse
    {
        try {
            $payment = Payment::with(['order', 'gateway'])->findOrFail($paymentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'gateway' => $payment->gateway ? [
                        'name' => $payment->gateway->name,
                        'slug' => $payment->gateway->slug,
                    ] : null,
                    'transaction_id' => $payment->transaction_id,
                    'created_at' => $payment->created_at->toISOString(),
                    'updated_at' => $payment->updated_at->toISOString(),
                    'completed_at' => $payment->completed_at?->toISOString(),
                    'failure_reason' => $payment->failure_reason,
                ],
                'message' => 'Payment status retrieved',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Not found',
            ], 404);
        }
    }
}
