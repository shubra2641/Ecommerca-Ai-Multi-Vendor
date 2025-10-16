<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('name')->paginate(30);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191|unique:brands,slug',
            'active' => 'sometimes|boolean',
        ]);

        // sanitize simple string fields
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        Brand::create($data + ['active' => (bool) ($data['active'] ?? true)]);

        return redirect()->route('admin.brands.index')->with('success', __('Brand created'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191|unique:brands,slug,' . $brand->id,
            'active' => 'sometimes|boolean',
        ]);

        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        $brand->update($data + ['active' => (bool) ($data['active'] ?? $brand->active)]);

        return redirect()->route('admin.brands.index')->with('success', __('Brand updated'));
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', __('Brand deleted'));
    }
}
