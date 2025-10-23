<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Str;

final class GatewayProcessor
{
    public static function normalizeGateways($rawGateways): array
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

    public static function processArrayGateway(array $g): ?array
    {
        $label = $g['label'] ?? ($g['name'] ?? null);
        if (! $label) {
            return null;
        }
        $slug = Str::slug($label);
        return ['slug' => $slug, 'label' => $label];
    }

    public static function processNumericGateway($g): ?array
    {
        $pg = PaymentGateway::find((int) $g);
        if (! $pg) {
            return null;
        }
        return [
            'slug' => $pg->slug ?? Str::slug($pg->name ?? (string) $pg->id),
            'label' => $pg->name ?? $pg->slug,
        ];
    }

    public static function processStringGateway(string $g): ?array
    {
        if ($g === '') {
            return null;
        }
        $pg = PaymentGateway::where('slug', $g)->first();
        return $pg ? ['slug' => $pg->slug, 'label' => $pg->name ?? $pg->slug] : ['slug' => Str::slug($g), 'label' => $g];
    }

    public static function processGateway($g): ?array
    {
        return match (true) {
            is_array($g) => self::processArrayGateway($g),
            is_numeric($g) => self::processNumericGateway($g),
            is_string($g) => self::processStringGateway($g),
            default => null,
        };
    }

    public static function processGatewaySlug($g): ?string
    {
        if (is_array($g)) {
            $label = $g['label'] ?? ($g['name'] ?? null);
            return $label ? Str::slug($label) : null;
        }
        if (is_string($g) && $g !== '') {
            return Str::slug($g);
        }
        return null;
    }
}
