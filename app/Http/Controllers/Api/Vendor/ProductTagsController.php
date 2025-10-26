<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductTag;

class ProductTagsController extends Controller
{
    public function index()
    {
        $tags = ProductTag::select('id', 'name')->orderBy('name')->get();

        return response()->json(['data' => $tags]);
    }
}
