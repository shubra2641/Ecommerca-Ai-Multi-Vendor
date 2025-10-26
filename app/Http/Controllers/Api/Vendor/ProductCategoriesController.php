<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;

class ProductCategoriesController extends Controller
{
    public function index()
    {
        $cats = ProductCategory::select('id', 'name')->orderBy('name')->get();

        return response()->json(['data' => $cats]);
    }
}
