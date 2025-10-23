<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Services\AI\SimpleAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class ProductCategoryController extends Controller
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

        $this->processTranslationsAndSlug($data, $r, $sanitizer);

        ProductCategory::create($data);

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', __('Category created'));
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

        $this->processTranslationsAndSlug($data, $r, $sanitizer);

        $productCategory->update($data);

        return redirect()->route('admin.product-categories.index')->with('success', __('Updated'));
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return back()->with('success', __('Deleted'));
    }

    public function aiSuggest(Request $request, SimpleAIService $ai)
    {
        $title = $request->input('name') ? $request->input('name') : $request->input('title');
        $locale = $request->input('locale');

        // Validate title
        if (empty($title)) {
            return back()->with('error', __('Please enter a name first'));
        }

        $result = $ai->generate($title, 'category', $locale);

        if (isset($result['error'])) {
            return back()->with('error', $result['error'])->withInput();
        }

        $merge = $this->buildMergeArray($result, $title, $locale);

        // Merge with existing form data to preserve user input
        $existingData = $request->except(['_token']);
        $mergedData = array_merge($existingData, $merge);

        return back()->with('success', __('AI generated successfully'))->withInput($mergedData);
    }

    private function buildMergeArray(array $result, string $title, ?string $locale): array
    {
        $merge = array_filter([
            'description' => $result['description'] ?? null,
            'seo_description' => $result['seo_description'] ?? null,
            'seo_keywords' => $result['seo_tags'] ?? null,
            'seo_title' => $result['seo_title'] ?? null,
        ], fn ($v) => ! empty($v));

        // Fill translations only for the requested language
        if ($locale && ! empty($result['description'])) {
            $merge["name_i18n.{$locale}"] = $title;
            $merge["description_i18n.{$locale}"] = $result['description'];
        }

        return $merge;
    }

    private function cleanTranslations(array $translations, \App\Services\HtmlSanitizer $sanitizer): array
    {
        $clean = [];
        foreach ($translations as $lc => $v) {
            $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
        }
        return array_filter($clean, fn ($v) => $v !== null && $v !== '');
    }

    private function processTranslationsAndSlug(array &$data, Request $r, \App\Services\HtmlSanitizer $sanitizer): void
    {
        $defaultLocale = cache()->remember('default_locale_code', 3600, function () {
            return optional(\App\Models\Language::where('is_default', 1)->first())->code ?? 'en';
        });
        $nameTranslations = $r->input('name_i18n', []);
        $descTranslations = $r->input('description_i18n', []);
        if (! empty($nameTranslations)) {
            $data['name_translations'] = $this->cleanTranslations($nameTranslations, $sanitizer);
        }
        if (! empty($descTranslations)) {
            $data['description_translations'] = $this->cleanTranslations($descTranslations, $sanitizer);
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
    }
}
