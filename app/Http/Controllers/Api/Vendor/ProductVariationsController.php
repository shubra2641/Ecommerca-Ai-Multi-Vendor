<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

class ProductVariationsController extends Controller
{
    protected function findOwnedProduct($user, $productId): Product
    {
        return Product::where('id', $productId)->where('vendor_id', $user->id)->firstOrFail();
    }

    protected function variationDataFrom(Request $r): array
    {
        $attrRaw = $r->input('attributes', []);
        if (is_string($attrRaw)) {
            $decoded = json_decode($attrRaw, true);
            $attrRaw = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [
            'name' => $r->input('name'),
            'sku' => $r->input('sku'),
            'price' => $r->input('price'),
            'sale_price' => $r->input('sale_price'),
            'sale_start' => $r->input('sale_start'),
            'sale_end' => $r->input('sale_end'),
            'manage_stock' => $r->boolean('manage_stock'),
            'stock_qty' => $r->input('stock_qty', 0),
            'reserved_qty' => $r->input('reserved_qty', 0),
            'backorder' => $r->boolean('backorder'),
            'image' => $r->input('image'),
            'attribute_data' => $attrRaw,
            'active' => $r->boolean('active', true),
        ];
    }

    public function update(Request $r, $productId, $variationId)
    {
        $product = $this->findOwnedProduct($r->user(), $productId);
        $variation = ProductVariation::where('product_id', $product->id)->where('id', $variationId)->firstOrFail();
        $r->validate([
            'price' => ['required', 'numeric'],
            'sale_price' => ['nullable', 'numeric'],
            'sale_start' => ['nullable', 'date'],
            'sale_end' => ['nullable', 'date', 'after_or_equal:sale_start'],
            'stock_qty' => ['nullable', 'integer'],
            'reserved_qty' => ['nullable', 'integer'],
        ]);
        $data = $this->variationDataFrom($r);
        // ensure price present
        if ($data['price'] === null || $data['price'] === '') {
            return response()->json(['message' => 'Price required'], 422);
        }
        $variation->update($data);

        return response()->json([
            'ok' => true,
            'variation' => [
                'id' => $variation->id,
                'product_id' => $product->id,
                'attributes' => $variation->attribute_data,
                'price' => $variation->price,
                'sale_price' => $variation->sale_price,
                'active' => (bool) $variation->active,
            ],
        ]);
    }

    public function destroy(Request $r, $productId, $variationId)
    {
        $product = $this->findOwnedProduct($r->user(), $productId);
        $variation = ProductVariation::where('product_id', $product->id)->where('id', $variationId)->firstOrFail();
        $variation->delete();

        return response()->json(['ok' => true]);
    }

    public function store(Request $r, $productId)
    {
        $product = $this->findOwnedProduct($r->user(), $productId);
        $r->validate([
            'price' => ['required', 'numeric'],
            'sale_price' => ['nullable', 'numeric'],
            'sale_start' => ['nullable', 'date'],
            'sale_end' => ['nullable', 'date', 'after_or_equal:sale_start'],
            'stock_qty' => ['nullable', 'integer'],
            'reserved_qty' => ['nullable', 'integer'],
        ]);
        $data = $this->variationDataFrom($r);
        if ($data['price'] === null || $data['price'] === '') {
            return response()->json(['message' => 'Price required'], 422);
        }
        $variation = $product->variations()->create($data);

        return response()->json([
            'ok' => true,
            'variation' => [
                'id' => $variation->id,
                'product_id' => $product->id,
                'attributes' => $variation->attribute_data,
                'price' => $variation->price,
                'sale_price' => $variation->sale_price,
                'active' => (bool) $variation->active,
                'stock_qty' => $variation->stock_qty,
            ],
        ], 201);
    }
}
