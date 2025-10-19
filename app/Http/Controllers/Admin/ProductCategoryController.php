<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
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
        $defaultLocale = cache()->remember('default_locale_code', 3600, function () {
            return optional(\App\Models\Language::where('is_default', 1)->first())->code ?? 'en';
        });
        $nameTranslations = $r->input('name_i18n', []);
        $descTranslations = $r->input('description_i18n', []);
        if (! empty($nameTranslations)) {
            $clean = [];
            foreach ($nameTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['name_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
        }
        if (! empty($descTranslations)) {
            $clean = [];
            foreach ($descTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['description_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
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
        ProductCategory::create($data);

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Category created');
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
        $defaultLocale = cache()->remember('default_locale_code', 3600, function () {
            return optional(\App\Models\Language::where('is_default', 1)->first())->code ?? 'en';
        });
        $nameTranslations = $r->input('name_i18n', []);
        $descTranslations = $r->input('description_i18n', []);
        if (! empty($nameTranslations)) {
            $clean = [];
            foreach ($nameTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['name_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
        }
        if (! empty($descTranslations)) {
            $clean = [];
            foreach ($descTranslations as $lc => $v) {
                $clean[$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            $data['description_translations'] = array_filter($clean, fn($v) => $v !== null && $v !== '');
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
        $productCategory->update($data);

        return redirect()->route('admin.product-categories.index')->with('success', 'Updated');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return back()->with('success', 'Deleted');
    }

    public function export(Request $r)
    {
        $fileName = 'categories_export_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];
        $columns = ['id', 'name', 'slug', 'parent_id', 'position', 'active', 'created_at'];
        $callback = function () use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            \App\Models\ProductCategory::chunk(200, function ($items) use ($out) {
                foreach ($items as $c) {
                    $row = [
                        $c->id,
                        $c->name,
                        $c->slug,
                        $c->parent_id,
                        $c->position,
                        $c->active ? 1 : 0,
                        $c->created_at,
                    ];
                    fputcsv($out, $row);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // AI suggestion for category description & SEO
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
        $apiKey = $setting->ai_openai_api_key; // decrypted accessor
        $locale = $request->locale ?: app()->getLocale();
        $cacheKey = 'ai_cat_suggest_v1:' . md5($request->name . '|' . $locale);
        if ($cached = cache()->get($cacheKey)) {
            return response()->json($cached + ['cached' => true, 'source' => 'cache']);
        }
        $perMinuteLimit = (int) env('AI_CATEGORY_RATE_PER_MIN', 8);
        $userId = auth()->id() ?: 0;
        $rateKey = 'ai_cat_rate:' . $userId . ':' . now()->format('YmdHi');
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
        $promptParts = [
            'Generate JSON with keys seo_description (<=160 chars), seo_keywords (<=12 comma keywords),',
            ' description (1-2 paragraphs informative) for a product category named "',
            $request->name,
            '". Language: ',
            $locale,
            '. Return ONLY JSON.',
        ];
        $prompt = implode('', $promptParts);
        $model = config('services.openai.model', 'gpt-4o-mini');
        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful e-commerce taxonomy assistant. Output concise valid JSON only.',
                ],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.6,
        ];
        try {
            $resp = \Http::withToken($apiKey)->acceptJson()->timeout(25)->post('https://api.openai.com/v1/chat/completions', $payload);
        } catch (\Throwable $e) {
            \Log::warning('AI category HTTP exception: ' . $e->getMessage());

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
                'retry_after' => $resp->header('Retry-After')
                    ? (int) $resp->header('Retry-After')
                    : null,
            ], $providerStatus);
        }
        $rawText = data_get($providerBody, 'choices.0.message.content');
        if (! $rawText) {
            return response()->json(['error' => 'empty_output', 'provider_status' => $providerStatus, 'provider_body' => $providerBody], 502);
        }
        $seoDescription = '';
        $seoKeywords = '';
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
            $seoKeywords = (string) ($parsed['seo_keywords'] ?? '');
            $description = (string) ($parsed['description'] ?? '');
        } else {
            $lines = preg_split('/\n+/', trim($rawText));
            foreach ($lines as $l) {
                $ll = trim($l);
                if ($seoDescription === '' && mb_strlen($ll) <= 200) {
                    $seoDescription = $ll;

                    continue;
                }
                if ($seoKeywords === '' && str_contains($ll, ',')) {
                    $seoKeywords = $ll;

                    continue;
                }
                $description .= $ll . "\n\n";
            }
        }
        if ($seoDescription === '' && $description !== '') {
            $seoDescription = mb_substr(
                preg_replace('/\s+/', ' ', trim($description)),
                0,
                160
            );
        }
        $result = [
            'seo_description' => mb_substr($seoDescription, 0, 160),
            'seo_keywords' => $seoKeywords,
            'description' => trim($description),
            'provider_status' => $providerStatus,
            'source' => 'live',
        ];
        cache()->put($cacheKey, $result, 600);

        return response()->json($result);
    }
}
