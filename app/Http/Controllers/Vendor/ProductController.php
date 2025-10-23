<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ProductRequest;
use App\Mail\ProductPendingForReview;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Http\Request;
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
        $categories = \Illuminate\Support\Facades\Cache::remember('product_categories_ordered', 3600, function () {
            return ProductCategory::orderBy('name')->get();
        });
        $tags = \Illuminate\Support\Facades\Cache::remember('product_tags_ordered', 3600, function () {
            return ProductTag::orderBy('name')->get();
        });
        $attributes = \Illuminate\Support\Facades\Cache::remember('product_attributes_with_values', 3600, function () {
            return ProductAttribute::with('values')->orderBy('name')->get();
        });

        return view('vendor.products.create', compact('categories', 'tags', 'attributes'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // Build translations arrays (name/short_description/description) like admin flow
        $this->mergeVendorTranslations($request, $data);

        // Collapse to base columns after building *_translations arrays (base = default language value)
        $data = $this->collapsePrimaryTextFields($data);

        // prepare slug similar to admin
        $defaultName = $data['name'] ?? '';
        $slug = Str::slug(
            is_array($defaultName)
                ? (array_values(array_filter($defaultName))[0] ?? '')
                : $defaultName
        );
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        $data['slug'] = $slug;
        if (isset($data['gallery'])) {
            $data['gallery'] = $this->cleanGalleryValue($data['gallery']);
        }

        $product = Product::create($data + [
            'vendor_id' => auth()->id(),
            'active' => false,
        ]);
        // tags
        $product->tags()->sync($request->input('tag_ids', []));
        if ($product->type === 'variable') {
            $this->syncVariations($product, $request);
        }

        // notify admins
        try {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new ProductPendingForReview($product));
                try {
                    $admin->notify(new \App\Notifications\AdminProductPendingReviewNotification($product));
                } catch (\Throwable $e) {
                    logger()->warning('Failed to send product notification: '.$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to send product review emails: '.$e->getMessage());
        }

        return redirect()->route('vendor.products.index')->with('success', __('Product submitted for review.'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = \Illuminate\Support\Facades\Cache::remember('product_categories_ordered', 3600, function () {
            return ProductCategory::orderBy('name')->get();
        });
        $tags = \Illuminate\Support\Facades\Cache::remember('product_tags_ordered', 3600, function () {
            return ProductTag::orderBy('name')->get();
        });
        $attributes = \Illuminate\Support\Facades\Cache::remember('product_attributes_with_values', 3600, function () {
            return ProductAttribute::with('values')->orderBy('name')->get();
        });

        return view('vendor.products.edit', compact('product', 'categories', 'tags', 'attributes'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $data = $request->validated();

        // Merge translations (arrays) into *_translations JSON columns, then collapse to base
        $this->mergeVendorTranslations($request, $data, $product);
        $data = $this->collapsePrimaryTextFields($data, $product);

        // update slug if name changed
        $defaultName = $data['name'] ?? $product->name;
        $slug = Str::slug(
            is_array($defaultName)
                ? (array_values(array_filter($defaultName))[0] ?? '')
                : $defaultName
        );
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            $slug = $base.'-'.$i++;
        }
        $data['slug'] = $slug;
        if (isset($data['gallery'])) {
            $data['gallery'] = $this->cleanGalleryValue($data['gallery']);
        }

        $product->fill($data);
        $product->active = false;
        $product->save();
        $product->tags()->sync($request->input('tag_ids', []));
        if ($product->type === 'variable') {
            $this->syncVariations($product, $request);
        }

        try {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new ProductPendingForReview($product));
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to send product update emails: '.$e->getMessage());
        }

        return redirect()->route('vendor.products.index')
            ->with('success', __('Product updated and resubmitted for review.'));
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return back()->with('success', __('Product deleted.'));
    }

    // validateVendorData removed: validation moved to FormRequest Vendor\ProductRequest

    /**
     * Merge vendor submitted multilingual inputs into translation JSON arrays.
     * Similar to admin merge but simplified: we trust active languages table.
     */
    public function mergeVendorTranslations(Request $r, array &$data, ?Product $existing = null): void
    {
        try {
            $languages = \App\Models\Language::where('is_active', 1)->orderByDesc('is_default')->get();
            if ($languages->isEmpty()) {
                return;
            }
            $defaultLang = $languages->firstWhere('is_default', 1) ?? $languages->first();
            $defaultCode = $defaultLang->code;
            foreach (
                ['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords'] as $field
            ) {
                if ($r->has($field) && is_array($r->input($field))) {
                    $incoming = $r->input($field);
                    // pick default value
                    $defaultVal = $incoming[$defaultCode] ??
                        collect($incoming)->first(
                            fn ($v) => trim((string) $v) !== ''
                        );
                    if ($defaultVal === null) {
                        $defaultVal = $existing?->$field; // fallback
                    }
                    // normalize blanks -> default
                    foreach ($languages as $lang) {
                        $code = $lang->code;
                        if (! isset($incoming[$code]) || trim((string) $incoming[$code]) === '') {
                            $incoming[$code] = $defaultVal;
                        }
                    }
                    $data[$field.'_translations'] = $incoming;
                    // base column will be set later by collapsePrimaryTextFields
                }
            }
            // slug translations auto from name_translations if present
            if (! empty($data['name_translations'])) {
                $slugTranslations = [];
                foreach ($data['name_translations'] as $lc => $nm) {
                    $slugTranslations[$lc] = Str::slug($nm ?? '');
                }
                $data['slug_translations'] = $slugTranslations;
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed to merge vendor translations: '.$e->getMessage());
        }
    }

    /**
     * Collapse array-based multilingual fields into single base string values.
     * Picks first non-empty value. Leaves original array in *_translations columns if added later.
     */
    public function collapsePrimaryTextFields(array $data, ?Product $existing = null): array
    {
        foreach (
            ['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords'] as $field
        ) {
            if (isset($data[$field]) && is_array($data[$field])) {
                // choose first non-empty value
                $first = null;
                foreach ($data[$field] as $val) {
                    if (is_string($val) && trim($val) !== '') {
                        $first = $val;
                        break;
                    }
                }
                if ($first === null) { // fallback to existing or empty string
                    $first = (string) ($existing?->$field ?? '');
                }
                $data[$field] = $first;
            }
        }

        return $data;
    }

    public function cleanGalleryValue($raw)
    {
        $arr = [];
        if (is_array($raw)) {
            $arr = $raw;
        } elseif (is_string($raw) && trim($raw) !== '') {
            $candidate = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($candidate)) {
                $arr = $candidate;
            }
        }
        return array_values(
            array_filter(
                array_map(fn ($v) => is_string($v) ? trim($v) : '', $arr),
                fn ($v) => $v !== ''
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
            $id = $v['id'] ?? null;
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
            // Variation name translations (optional)
            if (! empty($v['name']) && is_array($v['name'])) {
                try {
                    $languages = \App\Models\Language::where('is_active', 1)->orderByDesc('is_default')->get();
                    if ($languages->count()) {
                        $default = optional($languages->firstWhere('is_default', 1))->code ?? $languages->first()->code;
                        $translations = $v['name'];
                        $defaultVal = $translations[$default] ??
                            collect($translations)->first(
                                fn ($val) => trim((string) $val) !== ''
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
                    logger()->warning('Failed to process variation translations: '.$e->getMessage());
                }
            }
            if ($id) {
                $variation = \App\Models\ProductVariation::where('product_id', $product->id)
                    ->where('id', $id)
                    ->first();
                if ($variation) {
                    $variation->update($data);
                    $ids[] = $variation->id;
                }
            } else {
                $ids[] = $product->variations()->create($data)->id;
            }
        }
        $product->variations()->whereNotIn('id', $ids)->delete();
    }

    protected function syncSerials(Product $product, array $serials): void
    {
        foreach ($serials as $s) {
            $s = trim($s);
            if ($s === '') {
                continue;
            }
            $exists = \App\Models\ProductSerial::where('product_id', $product->id)
                ->where('serial', $s)
                ->first();
            if (! $exists) {
                \App\Models\ProductSerial::create(['product_id' => $product->id, 'serial' => $s]);
            }
        }
    }
}
