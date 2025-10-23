<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function index(Request $r)
    {
        $products = $r->user()->products()
            ->with('category')
            ->withCount('variations')
            ->orderByDesc('created_at')
            ->paginate(20);

        return \App\Http\Resources\ProductResource::collection($products)->response()->getData(true);
    }

    public function store(ProductRequest $r)
    {
        $data = $r->validated();
        // attempt to reuse merge logic from web controller
        $controller = new \App\Http\Controllers\Vendor\ProductController();
        $controller->mergeVendorTranslations($r, $data);
        $data = $controller->collapsePrimaryTextFields($data);

        $defaultName = $data['name'] ?? '';
        $slug = Str::slug(is_array($defaultName) ? (array_values(array_filter($defaultName))[0] ?? '') : $defaultName);
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        $data['slug'] = $slug;
        if (isset($data['gallery'])) {
            $data['gallery'] = $controller->cleanGalleryValue($data['gallery']);
        }

        $product = Product::create($data + ['vendor_id' => $r->user()->id, 'active' => false]);
        $product->tags()->sync($r->input('tag_ids', []));
        if (($data['type'] ?? null) === 'variable') {
            try {
                $controller->syncVariations($product, $r);
            } catch (\Throwable $e) {
                // ignore sync errors from API path
            }
        }

        return response()->json(['ok' => true, 'product' => new \App\Http\Resources\ProductResource($product)], 201);
    }

    public function show(Request $r, $id)
    {
        $product = Product::where('id', $id)
            ->where('vendor_id', $r->user()->id)
            ->with(['variations', 'category', 'tags'])
            ->firstOrFail();

        return response()->json(new \App\Http\Resources\ProductResource($product));
    }

    public function update(ProductRequest $r, $id)
    {
        $product = Product::where('id', $id)->where('vendor_id', $r->user()->id)->firstOrFail();
        $data = $r->validated();
        $controller = new \App\Http\Controllers\Vendor\ProductController();
        $controller->mergeVendorTranslations($r, $data, $product);
        $data = $controller->collapsePrimaryTextFields($data, $product);

        $defaultName = $data['name'] ?? $product->name;
        $slug = Str::slug(is_array($defaultName) ? (array_values(array_filter($defaultName))[0] ?? '') : $defaultName);
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            $slug = $base.'-'.$i++;
        }
        $data['slug'] = $slug;
        if (isset($data['gallery'])) {
            $data['gallery'] = $controller->cleanGalleryValue($data['gallery']);
        }

        $product->fill($data);
        $product->active = false;
        $product->save();
        $product->tags()->sync($r->input('tag_ids', []));
        if (($data['type'] ?? $product->type) === 'variable') {
            try {
                $controller->syncVariations($product, $r);
            } catch (\Throwable $e) {
                // ignore sync errors
            }
        }

        return response()->json(['ok' => true, 'product' => new \App\Http\Resources\ProductResource($product)]);
    }

    public function destroy(Request $r, $id)
    {
        $product = Product::where('id', $id)->where('vendor_id', $r->user()->id)->firstOrFail();
        $product->delete();

        return response()->json(['ok' => true]);
    }
}
