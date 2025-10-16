<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoriesController extends Controller
{
    public function index(Request $r)
    {
        $cats = ProductCategory::select('id', 'name')->orderBy('name')->get();

        return response()->json(['data' => $cats]);
    }
}
