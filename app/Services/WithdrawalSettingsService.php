<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

final class WithdrawalSettingsService
{
    public function getWithdrawalSettings(): array
    {
        $setting = Setting::first();
        $minimumAmount = isset($setting->min_withdrawal_amount) ? (float) $setting->min_withdrawal_amount : 10.0;
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];

        $normalizedGateways = GatewayProcessor::normalizeGateways($rawGateways);

        $gateways = collect($normalizedGateways)->map(fn($g) => GatewayProcessor::processGateway($g))->filter()->values()->toArray();

        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);

        return [
            'minimum_withdrawal' => $minimumAmount,
            'withdrawal_gateways' => $gateways,
            'withdrawal_commission_enabled' => $commissionEnabled,
            'withdrawal_commission_rate' => $commissionRate,
        ];
    }

    public function getWithdrawalGatewaySlugs(): array
    {
        $setting = Setting::first();
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];

        $normalizedGateways = GatewayProcessor::normalizeGateways($rawGateways);

        return collect($normalizedGateways)->map(fn($g) => GatewayProcessor::processGatewaySlug($g))->filter()->values()->toArray();
    }

    public function getCommissionSettings(): array
    {
        $setting = Setting::first();
        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);

        return [
            'enabled' => $commissionEnabled,
            'rate' => $commissionRate,
        ];
    }
}
