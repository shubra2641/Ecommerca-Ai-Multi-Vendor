<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTagController extends Controller
{
    public function index()
    {
        $tags = ProductTag::orderBy('name')->paginate(30);

        return view('admin.products.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.products.tags.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate(['name' => 'required', 'slug' => 'nullable|unique:product_tags,slug']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        ProductTag::create($data);

        return redirect()->route('admin.product-tags.index')->with('success', __('Tag created'));
    }

    public function edit(ProductTag $productTag)
    {
        return view('admin.products.tags.edit', compact('productTag'));
    }

    public function update(Request $r, ProductTag $productTag)
    {
        $data = $r->validate(['name' => 'required', 'slug' => 'nullable|unique:product_tags,slug,' . $productTag->id]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $productTag->update($data);

        return redirect()->route('admin.product-tags.index')->with('success', __('Updated'));
    }

    public function destroy(ProductTag $productTag)
    {
        $productTag->delete();

        return back()->with('success', __('Deleted'));
    }
}
