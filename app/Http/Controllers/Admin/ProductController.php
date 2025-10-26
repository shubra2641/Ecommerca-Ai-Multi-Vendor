<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Mail\ProductApproved;
use App\Mail\ProductRejected;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductSerial;
use App\Models\ProductTag;
use App\Models\ProductVariation;
use App\Models\User;
use App\Notifications\AdminStockLowNotification;
use App\Services\AI\SimpleAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $query = Product::with(['category', 'variations']);

        if (! $isAdmin) {
            $query->where('vendor_id', $user->id);
        }

        $products = $query->paginate($request->get('per_page', 10));

        $totalProducts = $query->count();
        $activeProducts = (clone $query)->where('active', true)->count();
        $featuredProducts = (clone $query)->where('is_featured', true)->count();
        $bestSellers = (clone $query)->where('is_best_seller', true)->count();

        $apiStockProducts = [];
        $apiStockVariations = [];

        foreach ($products as $product) {
            $apiStockProducts[$product->id] = $this->getStockInfo($product);
            foreach ($product->variations as $variation) {
                $apiStockVariations[$variation->id] = $this->getStockInfo($variation);
            }
        }

        return view($isAdmin ? 'admin.products.products.index' : 'vendor.products.index', compact('products', 'totalProducts', 'activeProducts', 'featuredProducts', 'bestSellers', 'apiStockProducts', 'apiStockVariations', 'isAdmin'));
    }

    public function show(Product $product)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        if (! $isAdmin && $product->vendor_id !== $user->id) {
            abort(403);
        }
        $product->load(['tags', 'variations', 'category']);

        return view($isAdmin ? 'admin.products.products.show' : 'vendor.products.show', compact('product'));
    }

    public function create()
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        return view($isAdmin ? 'admin.products.products.create' : 'vendor.products.create', $this->getFormData() + compact('isAdmin'));
    }

    public function store(ProductRequest $request)
    {
        $data = $this->prepareProductData($request->validated());
        $user = Auth::user();
        $data['vendor_id'] = $user->id;
        if ($user->role !== 'admin') {
            $data['active'] = false;
        }
        $product = Product::create($data);
        $this->syncProductRelations($product, $request);
        $this->handleNotifications($product);

        return redirect()->route('admin.products.index')->with('success', __('Product created successfully.'));
    }

    public function edit(Product $product)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        if (! $isAdmin && $product->vendor_id !== $user->id) {
            abort(403);
        }
        $product->load(['tags', 'variations', 'serials']);
        $data = array_merge($this->getFormData(), compact('product', 'isAdmin'));

        return view($isAdmin ? 'admin.products.products.edit' : 'vendor.products.edit', $data);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        if (! $isAdmin && $product->vendor_id !== $user->id) {
            abort(403);
        }
        $oldActive = $product->active;
        $data = $this->prepareProductData($request->validated());
        $product->update($data);
        $this->syncProductRelations($product, $request);
        $this->handleNotifications($product, $oldActive);

        return redirect()->route('admin.products.index')->with('success', __('Product updated successfully.'));
    }

    public function destroy(Product $product)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        if (! $isAdmin && $product->vendor_id !== $user->id) {
            abort(403);
        }
        if (! $isAdmin) {
            abort(403, 'Vendors cannot delete products.');
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', __('Product deleted successfully.'));
    }

    public function toggleStatus(Product $product)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403);
        }
        $product->update(['active' => ! $product->active]);
        $status = $product->active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', __("Product {$status} successfully."));
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => ! $product->is_featured]);
        $status = $product->is_featured ? 'featured' : 'unfeatured';

        return redirect()->back()->with('success', __("Product {$status} successfully."));
    }

    public function aiSuggest(Request $request, SimpleAIService $aiService)
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

        $result = $aiService->generate($title, 'product', $locale);

        if (isset($result['error'])) {
            return back()->with('error', $result['error'])->withInput();
        }

        // Simple approach - return with message and let user copy manually
        return back()
            ->with('success', __('AI generated successfully'))
            ->with('ai_result', $result)
            ->with('ai_locale', $locale);
    }

    protected function getFormData(): array
    {
        return [
            'categories' => Cache::remember('product_categories_ordered', 3600, fn () => ProductCategory::orderBy('name')->get()),
            'tags' => Cache::remember('product_tags_ordered', 3600, fn () => ProductTag::orderBy('name')->get()),
            'attributes' => Cache::remember('product_attributes_with_values', 3600, fn () => ProductAttribute::with('values')->orderBy('name')->get()),
        ];
    }

    protected function prepareProductData(array $validated): array
    {
        [$name, $nameTranslations] = $this->separateTranslatedField($validated['name'] ?? null);
        [$shortDescription, $shortDescTrans] = $this->separateTranslatedField($validated['short_description'] ?? null);
        [$description, $descTranslations] = $this->separateTranslatedField($validated['description'] ?? null);
        [$seoTitle, $seoTitleTrans] = $this->separateTranslatedField($validated['seo_title'] ?? null);
        [$seoDesc, $seoDescTranslations] = $this->separateTranslatedField($validated['seo_description'] ?? null);
        [$seoKeywords, $seoKeywordsTrans] = $this->separateTranslatedField($validated['seo_keywords'] ?? null);

        $slugSource = $name ?? ($nameTranslations ? $this->extractPrimaryTextFromArray($nameTranslations) : '');
        $slug = Str::slug($slugSource ?? '');
        $slugTranslations = $nameTranslations ? $this->buildSlugTranslations($nameTranslations) : null;

        return [
            'name' => $name,
            'name_translations' => $nameTranslations,
            'slug' => $slug,
            'slug_translations' => $slugTranslations,
            'sku' => $validated['sku'] ?? null,
            'description' => $description,
            'description_translations' => $descTranslations,
            'short_description' => $shortDescription,
            'short_description_translations' => $shortDescTrans,
            'price' => $validated['price'] ?? null,
            'sale_price' => $validated['sale_price'] ?? null,
            'sale_start' => $validated['sale_start'] ?? null,
            'sale_end' => $validated['sale_end'] ?? null,
            'manage_stock' => ! empty($validated['manage_stock']),
            'stock_qty' => $validated['stock_qty'] ?? 0,
            'reserved_qty' => $validated['reserved_qty'] ?? 0,
            'backorder' => ! empty($validated['backorder']),
            'weight' => $validated['weight'] ?? null,
            'length' => $validated['length'] ?? null,
            'width' => $validated['width'] ?? null,
            'height' => $validated['height'] ?? null,
            'type' => $validated['type'],
            'product_category_id' => $validated['product_category_id'],
            'vendor_id' => $validated['vendor_id'] ?? null,
            'main_image' => $validated['main_image'] ?? null,
            'gallery' => $this->cleanGallery($validated['gallery'] ?? []),
            'is_featured' => ! empty($validated['is_featured']),
            'is_best_seller' => ! empty($validated['is_best_seller']),
            'active' => ! empty($validated['active']),
            'seo_title' => $seoTitle,
            'seo_title_translations' => $seoTitleTrans,
            'seo_description' => $seoDesc,
            'seo_description_translations' => $seoDescTranslations,
            'seo_keywords' => $seoKeywords,
            'seo_keywords_translations' => $seoKeywordsTrans,
            'refund_days' => $validated['refund_days'] ?? null,
            'used_attributes' => $validated['used_attributes'] ?? [],
        ];
    }

    protected function syncProductRelations(Product $product, ProductRequest $request): void
    {
        $this->syncTags($product, $request->input('tags', []));
        $this->syncVariations($product, $request);
        $serials = $request->input('serials', []);
        if ($request->has('__serials_to_sync')) {
            $serials = (array) $request->input('__serials_to_sync', []);
        }
        $serials = is_array($serials) ? $serials : [];
        $this->syncSerials($product, $serials);
    }

    protected function syncTags(Product $product, array $tags): void
    {
        $tagIds = ProductTag::whereIn('name', $tags)->pluck('id');
        $product->tags()->sync($tagIds);
    }

    protected function syncVariations(Product $product, ProductRequest $request): void
    {
        $variations = $request->input('variations', []);
        $variationIds = [];

        foreach ($variations as $variationData) {
            if (empty($variationData['price'])) {
                continue;
            }

            $data = $this->prepareVariationData($variationData, $product);

            if (isset($variationData['id'])) {
                $variation = ProductVariation::where('product_id', $product->id)->where('id', $variationData['id'])->first();
                if ($variation) {
                    // Preserve existing values if not provided in request
                    if (empty($variationData['attributes']) && $variation->attribute_data) {
                        $data['attribute_data'] = $variation->attribute_data;
                        $data['attribute_hash'] = $variation->attribute_hash;
                    }
                    if (empty($variationData['image']) && $variation->image) {
                        $data['image'] = $variation->image;
                    }
                    $variation->update($data);
                    $variationIds[] = $variation->id;
                }
            } else {
                $variation = null;
                if (! empty($data['attribute_hash'])) {
                    $variation = $product->variations()->where('attribute_hash', $data['attribute_hash'])->first();
                }

                if ($variation) {
                    $variation->update($data);
                } else {
                    $variation = $product->variations()->create($data);
                }

                $variationIds[] = $variation->id;
            }
        }

        // Get all variation IDs that were in the form (including those we just updated/created)
        $formVariationIds = array_filter(array_column($variations, 'id'));

        // Only delete variations that:
        // 1. Were present in the form (had an ID)
        // 2. But are NOT in the updated list (meaning they were intentionally removed)
        if (! empty($formVariationIds)) {
            $product->variations()
                ->whereIn('id', $formVariationIds)
                ->whereNotIn('id', $variationIds)
                ->delete();
        }
    }

    protected function prepareVariationData(array $data, ?Product $product = null): array
    {
        [$attributes, $hash] = $this->normalizeVariationAttributes($data['attributes'] ?? []);

        // Inherit manage_stock from parent product if not explicitly set for variation
        $manageStock = isset($data['manage_stock'])
            ? ! empty($data['manage_stock'])
            : ($product ? $product->manage_stock : false);

        return [
            'name' => $data['name'] ?? null,
            'sku' => $data['sku'] ?? null,
            'price' => $data['price'],
            'sale_price' => $data['sale_price'] ?? null,
            'sale_start' => $data['sale_start'] ?? null,
            'sale_end' => $data['sale_end'] ?? null,
            'manage_stock' => $manageStock,
            'stock_qty' => $data['stock_qty'] ?? 0,
            'reserved_qty' => $data['reserved_qty'] ?? 0,
            'backorder' => ! empty($data['backorder']),
            'image' => $data['image'] ?? null,
            'attribute_data' => $attributes,
            'attribute_hash' => $hash,
            'active' => ! empty($data['active']),
        ];
    }

    protected function syncSerials(Product $product, array $serials): void
    {
        if (! is_array($serials)) {
            return;
        }
        foreach ($serials as $serial) {
            $serial = trim($serial);
            if (empty($serial)) {
                continue;
            }

            ProductSerial::firstOrCreate(['product_id' => $product->id, 'serial' => $serial]);
        }
    }

    protected function separateTranslatedField($value): array
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $locale => $val) {
                if ($val === null) {
                    $normalized[$locale] = '';

                    continue;
                }
                $normalized[$locale] = is_string($val) ? trim($val) : trim((string) $val);
            }
            $base = $this->extractPrimaryTextFromArray($normalized);
            $translations = array_filter($normalized, fn ($val) => $val !== '');

            return [$base, $translations ? $translations : null];
        }

        if ($value === null) {
            return [null, null];
        }

        $stringValue = is_string($value) ? trim($value) : trim((string) $value);

        return [$stringValue === '' ? null : $stringValue, null];
    }

    protected function extractPrimaryTextFromArray(array $values): ?string
    {
        if (empty($values)) {
            return null;
        }

        $defaultCode = $this->getDefaultLanguageCode();
        if ($defaultCode && isset($values[$defaultCode]) && $values[$defaultCode] !== '') {
            return $values[$defaultCode];
        }

        foreach ($values as $val) {
            if ($val !== '') {
                return $val;
            }
        }

        return null;
    }

    protected function getDefaultLanguageCode(): ?string
    {
        static $code = null;

        if ($code !== null) {
            return $code;
        }

        try {
            $default = Language::where('is_active', 1)->where('is_default', 1)->first();
            if ($default) {
                $code = $default->code;

                return $code;
            }

            $fallback = Language::where('is_active', 1)->first();
            if ($fallback) {
                $code = $fallback->code;
            }
        } catch (\Throwable $e) {
            $code = null;
        }

        return $code;
    }

    protected function buildSlugTranslations(?array $nameTranslations): ?array
    {
        if (empty($nameTranslations)) {
            return null;
        }

        $slugs = [];
        foreach ($nameTranslations as $locale => $value) {
            $slugs[$locale] = Str::slug((string) $value);
        }

        return $slugs ? $slugs : null;
    }

    protected function normalizeVariationAttributes($attributes): array
    {
        if (is_string($attributes)) {
            $decodedAttributes = json_decode($attributes, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $attributes = $decodedAttributes;
            }
        }

        if (! is_array($attributes)) {
            return [[], null];
        }

        $normalizedAttrs = [];
        foreach ($attributes as $slug => $value) {
            if (is_array($value)) {
                $value = $value['value'] ?? null;
            }
            if ($value === null) {
                continue;
            }
            if (is_string($value)) {
                $value = trim($value);
            } else {
                $value = trim((string) $value);
            }
            if ($value === '') {
                continue;
            }
            $normalizedAttrs[$slug] = $value;
        }

        if (empty($normalizedAttrs)) {
            return [[], null];
        }

        ksort($normalizedAttrs);
        $attrHash = hash('sha256', json_encode($normalizedAttrs));

        return [$normalizedAttrs, $attrHash];
    }

    protected function cleanGallery($gallery)
    {
        if (is_string($gallery)) {
            $gallery = json_decode($gallery, true) ? json_decode($gallery, true) : [];
        }

        return array_values(array_filter(array_map('trim', $gallery), fn ($item) => ! empty($item)));
    }

    protected function handleNotifications(Product $product, ?bool $oldActive = null): void
    {
        if ($product->manage_stock) {
            $available = (int) $product->stock_qty - (int) ($product->reserved_qty ?? 0);
            $lowThreshold = (int) config('catalog.stock_low_threshold', 5);

            if ($available <= $lowThreshold) {
                try {
                    $admins = User::where('role', 'admin')->get();
                    if ($admins->count()) {
                        Notification::sendNow($admins, new AdminStockLowNotification($product, $available));
                    }
                } catch (\Throwable $e) {
                    // Silent fail for notifications
                    null;
                }
            }
        }

        if ($oldActive !== null && $oldActive !== $product->active && $product->vendor) {
            try {
                if ($product->active) {
                    Mail::to($product->vendor->email)->queue(new ProductApproved($product));
                } else {
                    Mail::to($product->vendor->email)->queue(new ProductRejected($product, null));
                }
            } catch (\Throwable $e) {
                // Silent fail for mail
                null;
            }
        }
    }

    protected function getStockInfo($item): array
    {
        if (! $item->manage_stock) {
            return [
                'available' => null,
                'stock_qty' => null,
                'class' => '',
                'badge' => null,
                'backorder' => null,
            ];
        }

        $available = (int) $item->stock_qty - (int) ($item->reserved_qty ?? 0);
        $stockQty = (int) $item->stock_qty;
        $lowThreshold = (int) config('catalog.stock_low_threshold', 5);
        $soonThreshold = (int) config('catalog.stock_soon_threshold', 10);

        $class = '';
        $badge = null;

        if ($available <= $lowThreshold) {
            $class = 'text-danger';
            $badge = 'low';
        } elseif ($available <= $soonThreshold) {
            $class = 'text-warning';
            $badge = 'soon';
        }

        return [
            'available' => $available,
            'stock_qty' => $stockQty,
            'class' => $class,
            'badge' => $badge,
            'backorder' => $item->backorder ?? false,
        ];
    }
}
