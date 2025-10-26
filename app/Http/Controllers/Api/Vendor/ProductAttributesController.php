<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;

class ProductAttributesController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();

        $data = $attributes->map(function ($a) {
            return [
                'id' => $a->id,
                'name' => $a->name,
                'slug' => $a->slug,
                'values' => $a->values->map(fn($v) => [
                    'id' => $v->id,
                    'value' => $v->value,
                    'slug' => $v->slug,
                ])->values()->all(),
            ];
        })->values()->all();

        return response()->json(['data' => $data]);
    }
}
