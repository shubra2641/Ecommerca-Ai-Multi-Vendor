<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;

class ManifestController extends Controller
{
    public function show()
    {
        $path = public_path('manifest.webmanifest');
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file(
            $path,
            ['Content-Type' => 'application/manifest+json']
        );
    }
}
