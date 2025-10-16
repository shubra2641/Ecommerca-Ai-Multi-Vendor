<?php

namespace App\Http\Controllers\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CspReportController
{
    public function __invoke(Request $request): Response
    {
        $json = $request->getContent();
        // Log minimal sanitized report (avoid huge payload)
        Log::warning('CSP Report', [
            'ip' => $request->ip(),
            'agent' => $request->userAgent(),
            'body' => mb_strimwidth($json, 0, 4000, '...'),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
