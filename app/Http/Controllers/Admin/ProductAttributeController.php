<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();

        return view('admin.products.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.products.attributes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required', 'slug' => 'nullable|unique:product_attributes,slug']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $attr = ProductAttribute::create($data);

        return redirect()->route('admin.product-attributes.edit', $attr)->with('success', __('Attribute created'));
    }

    public function edit(ProductAttribute $productAttribute)
    {
        $productAttribute->load('values');

        return view('admin.products.attributes.edit', compact('productAttribute'));
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'nullable|unique:product_attributes,slug,' . $productAttribute->id,
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $productAttribute->update($data);

        return back()->with('success', __('Updated'));
    }

    public function storeValue(Request $request, ProductAttribute $productAttribute)
    {
        $data = $request->validate(['value' => 'required', 'slug' => 'nullable|unique:product_attribute_values,slug']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['value']);
        }

        $productAttribute->values()->create($data);

        return back()->with('success', __('Value added'));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function updateValue(
        Request $request,
        ProductAttributeValue $value
    ) {
        $data = $request->validate([
            'value' => 'required',
            'slug' => 'nullable|unique:product_attribute_values,slug,' . $value->id,
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['value']);
        }

        $value->update($data);

        return back()->with('success', __('Value updated'));
    }

    public function deleteValue(ProductAttributeValue $value)
    {
        $value->delete();

        return back()->with('success', __('Value deleted'));
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        $productAttribute->delete();

        return back()->with('success', __('Deleted'));
    }
}
