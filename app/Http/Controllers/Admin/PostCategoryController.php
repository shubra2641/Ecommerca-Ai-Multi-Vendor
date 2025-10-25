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
        $locale = $request->input('locale');

        // Extract title from multilingual name array
        if (is_array($nameInput)) {
            // If locale specified, try to get title from that locale
            if ($locale && ! empty($nameInput[$locale])) {
                $title = $nameInput[$locale];
            } else {
                // Otherwise get first non-empty value
                $title = collect($nameInput)->filter()->first();
            }
        } else {
            $title = $nameInput ? $nameInput : $request->input('title');
        }

        // Validate title - ensure it's a string
        if (empty($title) || ! is_string($title)) {
            return back()->with('error', __('Please enter a name first'));
        }

        $result = $aiService->generate($title, 'category', $locale);

        if (isset($result['error'])) {
            return back()->with('error', $result['error'])->withInput();
        }

        // Merge with existing form data to preserve user input
        $existingData = $request->except(['_token']);
        $mergedData = array_merge($existingData, $result);

        return back()->with('success', __('AI generated successfully'))->withInput($mergedData);
    }
}
