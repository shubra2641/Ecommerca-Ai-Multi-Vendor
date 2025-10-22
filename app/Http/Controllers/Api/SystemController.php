<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SystemController extends Controller
{
    /**
     * Get system settings for mobile app
     */
    public function settings(): JsonResponse
    {
        try {
            $setting = Setting::first();

            if (! $setting) {
                return response()->json([
                    'min_withdrawal_amount' => '10.00',
                    'withdrawal_gateways' => [],
                    'withdrawal_commission_enabled' => false,
                    'withdrawal_commission_rate' => '0.00',
                ]);
            }

            // Parse withdrawal gateways
            $withdrawalGateways = [];
            if (! empty($setting->withdrawal_gateways)) {
                $gateways = $setting->withdrawal_gateways;

                // Handle different formats
                if (is_string($gateways)) {
                    // Try to decode as JSON first
                    $decoded = json_decode($gateways, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $withdrawalGateways = $decoded;
                    } else {
                        // Split by newlines or commas
                        $withdrawalGateways = array_filter(
                            array_map('trim', preg_split('/[\n\r,]+/', $gateways))
                        );
                    }
                } elseif (is_array($gateways)) {
                    $withdrawalGateways = $gateways;
                }
            }

            return response()->json([
                'min_withdrawal_amount' => $setting->min_withdrawal_amount ?? '10.00',
                'withdrawal_gateways' => $withdrawalGateways,
                'withdrawal_commission_enabled' => (bool) $setting->withdrawal_commission_enabled,
                'withdrawal_commission_rate' => $setting->withdrawal_commission_rate ?? '0.00',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch system settings',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
