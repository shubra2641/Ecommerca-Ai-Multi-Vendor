<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\Setting;
use Illuminate\Support\Str;

class WithdrawalSettingsService
{
    private function normalizeGateways($rawGateways): array
    {
        if (is_string($rawGateways)) {
            $decoded = json_decode($rawGateways, true);
            if (is_array($decoded)) {
                $rawGateways = $decoded;
            } else {
                $rawGateways = array_filter(array_map('trim', preg_split('/\r?\n/', $rawGateways)));
            }
        }
        return (array) $rawGateways;
    }

    private function processGateway($g): ?array
    {
        if (is_array($g)) {
            $label = $g['label'] ?? ($g['name'] ?? null);
            if ($label) {
                $slug = Str::slug($label);
                return ['slug' => $slug, 'label' => $label];
            }
            return null;
        }

        if (is_numeric($g)) {
            $pg = PaymentGateway::find((int) $g);
            if ($pg) {
                return [
                    'slug' => $pg->slug ?? Str::slug($pg->name ?? (string) $pg->id),
                    'label' => $pg->name ?? $pg->slug,
                ];
            }
        }

        if (is_string($g) && $g !== '') {
            $pg = PaymentGateway::where('slug', $g)->first();
            if ($pg) {
                return ['slug' => $pg->slug, 'label' => $pg->name ?? $pg->slug];
            }

            $slug = Str::slug($g);
            return ['slug' => $slug, 'label' => $g];
        }

        return null;
    }

    public function getWithdrawalSettings(): array
    {
        $setting = Setting::first();
        $minimumAmount = isset($setting->min_withdrawal_amount) ? (float) $setting->min_withdrawal_amount : 10.0;
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];

        $normalizedGateways = $this->normalizeGateways($rawGateways);

        $gateways = [];
        foreach ($normalizedGateways as $g) {
            $processed = $this->processGateway($g);
            if ($processed) {
                $gateways[] = $processed;
            }
        }

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

        $normalizedGateways = $this->normalizeGateways($rawGateways);

        $gatewaySlugs = [];
        foreach ($normalizedGateways as $gw) {
            if (is_array($gw)) {
                $label = $gw['label'] ?? ($gw['name'] ?? null);
                if ($label) {
                    $gatewaySlugs[] = Str::slug($label);
                }
            } elseif (is_string($gw) && $gw !== '') {
                $gatewaySlugs[] = Str::slug($gw);
            }
        }

        return $gatewaySlugs;
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
