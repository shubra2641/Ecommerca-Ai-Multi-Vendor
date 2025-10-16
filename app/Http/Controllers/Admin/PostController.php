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
    public function index(Request $request)
    {
        $q = $request->get('q');
        $categoryId = $request->get('category_id');
        $published = $request->get('published'); // '1','0' or null
        $posts = Post::with('category', 'author')
            ->when($q, fn ($qq) => $qq->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")->orWhere('slug', 'like', "%$q%");
            }))
            ->when($categoryId, fn ($qq) => $qq->where('category_id', $categoryId))
            ->when($published !== null && $published !== '', fn ($qq) => $qq->where('published', (bool) $published))
            ->orderByDesc('published_at')
            ->paginate(20);
        $categories = PostCategory::orderBy('name')->get();

        return view('admin.blog.posts.index', compact('posts', 'q', 'categories', 'categoryId', 'published'));
    }

    public function create()
    {
        $categories = PostCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.blog.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
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
        $fallback = config('app.fallback_locale');
        $defaultTitle = $data['title'][$fallback] ?? collect($data['title'])->first(fn ($v) => ! empty($v));
        $slug = Str::slug($defaultTitle ?? '');
        $base = $slug;
        $i = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        // Build translations arrays
        $payload = [];
        $payload['title'] = $defaultTitle;
        $payload['title_translations'] = array_filter($data['title']);
        // slug translations (generate when missing)
        $slugTranslations = $data['slug'] ?? [];
        foreach ($data['title'] as $loc => $tVal) {
            if (! isset($slugTranslations[$loc]) || $slugTranslations[$loc] === '') {
                $slugTranslations[$loc] = Str::slug($tVal ?? $defaultTitle);
            }
        }
        $payload['slug_translations'] = array_filter($slugTranslations);
        $payload['slug'] = $slug;
        foreach (['excerpt', 'body', 'seo_title', 'seo_description', 'seo_tags'] as $f) {
            if (isset($data[$f]) && is_array($data[$f])) {
                $trans = array_filter($data[$f]);
                foreach ($trans as $lc => $val) {
                    $trans[$lc] = $sanitizer->clean($val);
                }
                $payload[$f . '_translations'] = $trans;
                $payload[$f] = $payload[$f . '_translations'][$fallback] ?? collect($payload[$f . '_translations'])->first(fn ($v) => ! empty($v));
            }
        }
        $payload['category_id'] = $data['category_id'] ?? null;
        if (! empty($data['featured_image_path'])) {
            $payload['featured_image'] = trim(str_replace(asset('storage/'), '', $data['featured_image_path']), ' /');
        }
        $payload['user_id'] = auth()->id();
        $post = Post::create($payload);
        $post->tags()->sync($data['tags'] ?? []);
        $this->flushBlogCache();

        return redirect()->route('admin.blog.posts.edit', $post)->with('success', __('Post created'));
    }

    public function edit(Post $post)
    {
        $categories = PostCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.blog.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post, HtmlSanitizer $sanitizer)
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
        $fallback = config('app.fallback_locale');
        $defaultTitle = $data['title'][$fallback] ?? collect($data['title'])->first(fn ($v) => ! empty($v));
        $payload = [];
        $payload['title'] = $defaultTitle;
        $payload['title_translations'] = array_filter($data['title']);
        // Slug regeneration if title changed in fallback locale
        $needsSlug = false;
        if ($defaultTitle && $defaultTitle !== $post->getRawOriginal('title')) {
            $needsSlug = true;
        }
        $slugTranslations = $data['slug'] ?? [];
        foreach ($data['title'] as $loc => $tVal) {
            if (! isset($slugTranslations[$loc]) || $slugTranslations[$loc] === '') {
                $slugTranslations[$loc] = Str::slug($tVal ?? $defaultTitle);
            }
        }
        $payload['slug_translations'] = array_filter($slugTranslations);
        if ($needsSlug) {
            $slug = Str::slug($defaultTitle ?? '');
            $base = $slug;
            $i = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $payload['slug'] = $slug;
        }
        foreach (['excerpt', 'body', 'seo_title', 'seo_description', 'seo_tags'] as $f) {
            if (isset($data[$f]) && is_array($data[$f])) {
                $trans = array_filter($data[$f]);
                foreach ($trans as $lc => $val) {
                    $trans[$lc] = $sanitizer->clean($val);
                }
                $payload[$f . '_translations'] = $trans;
                $payload[$f] = $payload[$f . '_translations'][$fallback] ?? collect($payload[$f . '_translations'])->first(fn ($v) => ! empty($v));
            }
        }
        if (! empty($data['featured_image_path'])) {
            $payload['featured_image'] = trim(str_replace(asset('storage/'), '', $data['featured_image_path']), ' /');
        }
        $payload['category_id'] = $data['category_id'] ?? null;
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
        $post->published = ! $post->published;
        $post->published_at = $post->published ? now() : null;
        $post->save();
        $this->flushBlogCache($post);

        return back()->with('success', __('Status updated'));
    }

    protected function flushBlogCache(?Post $post = null): void
    {
        $locales = [app()->getLocale(), config('app.fallback_locale')];
        foreach ($locales as $loc) {
            foreach (range(1, 5) as $page) {
                cache()->forget("blog.index.$loc.$page");
            }
        }
        if ($post) {
            foreach ($locales as $loc) {
                cache()->forget("blog.post.$loc." . $post->slug);
                cache()->forget("blog.post.$loc." . $post->slug . '.related');
            }
            if ($post->category_id) {
                foreach ($locales as $loc) {
                    cache()->forget("blog.cat.$loc." . optional($post->category)->slug);
                }
            }
            foreach ($post->tags as $t) {
                foreach ($locales as $loc) {
                    cache()->forget("blog.tag.$loc.$t->slug");
                }
            }
        }
    }

    // AI suggestion for blog post fields (excerpt, body intro, seo_description, seo_tags)
    public function aiSuggest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3',
            'locale' => 'nullable|string|max:10',
        ]);
        $setting = \App\Models\Setting::first();
        if (! ($setting?->ai_enabled) || ($setting?->ai_provider !== 'openai')) {
            return response()->json(['error' => 'AI disabled'], 422);
        }
        if (! $setting->ai_openai_api_key) {
            return response()->json(['error' => 'Missing API key'], 422);
        }
        $apiKey = $setting->ai_openai_api_key;
        $locale = $request->locale ?: app()->getLocale();
        $cacheKey = 'ai_blog_post_v1:' . md5($request->title . '|' . $locale);
        if ($cached = cache()->get($cacheKey)) {
            return response()->json($cached + ['cached' => true, 'source' => 'cache']);
        }
        $perMinuteLimit = (int) env('AI_BLOG_POST_RATE_PER_MIN', 6);
        $userId = auth()->id() ?: 0;
        $rateKey = 'ai_blog_post_rate:' . $userId . ':' . now()->format('YmdHi');
        $count = cache()->increment($rateKey);
        if ($count === 1) {
            cache()->put($rateKey, 1, 65);
        }
        if ($count > $perMinuteLimit) {
            return response()->json([
                'error' => 'rate_limited_local',
                'source' => 'local',
                'message' => 'Too many AI requests. Please wait a minute and try again.',
                'retry_after' => 60,
                'limit' => $perMinuteLimit,
            ], 429);
        }
        $prompt = sprintf(
            "Generate JSON with keys excerpt (<=300 chars engaging summary), body_intro (2 paragraphs opening), seo_description (<=160 chars), seo_tags (<=12 comma keywords) for a blog post titled '%s'. Language: %s. Return ONLY JSON.",
            $request->title,
            $locale
        );

        $model = config('services.openai.model', 'gpt-4o-mini');
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful blogging assistant. Output concise valid JSON only.'],
            ['role' => 'user', 'content' => $prompt],
        ];
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.65,
        ];
        try {
            $resp = \Http::withToken($apiKey)->acceptJson()->timeout(25)->post('https://api.openai.com/v1/chat/completions', $payload);
        } catch (\Throwable $e) {
            \Log::warning('AI blog post HTTP exception: ' . $e->getMessage());

            return response()->json(['error' => 'connection_failed', 'message' => $e->getMessage()], 502);
        }
        $providerStatus = $resp->status();
        $providerBody = $resp->json();
        if (! $resp->ok()) {
            return response()->json([
                'error' => $providerStatus == 429 ? 'rate_limited_provider' : 'provider_error',
                'source' => 'provider',
                'provider_status' => $providerStatus,
                'provider_body' => $providerBody,
                'provider_message' => data_get($providerBody, 'error.message'),
                'retry_after' => $resp->header('Retry-After') ? (int) $resp->header('Retry-After') : null,
            ], $providerStatus);
        }
        $rawText = data_get($providerBody, 'choices.0.message.content');
        if (! $rawText) {
            return response()->json(['error' => 'empty_output', 'provider_status' => $providerStatus, 'provider_body' => $providerBody], 502);
        }
        $excerpt = '';
        $bodyIntro = '';
        $seoDescription = '';
        $seoTags = '';
        $parsed = null;
        if (preg_match('/\{.*\}/s', $rawText, $m)) {
            try {
                $parsed = json_decode($m[0], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $parsed = null;
            }
        }
        if (is_array($parsed)) {
            $excerpt = (string) ($parsed['excerpt'] ?? '');
            $bodyIntro = (string) ($parsed['body_intro'] ?? '');
            $seoDescription = (string) ($parsed['seo_description'] ?? '');
            $seoTags = (string) ($parsed['seo_tags'] ?? '');
        } else {
            $lines = preg_split('/\n+/', trim($rawText));
            foreach ($lines as $l) {
                $ll = trim($l);
                if ($excerpt === '' && mb_strlen($ll) <= 320) {
                    $excerpt = $ll;

                    continue;
                } if ($seoDescription === '' && mb_strlen($ll) <= 180) {
                    $seoDescription = $ll;

                    continue;
                } if ($seoTags === '' && str_contains($ll, ',')) {
                    $seoTags = $ll;

                    continue;
                } $bodyIntro .= $ll . "\n\n";
            }
        }
        if ($seoDescription === '' && $excerpt !== '') {
            $seoDescription = mb_substr($excerpt, 0, 160);
        }
        $result = [
            'excerpt' => mb_substr($excerpt, 0, 300),
            'body_intro' => trim($bodyIntro),
            'seo_description' => mb_substr($seoDescription, 0, 160),
            'seo_tags' => $seoTags,
            'provider_status' => $providerStatus,
            'source' => 'live',
        ];
        cache()->put($cacheKey, $result, 600);

        return response()->json($result);
    }
}
