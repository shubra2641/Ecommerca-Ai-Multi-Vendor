<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $brands = Brand::orderBy('name')->paginate(30);

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\HtmlSanitizer  $sanitizer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191|unique:brands,slug',
            'active' => 'nullable|boolean',
        ]);
        // Explicitly set default for 'active' if not present
        $data['active'] = array_key_exists('active', $data) ? (bool) $data['active'] : true;

        // sanitize simple string fields
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', __('Brand created'));
    }

    /**
     * Show the form for editing the specified brand.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\View\View
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @param  \App\Services\HtmlSanitizer  $sanitizer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Brand $brand, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => [
                'nullable',
                'string',
                'max:191',
                \Illuminate\Validation\Rule::unique('brands', 'slug')
                    ->ignore($brand->id)
                    ->whereNull('deleted_at'),
            ],
            'active' => 'nullable|boolean',
        ]);
        // Explicitly set default for 'active' if not present
        $data['active'] = array_key_exists('active', $data) ? (bool) $data['active'] : $brand->active;

        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $data['slug'] = $sanitizer->clean($data['slug']);
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', __('Brand updated'));
    }

    /**
     * Remove the specified brand from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', __('Brand deleted'));
    }
}
