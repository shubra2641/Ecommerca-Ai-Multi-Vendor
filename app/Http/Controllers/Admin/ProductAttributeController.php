<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Services\HtmlSanitizer;
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

    public function store(Request $r, HtmlSanitizer $sanitizer)
    {
        $data = $r->validate(['name' => 'required', 'slug' => 'nullable|unique:product_attributes,slug']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        $attr = ProductAttribute::create($data);

        return redirect()->route('admin.product-attributes.edit', $attr)->with('success', 'Attribute created');
    }

    public function edit(ProductAttribute $productAttribute)
    {
        $productAttribute->load('values');

        return view('admin.products.attributes.edit', compact('productAttribute'));
    }

    public function update(Request $r, ProductAttribute $productAttribute, HtmlSanitizer $sanitizer)
    {
        $data = $r->validate([
            'name' => 'required',
            'slug' => 'nullable|unique:product_attributes,slug,' . $productAttribute->id
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        $productAttribute->update($data);

        return back()->with('success', 'Updated');
    }

    public function storeValue(Request $r, ProductAttribute $productAttribute, HtmlSanitizer $sanitizer)
    {
        $data = $r->validate(['value' => 'required', 'slug' => 'nullable|unique:product_attribute_values,slug']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['value']);
        }
        if (isset($data['value']) && is_string($data['value'])) {
            $data['value'] = $sanitizer->clean($data['value']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        $productAttribute->values()->create($data);

        return back()->with('success', 'Value added');
    }

    public function updateValue(Request $r, ProductAttribute $productAttribute, ProductAttributeValue $value, HtmlSanitizer $sanitizer)
    {
        $data = $r->validate([
            'value' => 'required',
            'slug' => 'nullable|unique:product_attribute_values,slug,' . $value->id,
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['value']);
        }
        if (isset($data['value']) && is_string($data['value'])) {
            $data['value'] = $sanitizer->clean($data['value']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        $value->update($data);

        return back()->with('success', 'Value updated');
    }

    public function deleteValue(ProductAttribute $productAttribute, ProductAttributeValue $value)
    {
        $value->delete();

        return back()->with('success', 'Value deleted');
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        $productAttribute->delete();

        return back()->with('success', 'Deleted');
    }
}
