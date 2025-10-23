<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Language;

class LanguagesController extends Controller
{
    public function index()
    {
        $langs = Language::where('is_active', 1)->orderByDesc('is_default')->get(['id', 'code', 'name', 'is_default']);

        return response()->json([
            'data' => $langs->map(fn ($l) => [
                'code' => $l->code,
                'name' => $l->name,
                'is_default' => (bool) $l->is_default,
            ])->values(),
        ]);
    }
}
