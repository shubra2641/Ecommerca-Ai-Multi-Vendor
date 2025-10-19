<?php

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
            $unique = $baseSlug . '-' . $i++;
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
                $payload[$f . '_translations'] = array_filter($clean);
                $payload[$f] = $payload[$f . '_translations'][$fallback] ??
                    collect($payload[$f . '_translations'])->first(fn ($v) => ! empty($v));
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
                $payload[$f . '_translations'] = array_filter($clean);
                $payload[$f] = $payload[$f . '_translations'][$fallback] ??
                    collect($payload[$f . '_translations'])->first(fn ($v) => ! empty($v));
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
    public function aiSuggest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
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
        $cacheKey = 'ai_blog_cat_v1:' . md5($request->name . '|' . $locale);
        if ($cached = cache()->get($cacheKey)) {
            return response()->json($cached + ['cached' => true, 'source' => 'cache']);
        }
        $perMinuteLimit = (int) env('AI_BLOG_CATEGORY_RATE_PER_MIN', 6);
        $userId = auth()->id() ?: 0;
        $rateKey = 'ai_blog_cat_rate:' . $userId . ':' . now()->format('YmdHi');
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
            "Generate JSON with keys seo_description (<=160 chars), " .
            "seo_tags (<=12 comma keywords), description (1-2 paragraphs) " .
            "for a blog category named '%s'. Language: %s. Return ONLY JSON.",
            $request->name,
            $locale
        );
        $model = config('services.openai.model', 'gpt-4o-mini');
        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system',
                    'content' => 'You are a helpful blogging taxonomy assistant. ' .
                        'Output concise valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.6,
        ];
        try {
            $resp = \Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(25)
                ->post('https://api.openai.com/v1/chat/completions', $payload);
        } catch (\Throwable $e) {
            \Log::warning('AI blog category HTTP exception: ' . $e->getMessage());

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
                'retry_after' => $resp->header('Retry-After') ?
                    (int) $resp->header('Retry-After') : null,
            ], $providerStatus);
        }
        $rawText = data_get($providerBody, 'choices.0.message.content');
        if (! $rawText) {
            return response()->json([
                'error' => 'empty_output',
                'provider_status' => $providerStatus,
                'provider_body' => $providerBody
            ], 502);
        }
        $seoDescription = '';
        $seoTags = '';
        $description = '';
        $parsed = null;
        if (preg_match('/\{.*\}/s', $rawText, $m)) {
            try {
                $parsed = json_decode($m[0], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $parsed = null;
            }
        }
        if (is_array($parsed)) {
            $seoDescription = (string) ($parsed['seo_description'] ?? '');
            $seoTags = (string) ($parsed['seo_tags'] ?? '');
            $description = (string) ($parsed['description'] ?? '');
        } else {
            $lines = preg_split('/\n+/', trim($rawText));
            foreach ($lines as $l) {
                $ll = trim($l);
                if ($seoDescription === '' && mb_strlen($ll) <= 200) {
                    $seoDescription = $ll;

                    continue;
                } if ($seoTags === '' && str_contains($ll, ',')) {
                    $seoTags = $ll;

                    continue;
                } $description .= $ll . "\n\n";
            }
        }
        if ($seoDescription === '' && $description !== '') {
            $seoDescription = mb_substr(preg_replace('/\s+/', ' ', trim($description)), 0, 160);
        }
        $result = [
            'seo_description' => mb_substr($seoDescription, 0, 160),
            'seo_tags' => $seoTags,
            'description' => trim($description),
            'provider_status' => $providerStatus,
            'source' => 'live',
        ];
        cache()->put($cacheKey, $result, 600);

        return response()->json($result);
    }
}
