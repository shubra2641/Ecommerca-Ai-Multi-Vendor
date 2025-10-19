<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\ProductSerial;
use App\Models\Setting;
use App\Models\User;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display products list
     */
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|integer|exists:product_categories,id',
            'type' => 'nullable|string|max:50',
            'flag' => 'nullable|in:featured,best,inactive',
            'stock' => 'nullable|in:na,low,soon,in',
        ]);

        $query = Product::with(['category', 'variations']);

        // Search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('product_category_id', $request->input('category'));
        }

        // Type filter
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Flag filter
        if ($flag = $request->input('flag')) {
            switch ($flag) {
                case 'featured':
                    $query->where('is_featured', 1);
                    break;
                case 'best':
                    $query->where('is_best_seller', 1);
                    break;
                case 'inactive':
                    $query->where('active', 0);
                    break;
            }
        }

        // Stock filter
        if ($stock = $request->input('stock')) {
            $this->applyStockFilter($query, $stock);
        }

        $products = $query->latest()->paginate(40)->withQueryString();

        return view('admin.products.products.index', compact('products'));
    }

    /**
     * Show product details
     */
    public function show(Product $product)
    {
        $product->load(['tags', 'variations', 'category']);
        return view('admin.products.products.show', compact('product'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = $this->getFormData();
        return view('admin.products.products.create', $data);
    }

    /**
     * Store new product
     */
    public function store(ProductRequest $request, HtmlSanitizer $sanitizer)
    {
        $data = $this->prepareProductData($request, $sanitizer);
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        $product = Product::create($data);

        // Sync relationships
        $this->syncProductRelations($product, $request);

        // Handle variations
        if ($product->type === 'variable') {
            $this->syncVariations($product, $request);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Product created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Product $product)
    {
        $product->load(['tags', 'variations']);
        $data = $this->getFormData();
        $data['product'] = $product;

        return view('admin.products.products.edit', $data);
    }

    /**
     * Update product
     */
    public function update(ProductRequest $request, Product $product, HtmlSanitizer $sanitizer)
    {
        $data = $this->prepareProductData($request, $sanitizer, $product);
        $data['slug'] = $this->generateUniqueSlug($data['name'], $product->id);

        $oldActive = $product->active;
        $product->update($data);

        // Sync relationships
        $this->syncProductRelations($product, $request);

        // Handle variations
        if ($product->type === 'variable') {
            $this->syncVariations($product, $request);
        }

        // Handle notifications
        $this->handleProductNotifications($product, $oldActive);

        return back()->with('success', 'Product updated successfully');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted successfully');
    }

    /**
     * Export products to CSV
     */
    public function export(Request $request)
    {
        $fileName = 'products_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $columns = ['id', 'name', 'sku', 'type', 'price', 'sale_price', 'active', 'created_at'];

        $callback = function () use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);

            Product::with('tags')->chunk(200, function ($items) use ($out) {
                foreach ($items as $product) {
                    fputcsv($out, [
                        $product->id,
                        $product->name,
                        $product->sku,
                        $product->type,
                        $product->price,
                        $product->sale_price,
                        $product->active ? 1 : 0,
                        $product->created_at,
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export variations to CSV
     */
    public function variationsExport(Request $request)
    {
        $fileName = 'variations_inventory_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $columns = [
            'product_id',
            'product_name',
            'variation_id',
            'sku',
            'manage_stock',
            'stock_qty',
            'reserved_qty',
            'available_stock'
        ];

        $callback = function () use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);

            ProductVariation::with('product')->chunk(200, function ($items) use ($out) {
                foreach ($items as $variation) {
                    $available = ($variation->stock_qty ?? 0) - ($variation->reserved_qty ?? 0);
                    fputcsv($out, [
                        $variation->product_id,
                        $variation->product?->name,
                        $variation->id,
                        $variation->sku,
                        $variation->manage_stock ? 1 : 0,
                        $variation->stock_qty ?? 0,
                        $variation->reserved_qty ?? 0,
                        $available,
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * AI suggestion for product content
     */
    public function aiSuggest(Request $request)
    {
        $this->authorize('access-admin');

        $request->validate([
            'name' => 'required|string|min:3',
            'locale' => 'nullable|string|max:10',
        ]);

        $setting = Setting::first();
        if (!$setting?->ai_enabled || $setting?->ai_provider !== 'openai') {
            return response()->json(['error' => 'AI disabled'], 422);
        }

        if (!$setting->ai_openai_api_key) {
            return response()->json(['error' => 'Missing API key'], 422);
        }

        $locale = $request->locale ?: app()->getLocale();
        $cacheKey = 'ai_suggest_cache:' . md5($request->name . '|' . $locale);

        if ($cached = Cache::get($cacheKey)) {
            return response()->json($cached + ['cached' => true]);
        }

        // Rate limiting
        $userId = auth()->id() ?: 0;
        $rateKey = 'ai_suggest_rate:' . $userId . ':' . now()->format('YmdHi');
        $count = Cache::increment($rateKey);

        if ($count === 1) {
            Cache::put($rateKey, 1, 65);
        }

        $perMinuteLimit = (int) env('AI_SUGGEST_RATE_PER_MIN', 10);
        if ($count > $perMinuteLimit) {
            return response()->json([
                'error' => 'rate_limited',
                'message' => 'Too many AI requests. Please wait a minute and try again.',
            ], 429);
        }

        $result = $this->callOpenAI($request->name, $locale, $setting->ai_openai_api_key);

        if (isset($result['error'])) {
            return response()->json($result, 422);
        }

        Cache::put($cacheKey, $result, 600);
        return response()->json($result);
    }

    /**
     * Get form data (categories, tags, attributes)
     */
    private function getFormData()
    {
        return [
            'categories' => Cache::remember('product_categories_ordered', 3600, function () {
                return ProductCategory::orderBy('name')->get();
            }),
            'tags' => Cache::remember('product_tags_ordered', 3600, function () {
                return ProductTag::orderBy('name')->get();
            }),
            'attributes' => Cache::remember('product_attributes_with_values', 3600, function () {
                return ProductAttribute::with('values')->orderBy('name')->get();
            }),
        ];
    }

    /**
     * Prepare product data for storage
     */
    private function prepareProductData(ProductRequest $request, HtmlSanitizer $sanitizer, ?Product $product = null)
    {
        $data = $request->validated();

        // Handle translations
        $this->handleTranslations($request, $data);

        // Sanitize HTML fields
        $this->sanitizeHtmlFields($data, $sanitizer);

        // Clean gallery data
        if (isset($data['gallery'])) {
            $data['gallery'] = $this->cleanGallery($data['gallery']);
        }

        // Handle serials
        if ($request->filled('__serials_to_sync')) {
            $this->syncSerials($product, $request->input('__serials_to_sync'));
        }

        return $data;
    }

    /**
     * Handle product translations
     */
    private function handleTranslations(ProductRequest $request, array &$data)
    {
        $translationFields = [
            'name',
            'short_description',
            'description',
            'seo_title',
            'seo_description',
            'seo_keywords'
        ];

        foreach ($translationFields as $field) {
            if ($request->has($field) && is_array($request->input($field))) {
                $translations = $request->input($field);
                $data[$field] = $this->getDefaultTranslation($translations);
                $data[$field . '_translations'] = $translations;
            }
        }
    }

    /**
     * Get default translation value
     */
    private function getDefaultTranslation(array $translations)
    {
        $defaultLocale = config('app.fallback_locale');
        return $translations[$defaultLocale] ?? collect($translations)->first(fn($v) => !empty($v));
    }

    /**
     * Sanitize HTML fields
     */
    private function sanitizeHtmlFields(array &$data, HtmlSanitizer $sanitizer)
    {
        $htmlFields = ['short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords'];

        foreach ($htmlFields as $field) {
            if (isset($data[$field . '_translations'])) {
                foreach ($data[$field . '_translations'] as $locale => $value) {
                    $data[$field . '_translations'][$locale] = $sanitizer->clean($value);
                }
            }
        }
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null)
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Sync product relationships
     */
    private function syncProductRelations(Product $product, ProductRequest $request)
    {
        $product->tags()->sync($request->input('tag_ids', []));
    }

    /**
     * Sync product variations
     */
    private function syncVariations(Product $product, ProductRequest $request)
    {
        $variations = $request->input('variations', []);
        $variationIds = [];

        foreach ($variations as $variationData) {
            if (empty($variationData['price'])) {
                continue;
            }

            $data = $this->prepareVariationData($variationData);

            if (isset($variationData['id'])) {
                $variation = ProductVariation::where('product_id', $product->id)
                    ->where('id', $variationData['id'])
                    ->first();
                if ($variation) {
                    $variation->update($data);
                    $variationIds[] = $variation->id;
                }
            } else {
                $variation = $product->variations()->create($data);
                $variationIds[] = $variation->id;
            }
        }

        // Delete unused variations
        $product->variations()->whereNotIn('id', $variationIds)->delete();
    }

    /**
     * Prepare variation data
     */
    private function prepareVariationData(array $data)
    {
        return [
            'name' => $data['name'] ?? null,
            'sku' => $data['sku'] ?? null,
            'price' => $data['price'],
            'sale_price' => $data['sale_price'] ?? null,
            'sale_start' => $data['sale_start'] ?? null,
            'sale_end' => $data['sale_end'] ?? null,
            'manage_stock' => !empty($data['manage_stock']),
            'stock_qty' => $data['stock_qty'] ?? 0,
            'reserved_qty' => $data['reserved_qty'] ?? 0,
            'backorder' => !empty($data['backorder']),
            'image' => $data['image'] ?? null,
            'attribute_data' => $data['attributes'] ?? [],
            'active' => !empty($data['active']),
        ];
    }

    /**
     * Sync product serials
     */
    private function syncSerials(?Product $product, array $serials)
    {
        if (!$product) {
            return;
        }

        foreach ($serials as $serial) {
            $serial = trim($serial);
            if (empty($serial)) {
                continue;
            }

            ProductSerial::firstOrCreate([
                'product_id' => $product->id,
                'serial' => $serial,
            ]);
        }
    }

    /**
     * Clean gallery data
     */
    private function cleanGallery($gallery)
    {
        if (is_string($gallery)) {
            $gallery = json_decode($gallery, true) ?: [];
        }

        return array_values(array_filter(array_map('trim', $gallery), fn($v) => !empty($v)));
    }

    /**
     * Apply stock filter
     */
    private function applyStockFilter($query, string $stock)
    {
        $low = config('catalog.stock_low_threshold', 5);
        $soon = config('catalog.stock_soon_threshold', 10);

        switch ($stock) {
            case 'na':
                $query->where(function ($q) {
                    $q->where('manage_stock', 0)
                        ->orWhereNull('manage_stock');
                });
                break;
            case 'low':
                $query->where('manage_stock', 1)
                    ->whereRaw('(stock_qty - COALESCE(reserved_qty,0)) <= ?', [$low]);
                break;
            case 'soon':
                $query->where('manage_stock', 1)
                    ->whereRaw('(stock_qty - COALESCE(reserved_qty,0)) > ? AND ' .
                        '(stock_qty - COALESCE(reserved_qty,0)) <= ?', [$low, $soon]);
                break;
            case 'in':
                $query->where('manage_stock', 1)
                    ->whereRaw('(stock_qty - COALESCE(reserved_qty,0)) > ?', [$soon]);
                break;
        }
    }

    /**
     * Handle product notifications
     */
    private function handleProductNotifications(Product $product, bool $oldActive)
    {
        // Stock low notification
        if ($product->manage_stock) {
            $available = (int) $product->stock_qty - (int) ($product->reserved_qty ?? 0);
            $lowThreshold = (int) config('catalog.stock_low_threshold', 5);

            if ($available <= $lowThreshold) {
                try {
                    $admins = User::where('role', 'admin')->get();
                    if ($admins->count()) {
                        \Illuminate\Support\Facades\Notification::sendNow(
                            $admins,
                            new \App\Notifications\AdminStockLowNotification($product, $available)
                        );
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed sending stock low notification: ' . $e->getMessage());
                }
            }
        }

        // Product status change notification
        if ($oldActive !== $product->active && $product->vendor) {
            try {
                if ($product->active) {
                    \Illuminate\Support\Facades\Mail::to($product->vendor->email)
                        ->queue(new \App\Mail\ProductApproved($product));
                } else {
                    \Illuminate\Support\Facades\Mail::to($product->vendor->email)
                        ->queue(new \App\Mail\ProductRejected($product, null));
                }
            } catch (\Throwable $e) {
                Log::warning('Failed sending product status mail: ' .
                    $e->getMessage());
            }
        }
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $name, string $locale, string $apiKey)
    {
        $prompt = "Generate JSON with keys short_description (<=200 chars), " .
            "seo_description (<=160 chars), seo_keywords (<=12 comma keywords), " .
            "description (2 paragraphs) based on product name: \"{$name}\" " .
            "Language: {$locale}. Return ONLY JSON.";

        $payload = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are a product copy assistant. Output concise valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.6,
        ];

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(25)
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if (!$response->ok()) {
                return [
                    'error' => 'provider_error',
                    'message' => 'AI service unavailable',
                ];
            }

            $content = $response->json('choices.0.message.content');
            if (!$content) {
                return [
                    'error' => 'empty_output',
                    'message' => 'No content generated',
                ];
            }

            return $this->parseAIResponse($content);
        } catch (\Throwable $e) {
            Log::warning('AI HTTP exception: ' . $e->getMessage());
            return [
                'error' => 'connection_failed',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Parse AI response
     */
    private function parseAIResponse(string $content)
    {
        // Try to extract JSON
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            try {
                $parsed = json_decode($matches[0], true, 512, JSON_THROW_ON_ERROR);
                return [
                    'short_description' => mb_substr($parsed['short_description'] ?? '', 0, 200),
                    'seo_description' => mb_substr($parsed['seo_description'] ?? '', 0, 160),
                    'seo_keywords' => $parsed['seo_keywords'] ?? '',
                    'description' => trim($parsed['description'] ?? ''),
                ];
            } catch (\Throwable $e) {
                // Fall through to heuristic parsing
            }
        }

        // Heuristic parsing fallback
        $lines = preg_split('/\n+/', trim($content));
        $result = [
            'short_description' => '',
            'seo_description' => '',
            'seo_keywords' => '',
            'description' => '',
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (empty($result['short_description']) && mb_strlen($line) <= 200) {
                $result['short_description'] = $line;
            } elseif (empty($result['seo_description']) && mb_strlen($line) <= 160) {
                $result['seo_description'] = $line;
            } elseif (empty($result['seo_keywords']) && str_contains($line, ',')) {
                $result['seo_keywords'] = $line;
            } else {
                $result['description'] .= $line . "\n\n";
            }
        }

        return [
            'short_description' => mb_substr($result['short_description'], 0, 200),
            'seo_description' => mb_substr($result['seo_description'], 0, 160),
            'seo_keywords' => $result['seo_keywords'],
            'description' => trim($result['description']),
        ];
    }
}
