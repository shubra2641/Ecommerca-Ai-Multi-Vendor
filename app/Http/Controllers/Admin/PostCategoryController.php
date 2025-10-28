<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryController extends Controller
{
    public function index()
    {
        $categories = PostCategory::orderBy('name')->paginate(30);

        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = PostCategory::orderBy('name')->get();

        return view('admin.blog.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'name.*' => 'nullable|string|max:190',
            'slug' => 'nullable|array',
            'slug.*' => 'nullable|string|max:190',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'seo_title' => 'nullable|array',
            'seo_title.*' => 'nullable|string|max:190',
            'seo_description' => 'nullable|array',
            'seo_description.*' => 'nullable|string|max:300',
            'seo_tags' => 'nullable|array',
            'seo_tags.*' => 'nullable|string|max:250',
            'parent_id' => 'nullable|exists:post_categories,id',
        ]);
        $fallback = config('app.fallback_locale');
        $defaultName = $data['name'][$fallback] ?? collect($data['name'])->first(fn($v) => ! empty($v));
        $slugTranslations = $data['slug'] ?? [];
        foreach ($data['name'] as $loc => $v) {
            if (! isset($slugTranslations[$loc]) || $slugTranslations[$loc] === '') {
                $slugTranslations[$loc] = Str::slug($v ?? $defaultName ?? '');
            }
        }
        $baseSlug = Str::slug($defaultName ?? '');
        if ($baseSlug === '') {
            $baseSlug = Str::random(8);
        }
        $unique = $baseSlug;
        $counter = 1;
        while (PostCategory::where('slug', $unique)->exists()) {
            $unique = $baseSlug . '-' . $counter;
            $counter++;
        }
        $payload = [
            'name' => $defaultName,
            'slug' => $unique,
            'name_translations' => array_filter($data['name']),
            'slug_translations' => array_filter($slugTranslations),
            'parent_id' => $data['parent_id'] ?? null,
        ];
        foreach (['description', 'seo_title', 'seo_description', 'seo_tags'] as $f) {
            if (isset($data[$f]) && is_array($data[$f])) {
                $payload[$f . '_translations'] = array_filter($data[$f]);
                $payload[$f] = $payload[$f . '_translations'][$fallback] ??
                    collect($payload[$f . '_translations'])->first(fn($value) => ! empty($value));
            }
        }
        PostCategory::create($payload);

        return redirect()->route('admin.blog.categories.index')->with('success', __('Category created'));
    }

    public function edit(PostCategory $category)
    {
        $parents = PostCategory::where('id', '!=', $category->id)->orderBy('name')->get();

        return view('admin.blog.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, PostCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'name.*' => 'nullable|string|max:190',
            'slug' => 'nullable|array',
            'slug.*' => 'nullable|string|max:190',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'seo_title' => 'nullable|array',
            'seo_title.*' => 'nullable|string|max:190',
            'seo_description' => 'nullable|array',
            'seo_description.*' => 'nullable|string|max:300',
            'seo_tags' => 'nullable|array',
            'seo_tags.*' => 'nullable|string|max:250',
            'parent_id' => 'nullable|exists:post_categories,id',
        ]);
        $fallback = config('app.fallback_locale');
        $defaultName = $data['name'][$fallback] ??
            collect($data['name'])->first(fn($value) => ! empty($value)) ??
            $category->getRawOriginal('name');
        $slugTranslations = $data['slug'] ?? ($category->slug_translations ?? []);
        foreach ($data['name'] as $loc => $value) {
            if (! isset($slugTranslations[$loc]) || $slugTranslations[$loc] === '') {
                $slugTranslations[$loc] = Str::slug($value ?? $defaultName ?? '');
            }
        }
        $payload = [
            'name' => $defaultName,
            'name_translations' => array_filter($data['name']),
            'slug_translations' => array_filter($slugTranslations),
            'parent_id' => $data['parent_id'] ?? null,
        ];
        // keep existing base slug (don't regenerate unless fallback changed to empty)
        $payload['slug'] = $category->getRawOriginal('slug');
        foreach (['description', 'seo_title', 'seo_description', 'seo_tags'] as $f) {
            if (isset($data[$f]) && is_array($data[$f])) {
                $payload[$f . '_translations'] = array_filter($data[$f]);
                $payload[$f] = $payload[$f . '_translations'][$fallback] ??
                    collect($payload[$f . '_translations'])->first(fn($value) => ! empty($value));
            }
        }
        $category->update($payload);

        return back()->with('success', __('Category updated'));
    }

    public function destroy(PostCategory $category)
    {
        $category->delete();

        return back()->with('success', __('Category deleted'));
    }

    // AI suggestion for blog category description & SEO
    public function aiSuggest(Request $request, \App\Services\AI\SimpleAIService $aiService)
    {
        // Get name from array or string
        $nameInput = $request->input('name');

        // Extract title from multilingual name array
        if (is_array($nameInput)) {
            $title = collect($nameInput)->filter()->first();
        } else {
            $title = $nameInput ?: $request->input('title');
        }

        // Validate title
        if (empty($title) || ! is_string($title)) {
            return back()->with('error', __('Please enter a name first'));
        }

        // Get all active languages
        $languages = \App\Models\Language::where('is_active', 1)->get();

        // Generate content for all languages
        $formattedData = [];

        $errors = [];
        foreach ($languages as $language) {
            try {
                $result = $aiService->generate($title, 'category', $language->code);

                if (isset($result['error'])) {
                    $errors[] = "Language {$language->name}: " . $result['error'];
                    continue; // Skip this language if AI fails
                }

                // Add content for this language
                if (isset($result['name'])) {
                    $formattedData['name'][$language->code] = $result['name'];
                }
                if (isset($result['description'])) {
                    $formattedData['description'][$language->code] = $result['description'];
                }
                if (isset($result['seo_title'])) {
                    $formattedData['seo_title'][$language->code] = $result['seo_title'];
                }
                if (isset($result['seo_description'])) {
                    $formattedData['seo_description'][$language->code] = $result['seo_description'];
                }
                if (isset($result['seo_tags'])) {
                    $formattedData['seo_tags'][$language->code] = $result['seo_tags'];
                }
            } catch (\Exception $e) {
                $errors[] = "Language {$language->name}: " . $e->getMessage();
            }
        }

        // Merge with existing form data
        $existingData = $request->except(['_token']);
        $mergedData = array_merge($existingData, $formattedData);

        // Prepare success message
        $successMessage = __('AI generated successfully for all languages');
        if (!empty($errors)) {
            $errorCount = count($errors);
            $successMessage .= " " . __('Some languages failed') . " ({$errorCount} " . __('errors') . ")";
        }

        return back()->with('success', $successMessage)->withInput($mergedData);
    }
}
