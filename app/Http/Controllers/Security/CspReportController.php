<?php

declare(strict_types=1);

namespace App\Http\Controllers\Security;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CspReportController
{
    public function __invoke(Request $request): Response
    {
        return response()->json(['status' => 'ok']);
    }
}
