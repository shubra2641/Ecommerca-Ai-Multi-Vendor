<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ProductRequest;
use App\Mail\ProductPendingForReview;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariation;
use App\Models\User;
use App\Notifications\AdminProductPendingReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        // Show all vendor products (active & pending) for clarity; eager-load category for listing
        $products = auth()->user()
            ->products()
            ->with('category')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('vendor.products.index', compact('products'));
    }

    public function create()
    {
        $data = $this->loadProductFormData();

        return view('vendor.products.create', $data);
    }

    public function store(ProductRequest $request)
    {
        return $this->handleProductSave($request, null);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        return $this->handleProductSave($request, $product);
    }

    private function handleProductSave(ProductRequest $request, ?Product $product): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        $this->processProductTranslations($request, $data, $product);
        $data = $this->collapsePrimaryTextFields($data, $product);
        $data['slug'] = $this->generateUniqueSlug($data, $product);

        $this->processGalleryData($data);

        $product = $this->saveProduct($data, $product);
        $this->syncProductTags($product, $request);
        $this->handleProductVariations($product, $request);

        $this->notifyAdminsOfProductSubmission($product);

        $message = $product->wasRecentlyCreated
            ? __('Product submitted for review.')
            : __('Product updated and resubmitted for review.');

        return redirect()->route('vendor.products.index')->with('success', $message);
    }

    private function processProductTranslations(ProductRequest $request, array &$data, ?Product $product): void
    {
        $this->mergeVendorTranslations($request, $data, $product);
    }

    private function processGalleryData(array &$data): void
    {
        if (isset($data['gallery'])) {
            $data['gallery'] = $this->cleanGalleryValue($data['gallery']);
        }
    }

    private function saveProduct(array $data, ?Product $product): Product
    {
        if ($product) {
            $product->fill($data);
            $product->active = false;
            $product->save();
            return $product;
        }

        return Product::create($data + [
            'vendor_id' => auth()->id(),
            'active' => false,
        ]);
    }

    private function syncProductTags(Product $product, ProductRequest $request): void
    {
        $product->tags()->sync($request->input('tag_ids', []));
    }

    private function handleProductVariations(Product $product, ProductRequest $request): void
    {
        if ($product->type === 'variable') {
            $this->syncVariations($product, $request);
        }
    }

    private function generateUniqueSlug(array $data, ?Product $product): string
    {
        $name = $this->extractProductName($data, $product);
        $baseSlug = Str::slug($name);

        if (!$this->slugExists($baseSlug, $product?->id)) {
            return $baseSlug;
        }

        return $this->generateUniqueSlugWithSuffix($baseSlug, $product?->id);
    }

    private function extractProductName(array $data, ?Product $product): string
    {
        $name = $data['name'] ?? $product?->name ?? '';

        if (is_array($name)) {
            return array_values(array_filter($name))[0] ?? '';
        }

        return $name;
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Product::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function generateUniqueSlugWithSuffix(string $baseSlug, ?int $excludeId): string
    {
        $counter = 1;
        $slug = $baseSlug . '-' . $counter;

        while ($this->slugExists($slug, $excludeId)) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $data = $this->loadProductFormData();
        $data['product'] = $product;

        return view('vendor.products.edit', $data);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return back()->with('success', __('Product deleted.'));
    }

    private function loadProductFormData(): array
    {
        $categories = Cache::remember('product_categories_ordered', 3600, function () {
            return ProductCategory::orderBy('name')->get();
        });
        $tags = Cache::remember('product_tags_ordered', 3600, function () {
            return ProductTag::orderBy('name')->get();
        });
        $attributes = Cache::remember('product_attributes_with_values', 3600, function () {
            return ProductAttribute::with('values')->orderBy('name')->get();
        });

        return compact('categories', 'tags', 'attributes');
    }

    private function notifyAdminsOfProductSubmission(Product $product): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new ProductPendingForReview($product));
                try {
                    $admin->notify(new AdminProductPendingReviewNotification($product));
                } catch (\Throwable $e) {
                    logger()->warning('Failed to send product notification: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to send product review emails: ' . $e->getMessage());
        }
    }

    private function notifyAdminsOfProductUpdate(Product $product): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new ProductPendingForReview($product));
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to send product update emails: ' . $e->getMessage());
        }
    }

    /**
     * Merge vendor submitted multilingual inputs into translation JSON arrays.
     * Similar to admin merge but simplified: we trust active languages table.
     */
    public function mergeVendorTranslations(Request $r, array &$data, ?Product $existing = null): void
    {
        try {
            $languages = $this->getActiveLanguages();
            if ($languages->isEmpty()) {
                return;
            }

            $defaultCode = $this->getDefaultLanguageCode($languages);

            foreach ($this->getTranslatableFields() as $field) {
                if ($r->has($field) && is_array($r->input($field))) {
                    $this->processFieldTranslations($r, $data, $field, $defaultCode, $existing);
                }
            }

            $this->generateSlugTranslations($data);
        } catch (\Throwable $e) {
            logger()->warning('Failed to merge vendor translations: ' . $e->getMessage());
        }
    }

    private function getActiveLanguages()
    {
        return Language::where('is_active', 1)->orderByDesc('is_default')->get();
    }

    private function getDefaultLanguageCode($languages): string
    {
        $defaultLang = $languages->firstWhere('is_default', 1) ?? $languages->first();
        return $defaultLang->code;
    }

    private function getTranslatableFields(): array
    {
        return [
            'name',
            'short_description',
            'description',
            'seo_title',
            'seo_description',
            'seo_keywords'
        ];
    }

    private function processFieldTranslations(Request $r, array &$data, string $field, string $defaultCode, ?Product $existing): void
    {
        $incoming = $r->input($field);
        $defaultVal = $this->getDefaultValue($incoming, $defaultCode, $existing, $field);
        $normalizedIncoming = $this->normalizeIncomingTranslations($incoming, $defaultVal);

        $data[$field . '_translations'] = $normalizedIncoming;
    }

    private function getDefaultValue(array $incoming, string $defaultCode, ?Product $existing, string $field)
    {
        $defaultVal = $incoming[$defaultCode] ??
            collect($incoming)->first(
                fn($v) => trim((string) $v) !== ''
            );

        return $defaultVal ?? $existing?->$field;
    }

    private function normalizeIncomingTranslations(array $incoming, $defaultVal): array
    {
        $languages = $this->getActiveLanguages();

        foreach ($languages as $lang) {
            $code = $lang->code;
            if (! isset($incoming[$code]) || trim((string) $incoming[$code]) === '') {
                $incoming[$code] = $defaultVal;
            }
        }

        return $incoming;
    }

    private function generateSlugTranslations(array &$data): void
    {
        if (! empty($data['name_translations'])) {
            $slugTranslations = [];
            foreach ($data['name_translations'] as $lc => $nm) {
                $slugTranslations[$lc] = Str::slug($nm ?? '');
            }
            $data['slug_translations'] = $slugTranslations;
        }
    }

    public function collapsePrimaryTextFields(array $data, ?Product $existing = null): array
    {
        foreach ($this->getTranslatableFields() as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = $this->extractFirstNonEmptyValue($data[$field], $existing, $field);
            }
        }

        return $data;
    }

    private function extractFirstNonEmptyValue(array $values, ?Product $existing, string $field): string
    {
        foreach ($values as $val) {
            if (is_string($val) && trim($val) !== '') {
                return $val;
            }
        }

        return (string) ($existing?->$field ?? '');
    }

    public function cleanGalleryValue($raw)
    {
        if (is_array($raw)) {
            $gallery = $raw;
        } elseif (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            $gallery = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        } else {
            $gallery = [];
        }

        return array_values(
            array_filter(
                array_map(fn($path) => is_string($path) ? trim($path) : '', $gallery),
                fn($path) => $path !== ''
            )
        );
    }

    public function syncVariations($product, Request $r): void
    {
        $payload = $r->input('variations', []);
        $ids = [];

        foreach ($payload as $v) {
            if (! isset($v['price']) || $v['price'] === '') {
                continue;
            }

            $variationData = $this->prepareVariationData($v);
            $id = $this->saveVariation($product, $variationData, $v['id'] ?? null);
            if ($id) {
                $ids[] = $id;
            }
        }

        $product->variations()->whereNotIn('id', $ids)->delete();
    }

    private function prepareVariationData(array $v): array
    {
        $attrRaw = $v['attributes'] ?? [];
        if (is_string($attrRaw)) {
            $decoded = json_decode($attrRaw, true);
            $attrRaw = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        $data = [
            'name' => is_array($v['name'] ?? null) ? null : ($v['name'] ?? null),
            'sku' => $v['sku'] ?? null,
            'price' => $v['price'],
            'sale_price' => $v['sale_price'] ?? null,
            'sale_start' => $v['sale_start'] ?? null,
            'sale_end' => $v['sale_end'] ?? null,
            'manage_stock' => ! empty($v['manage_stock']),
            'stock_qty' => $v['stock_qty'] ?? 0,
            'reserved_qty' => $v['reserved_qty'] ?? 0,
            'backorder' => ! empty($v['backorder']),
            'image' => $v['image'] ?? null,
            'attribute_data' => $attrRaw,
            'active' => ! empty($v['active']),
        ];

        if (! empty($v['name']) && is_array($v['name'])) {
            $this->processVariationNameTranslations($data, $v['name']);
        }

        return $data;
    }

    private function processVariationNameTranslations(array &$data, array $nameTranslations): void
    {
        try {
            $languages = Language::where('is_active', 1)->orderByDesc('is_default')->get();
            if ($languages->count()) {
                $default = optional($languages->firstWhere('is_default', 1))->code ?? $languages->first()->code;
                $translations = $nameTranslations;
                $defaultVal = $translations[$default] ??
                    collect($translations)->first(
                        fn($val) => trim((string) $val) !== ''
                    );

                foreach ($languages as $lang) {
                    $code = $lang->code;
                    if (! isset($translations[$code]) || trim((string) $translations[$code]) === '') {
                        $translations[$code] = $defaultVal;
                    }
                }

                $data['name_translations'] = $translations;
                $data['name'] = $defaultVal;
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed to process variation translations: ' . $e->getMessage());
        }
    }

    private function saveVariation(Product $product, array $data, $id): ?int
    {
        if ($id) {
            $variation = ProductVariation::where('product_id', $product->id)
                ->where('id', $id)
                ->first();
            if ($variation) {
                $variation->update($data);
                return $variation->id;
            }
        } else {
            return $product->variations()->create($data)->id;
        }

        return null;
    }
}
