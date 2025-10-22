<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Services\HtmlSanitizer;
use App\Services\AI\AIFormHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('category', 'author');

        if ($q = $request->get('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")->orWhere('slug', 'like', "%$q%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($published = $request->get('published')) {
            if ($published !== '') {
                $query->where('published', (bool) $published);
            }
        }

        $posts = $query->orderByDesc('published_at')->paginate(20);
        $categories = PostCategory::orderBy('name')->get();

        return view('admin.blog.posts.index', compact('posts', 'q', 'categories', 'categoryId', 'published'));
    }

    public function create()
    {
        return view('admin.blog.posts.create', [
            'categories' => PostCategory::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get()
        ]);
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $this->validatePostData($request);
        $payload = $this->buildPostPayload($data, $sanitizer);

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
            'tags' => Tag::orderBy('name')->get()
        ]);
    }

    public function update(Request $request, Post $post, HtmlSanitizer $sanitizer)
    {
        $data = $this->validatePostData($request);
        $payload = $this->buildPostPayload($data, $sanitizer, $post);

        $post->update($payload);
        $post->tags()->sync($data['tags'] ?? []);
        $this->flushBlogCache($post);

        return back()->with('success', __('Post updated'));
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
            'published' => !$post->published,
            'published_at' => !$post->published ? null : now()
        ]);

        $this->flushBlogCache($post);
        return back()->with('success', __('Status updated'));
    }

    public function aiSuggest(Request $request, AIFormHelper $aiHelper)
    {
        return $aiHelper->handleFormGeneration($request, 'blog');
    }

    private function validatePostData(Request $request): array
    {
        return $request->validate([
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
    }

    private function buildPostPayload(array $data, HtmlSanitizer $sanitizer, ?Post $post = null): array
    {
        $fallback = config('app.fallback_locale');
        $defaultTitle = $data['title'][$fallback] ?? collect($data['title'])->first(fn($v) => !empty($v)) ?? '';

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
        } elseif (!$post) {
            $payload['slug'] = $this->generateUniqueSlug($defaultTitle);
        }

        // Process translatable fields
        foreach (['excerpt', 'body', 'seo_title', 'seo_description', 'seo_tags'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $translations = array_filter($data[$field]);
                foreach ($translations as $locale => $value) {
                    $translations[$locale] = $sanitizer->clean($value);
                }
                $payload[$field . '_translations'] = $translations;
                $payload[$field] = $translations[$fallback] ?? collect($translations)->first(fn($v) => !empty($v));
            }
        }

        // Handle featured image
        if (!empty($data['featured_image_path'])) {
            $payload['featured_image'] = trim(str_replace(asset('storage/'), '', $data['featured_image_path']), ' /');
        }

        return $payload;
    }

    private function generateSlugTranslations(array $data, string $defaultTitle): array
    {
        $slugTranslations = $data['slug'] ?? [];
        foreach ($data['title'] as $locale => $title) {
            if (!isset($slugTranslations[$locale]) || $slugTranslations[$locale] === '') {
                $slugTranslations[$locale] = Str::slug($title ?: $defaultTitle);
            }
        }
        return array_filter($slugTranslations);
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $base = $slug;
        $i = 1;

        while (Post::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    private function flushBlogCache(?Post $post = null): void
    {
        $locales = [app()->getLocale(), config('app.fallback_locale')];

        // Clear pagination cache
        foreach ($locales as $locale) {
            foreach (range(1, 5) as $page) {
                cache()->forget("blog.index.$locale.$page");
            }
        }

        if (!$post) {
            return;
        }

        // Clear post-specific cache
        foreach ($locales as $locale) {
            cache()->forget("blog.post.$locale." . $post->slug);
            cache()->forget("blog.post.$locale." . $post->slug . '.related');
        }

        // Clear category cache
        if ($post->category_id) {
            foreach ($locales as $locale) {
                cache()->forget("blog.cat.$locale." . optional($post->category)->slug);
            }
        }

        // Clear tag cache
        foreach ($post->tags as $tag) {
            foreach ($locales as $locale) {
                cache()->forget("blog.tag.$locale.$tag->slug");
            }
        }
    }
}
