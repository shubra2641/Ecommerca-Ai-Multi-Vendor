<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use App\Services\AI\AIFormHelper;
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

    public function store(Request $request, \App\Services\HtmlSanitizer $sanitizer)
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
        $defaultName = $data['name'][$fallback] ?? collect($data['name'])->first(fn ($v) => ! empty($v));
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
        $i = 1;
        while (PostCategory::where('slug', $unique)->exists()) {
            $unique = $baseSlug.'-'.$i++;
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
                // sanitize incoming translations
                $clean = [];
                foreach ($data[$f] as $lc => $v) {
                    $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
                }
                $payload[$f.'_translations'] = array_filter($clean);
                $payload[$f] = $payload[$f.'_translations'][$fallback] ??
                    collect($payload[$f.'_translations'])->first(fn ($v) => ! empty($v));
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

    public function update(Request $request, PostCategory $category, \App\Services\HtmlSanitizer $sanitizer)
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
            collect($data['name'])->first(fn ($v) => ! empty($v)) ??
            $category->getRawOriginal('name');
        $slugTranslations = $data['slug'] ?? ($category->slug_translations ?? []);
        foreach ($data['name'] as $loc => $v) {
            if (! isset($slugTranslations[$loc]) || $slugTranslations[$loc] === '') {
                $slugTranslations[$loc] = Str::slug($v ?? $defaultName ?? '');
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
                $clean = [];
                foreach ($data[$f] as $lc => $v) {
                    $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
                }
                $payload[$f.'_translations'] = array_filter($clean);
                $payload[$f] = $payload[$f.'_translations'][$fallback] ??
                    collect($payload[$f.'_translations'])->first(fn ($v) => ! empty($v));
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
    public function aiSuggest(Request $request, AIFormHelper $aiHelper)
    {
        return $aiHelper->handleFormGeneration($request, 'category');
    }
}
