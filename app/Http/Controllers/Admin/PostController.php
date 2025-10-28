<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Services\AI\SimpleAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category', 'author')->orderByDesc('published_at')->paginate(20);

        return view('admin.blog.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.posts.create', [
            'categories' => PostCategory::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePostData($request);
        $payload = $this->buildPostPayload($data);

        $post = Post::create($payload);
        $post->tags()->sync($data['tags'] ?? []);
        $this->flushBlogCache();

        return redirect()->route('admin.blog.posts.edit', $post)
            ->with('success', __('Post created'));
    }

    public function edit(Post $post)
    {
        return view('admin.blog.posts.edit', [
            'post' => $post,
            'categories' => PostCategory::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        try {
            $data = $this->validatePostData($request);
            $payload = $this->buildPostPayload($data, $post);

            $post->update($payload);
            $post->tags()->sync($data['tags'] ?? []);
            $this->flushBlogCache($post);

            return back()->with('success', __('Post updated'));
        } catch (\Exception $e) {
            return back()->with('error', __('Update failed: ') . $e->getMessage());
        }
    }

    public function destroy(Post $post)
    {
        $post->delete();
        $this->flushBlogCache($post);

        return back()->with('success', __('Post deleted'));
    }

    public function publishToggle(Post $post)
    {
        $post->update([
            'published' => ! $post->published,
            'published_at' => ! $post->published ? null : now(),
        ]);

        $this->flushBlogCache($post);

        return back()->with('success', __('Status updated'));
    }

    public function aiSuggest(Request $request, SimpleAIService $aiService)
    {
        // Get name from array or string
        $nameInput = $request->input('title');

        // Extract title from multilingual name array
        if (is_array($nameInput)) {
            $title = collect($nameInput)->filter()->first();
        } else {
            $title = $nameInput ?: $request->input('name');
        }

        // Validate title
        if (empty($title) || ! is_string($title)) {
            return back()->with('error', __('Please enter a title first'));
        }

        // Get all active languages
        $languages = \App\Models\Language::where('is_active', 1)->get();

        // Generate content for all languages
        $formattedData = [];

        $errors = [];
        foreach ($languages as $language) {
            try {
                $result = $aiService->generate($title, 'blog', $language->code);

                if (isset($result['error'])) {
                    $errors[] = "Language {$language->name}: " . $result['error'];
                    continue; // Skip this language if AI fails
                }

                // Add content for this language
                if (isset($result['title'])) {
                    $formattedData['title'][$language->code] = $result['title'];
                }
                if (isset($result['excerpt'])) {
                    $formattedData['excerpt'][$language->code] = $result['excerpt'];
                }
                if (isset($result['body'])) {
                    $formattedData['body'][$language->code] = $result['body'];
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

    private function validatePostData(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string|max:190',
            'slug' => 'nullable|array',
            'slug.*' => 'nullable|string|max:190',
            'excerpt' => 'nullable|array',
            'excerpt.*' => 'nullable|string|max:300',
            'body' => 'nullable|array',
            'body.*' => 'nullable|string',
            'seo_title' => 'nullable|array',
            'seo_title.*' => 'nullable|string|max:190',
            'seo_description' => 'nullable|array',
            'seo_description.*' => 'nullable|string|max:300',
            'seo_tags' => 'nullable|array',
            'seo_tags.*' => 'nullable|string|max:250',
            'category_id' => 'nullable|exists:post_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
            'featured_image_path' => 'nullable|string',
        ]);

        // Ensure all multilingual fields are arrays
        foreach (['title', 'slug', 'excerpt', 'body', 'seo_title', 'seo_description', 'seo_tags'] as $field) {
            if (isset($data[$field]) && !is_array($data[$field])) {
                $data[$field] = [$data[$field]];
            }
        }

        return $data;
    }

    private function buildPostPayload(array $data, ?Post $post = null): array
    {
        $fallback = config('app.fallback_locale');
        $defaultTitle = $data['title'][$fallback] ?? collect($data['title'])->first(fn($v) => ! empty($v)) ?? '';

        $payload = [
            'title' => $defaultTitle,
            'title_translations' => array_filter($data['title']),
            'slug_translations' => $this->generateSlugTranslations($data, $defaultTitle),
            'category_id' => $data['category_id'] ?? null,
            'user_id' => auth()->id(),
        ];

        // Handle slug
        if ($post && $defaultTitle !== $post->getRawOriginal('title')) {
            $payload['slug'] = $this->generateUniqueSlug($defaultTitle, $post->id);
        } elseif (! $post) {
            $payload['slug'] = $this->generateUniqueSlug($defaultTitle);
        }

        // Process translatable fields
        foreach (['excerpt', 'body', 'seo_title', 'seo_description', 'seo_tags'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $translations = array_filter($data[$field]);
                $payload[$field . '_translations'] = $translations;
                $payload[$field] = $translations[$fallback] ?? collect($translations)->first(fn($v) => ! empty($v));
            }
        }

        // Handle featured image
        if (! empty($data['featured_image_path'])) {
            $storageUrl = \App\Helpers\GlobalHelper::storageImageUrl('');
            $payload['featured_image'] = $storageUrl ? trim(str_replace($storageUrl, '', $data['featured_image_path']), ' /') : $data['featured_image_path'];
        }

        return $payload;
    }

    private function generateSlugTranslations(array $data, string $defaultTitle): array
    {
        $slugTranslations = $data['slug'] ?? [];
        foreach ($data['title'] as $locale => $title) {
            if (! isset($slugTranslations[$locale]) || $slugTranslations[$locale] === '') {
                $slugTranslations[$locale] = Str::slug($title ? $title : $defaultTitle);
            }
        }

        return array_filter($slugTranslations);
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $base = $slug;
        $counter = 1;

        while (Post::withTrashed()->where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function flushBlogCache(?Post $post = null): void
    {
        $locales = [app()->getLocale(), config('app.fallback_locale')];

        // Clear pagination cache
        foreach ($locales as $locale) {
            foreach (range(1, 5) as $page) {
                cache()->forget("blog.index.{$locale}.{$page}");
            }
        }

        if (! $post) {
            return;
        }

        // Clear post-specific cache
        foreach ($locales as $locale) {
            cache()->forget("blog.post.{$locale}." . $post->slug);
            cache()->forget("blog.post.{$locale}." . $post->slug . '.related');
        }

        // Clear category cache
        if ($post->category_id) {
            foreach ($locales as $locale) {
                cache()->forget("blog.cat.{$locale}." . optional($post->category)->slug);
            }
        }

        // Clear tag cache
        foreach ($post->tags as $tag) {
            foreach ($locales as $locale) {
                cache()->forget("blog.tag.{$locale}.{$tag->slug}");
            }
        }
    }
}
