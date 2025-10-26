<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\Verifier;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class ApiRequestHandler
{
    public function getChargeStatus(array $cfg, string $apiBase, string $chargeId): Response
    {
        return Http::withToken(
            $cfg['secret_key'] ?? ($cfg['api_key'] ?? null)
        )
            ->acceptJson()
            ->get($apiBase . '/charges/' . $chargeId);
    }
}