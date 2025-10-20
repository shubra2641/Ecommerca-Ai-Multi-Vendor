<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Services\AI\AIFormHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::with('children')->whereNull('parent_id')->orderBy('position')->get();

        return view('admin.products.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = ProductCategory::orderBy('name')->get();

        return view('admin.products.categories.create', compact('parents'));
    }

    public function store(Request $r, \App\Services\HtmlSanitizer $sanitizer)
    {
        $data = $r->validate([
            'parent_id' => 'nullable|exists:product_categories,id',
            'name' => 'required',
            'slug' => 'nullable|unique:product_categories,slug',
            'description' => 'nullable',
            'image' => 'nullable|string',
            'seo_title' => 'nullable',
            'seo_description' => 'nullable',
            'seo_keywords' => 'nullable',
            'position' => 'nullable|integer',
            'commission_rate' => 'nullable|numeric|min:0',
            'active' => 'boolean',
            'name_i18n' => 'array',
            'description_i18n' => 'array',
        ]);
        $defaultLocale = cache()->remember('default_locale_code', 3600, function () {
            return optional(\App\Models\Language::where('is_default', 1)->first())->code ?? 'en';
        });
        $nameTranslations = $r->input('name_i18n', []);
        $descTranslations = $r->input('description_i18n', []);
        if (! empty($nameTranslations)) {
            $clean = [];
            foreach ($nameTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['name_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
        }
        if (! empty($descTranslations)) {
            $clean = [];
            foreach ($descTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['description_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
        }
        if (isset($data['name_translations'][$defaultLocale])) {
            $data['name'] = $data['name_translations'][$defaultLocale];
        }
        if (isset($data['description_translations'][$defaultLocale])) {
            $data['description'] = $data['description_translations'][$defaultLocale];
        }
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        ProductCategory::create($data);

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Category created');
    }

    public function edit(ProductCategory $productCategory)
    {
        $parents = ProductCategory::where('id', '!=', $productCategory->id)->orderBy('name')->get();

        return view('admin.products.categories.edit', compact('productCategory', 'parents'));
    }

    public function update(Request $r, ProductCategory $productCategory, \App\Services\HtmlSanitizer $sanitizer)
    {
        $data = $r->validate([
            'parent_id' => 'nullable|exists:product_categories,id',
            'name' => 'required',
            'slug' => 'nullable|unique:product_categories,slug,' . $productCategory->id,
            'description' => 'nullable',
            'image' => 'nullable|string',
            'seo_title' => 'nullable',
            'seo_description' => 'nullable',
            'seo_keywords' => 'nullable',
            'position' => 'nullable|integer',
            'commission_rate' => 'nullable|numeric|min:0',
            'active' => 'boolean',
            'name_i18n' => 'array',
            'description_i18n' => 'array',
        ]);
        $defaultLocale = cache()->remember('default_locale_code', 3600, function () {
            return optional(\App\Models\Language::where('is_default', 1)->first())->code ?? 'en';
        });
        $nameTranslations = $r->input('name_i18n', []);
        $descTranslations = $r->input('description_i18n', []);
        if (! empty($nameTranslations)) {
            $clean = [];
            foreach ($nameTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['name_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
        }
        if (! empty($descTranslations)) {
            $clean = [];
            foreach ($descTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['description_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
        }
        if (isset($data['name_translations'][$defaultLocale])) {
            $data['name'] = $data['name_translations'][$defaultLocale];
        }
        if (isset($data['description_translations'][$defaultLocale])) {
            $data['description'] = $data['description_translations'][$defaultLocale];
        }
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $productCategory->update($data);

        return redirect()->route('admin.product-categories.index')->with('success', 'Updated');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return back()->with('success', 'Deleted');
    }

    public function export(Request $r)
    {
        $fileName = 'categories_export_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];
        $columns = ['id', 'name', 'slug', 'parent_id', 'position', 'active', 'created_at'];
        $callback = function () use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            \App\Models\ProductCategory::chunk(200, function ($items) use ($out) {
                foreach ($items as $c) {
                    $row = [
                        $c->id,
                        $c->name,
                        $c->slug,
                        $c->parent_id,
                        $c->position,
                        $c->active ? 1 : 0,
                        $c->created_at,
                    ];
                    fputcsv($out, $row);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // AI suggestion for category description & SEO
    public function aiSuggest(Request $request, AIFormHelper $aiHelper)
    {
        return $aiHelper->handleFormGeneration($request, 'category');
    }
}
