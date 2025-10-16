<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductTag;
use Illuminate\Http\Request;

class ProductTagsController extends Controller
{
    public function index(Request $r)
    {
        $tags = ProductTag::select('id', 'name')->orderBy('name')->get();

        return response()->json(['data' => $tags]);
    }
}
