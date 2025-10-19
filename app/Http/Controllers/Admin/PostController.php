<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    private HtmlSanitizer $sanitizer;

    public function __construct(HtmlSanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function index(Request $request)
    {
        $posts = $this->buildPostsQuery($request)->paginate(20);
        $categories = PostCategory::orderBy('name')->get();

        return view('admin.blog.posts.index', [
            'posts' => $posts,
            'q' => $request->get('q'),
            'categories' => $categories,
            'categoryId' => $request->get('category_id'),
            'published' => $request->get('published')
        ]);
    }

    public function create()
    {
        return view('admin.blog.posts.create', [
            'categories' => PostCategory::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get()
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
            'tags' => Tag::orderBy('name')->get()
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validatePostData($request);
        $payload = $this->buildPostPayload($data, $post);
        
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

    public function aiSuggest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3',
            'locale' => 'nullable|string|max:10',
        ]);

        $aiService = app(\App\Services\AI\BlogPostSuggestionService::class);
        return $aiService->generateSuggestions($request->title, $request->locale);
    }

    private function buildPostsQuery(Request $request)
    {
        $query = Post::with('category', 'author');

        if ($q = $request->get('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")
                  ->orWhere('slug', 'like', "%$q%");
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

        return $query->orderByDesc('published_at');
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

    private function buildPostPayload(array $data, ?Post $post = null): array
    {
        $fallback = config('app.fallback_locale');
        $defaultTitle = $this->getDefaultTitle($data['title'], $fallback);
        
        $payload = [
            'title' => $defaultTitle,
            'title_translations' => array_filter($data['title']),
            'slug_translations' => $this->generateSlugTranslations($data, $defaultTitle),
            'category_id' => $data['category_id'] ?? null,
            'user_id' => auth()->id(),
        ];

        // Handle slug generation
        if ($post && $this->needsSlugUpdate($post, $defaultTitle)) {
            $payload['slug'] = $this->generateUniqueSlug($defaultTitle, $post->id);
        } elseif (!$post) {
            $payload['slug'] = $this->generateUniqueSlug($defaultTitle);
        }

        // Process translatable fields
        $this->processTranslatableFields($data, $payload, $fallback);

        // Handle featured image
        if (!empty($data['featured_image_path'])) {
            $payload['featured_image'] = $this->processFeaturedImage($data['featured_image_path']);
        }

        return $payload;
    }

    private function getDefaultTitle(array $titles, string $fallback): string
    {
        return $titles[$fallback] ?? collect($titles)->first(fn($v) => !empty($v)) ?? '';
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

    private function needsSlugUpdate(Post $post, string $newTitle): bool
    {
        return $newTitle && $newTitle !== $post->getRawOriginal('title');
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $base = $slug;
        $i = 1;

        $query = Post::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $base . '-' . $i++;
            $query = Post::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    private function processTranslatableFields(array $data, array &$payload, string $fallback): void
    {
        $fields = ['excerpt', 'body', 'seo_title', 'seo_description', 'seo_tags'];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || !is_array($data[$field])) {
                continue;
            }

            $translations = array_filter($data[$field]);
            foreach ($translations as $locale => $value) {
                $translations[$locale] = $this->sanitizer->clean($value);
            }

            $payload[$field . '_translations'] = $translations;
            $payload[$field] = $translations[$fallback] ?? collect($translations)->first(fn($v) => !empty($v));
        }
    }

    private function processFeaturedImage(string $imagePath): string
    {
        return trim(str_replace(asset('storage/'), '', $imagePath), ' /');
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