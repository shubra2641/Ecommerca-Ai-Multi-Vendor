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

    public function store(Request $request)
    {
        $data = $request->validate([
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

        $this->processTranslationsAndSlug($data, $request);

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

    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $request->validate([
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

        $this->processTranslationsAndSlug($data, $request);

        $productCategory->update($data);

        return redirect()->route('admin.product-categories.index')->with('success', __('Updated'));
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return back()->with('success', __('Deleted'));
    }

    public function aiSuggest(Request $request, SimpleAIService $aiService)
    {
        $title = $request->input('name') ?: $request->input('title');

        // Validate title
        if (empty($title)) {
            return back()->with('error', __('Please enter a name first'));
        }

        // Get all active languages
        $languages = \App\Models\Language::where('is_active', 1)->get();

        // Generate content for all languages
        $formattedData = [];

        foreach ($languages as $language) {
            $result = $aiService->generate($title, 'category', $language->code);

            if (isset($result['error'])) {
                continue; // Skip this language if AI fails
            }

            // Add content for this language
            if (isset($result['name'])) {
                $formattedData['name_i18n'][$language->code] = $result['name'];
            }
            if (isset($result['description'])) {
                $formattedData['description_i18n'][$language->code] = $result['description'];
            }
            if (isset($result['seo_title'])) {
                $formattedData['seo_title'][$language->code] = $result['seo_title'];
            }
            if (isset($result['seo_description'])) {
                $formattedData['seo_description'][$language->code] = $result['seo_description'];
            }
            if (isset($result['seo_tags'])) {
                $formattedData['seo_keywords'][$language->code] = $result['seo_tags'];
            }
        }

        // Merge with existing form data
        $existingData = $request->except(['_token']);
        $mergedData = array_merge($existingData, $formattedData);

        return back()->with('success', __('AI generated successfully for all languages'))->withInput($mergedData);
    }

    private function buildMergeArray(array $result, string $title, ?string $locale): array
    {
        $merge = array_filter([
            'description' => $result['description'] ?? null,
            'seo_description' => $result['seo_description'] ?? null,
            'seo_keywords' => $result['seo_tags'] ?? null,
            'seo_title' => $result['seo_title'] ?? null,
        ], fn($value) => ! empty($value));

        // Fill translations only for the requested language
        if ($locale && ! empty($result['description'])) {
            $merge["name_i18n.{$locale}"] = $title;
            $merge["description_i18n.{$locale}"] = $result['description'];
        }

        return $merge;
    }

    private function processTranslationsAndSlug(array &$data, Request $request): void
    {
        $defaultLocale = cache()->remember('default_locale_code', 3600, function () {
            return optional(\App\Models\Language::where('is_default', 1)->first())->code ?? 'en';
        });
        $nameTranslations = $request->input('name_i18n', []);
        $descTranslations = $request->input('description_i18n', []);
        if (! empty($nameTranslations)) {
            $data['name_translations'] = array_filter($nameTranslations);
        }
        if (! empty($descTranslations)) {
            $data['description_translations'] = array_filter($descTranslations);
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
