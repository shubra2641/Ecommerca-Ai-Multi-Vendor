<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariation;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        // validate query params to avoid unexpected input reaching queries
        $r->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:product_categories,id'],
            'type' => ['nullable', 'string', 'max:50'],
            'flag' => ['nullable', 'in:featured,best,inactive'],
            'stock' => ['nullable', 'in:na,low,soon,in'],
        ]);

        // eager-load variations to avoid N+1 queries when listing products
        $q = Product::with('category', 'variations');
        if ($search = $r->input('q')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', '%' . $search . '%')->orWhere('sku', 'like', '%' . $search . '%');
            });
        }
        if ($r->filled('category')) {
            $q->where('product_category_id', $r->input('category'));
        }
        if ($type = $r->input('type')) {
            $q->where('type', $type);
        }
        if ($flag = $r->input('flag')) {
            if ($flag === 'featured') {
                $q->where('is_featured', 1);
            } elseif ($flag === 'best') {
                $q->where('is_best_seller', 1);
            } elseif ($flag === 'inactive') {
                $q->where('active', 0);
            }
        }
        if ($stock = $r->input('stock')) {
            $low = config('catalog.stock_low_threshold');
            $soon = config('catalog.stock_soon_threshold');
            if ($stock === 'na') {
                $q->where(function ($qq) {
                    $qq->where('manage_stock', 0)->orWhereNull('manage_stock');
                });
            } else {
                $q->where('manage_stock', 1);
                if ($stock === 'low') {
                    $q->whereRaw('(stock_qty - COALESCE(reserved_qty,0)) <= ?', [$low]);
                } elseif ($stock === 'soon') {
                    $q->whereRaw('(stock_qty - COALESCE(reserved_qty,0)) > ? AND (stock_qty - COALESCE(reserved_qty,0)) <= ?', [$low, $soon]);
                } elseif ($stock === 'in') {
                    $q->whereRaw('(stock_qty - COALESCE(reserved_qty,0)) > ?', [$soon]);
                }
            }
        }
        $products = $q->latest()->paginate(40)->withQueryString();

        return view('admin.products.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load('tags', 'variations', 'category');

        return view('admin.products.products.show', compact('product'));
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

        return view('admin.products.products.create', compact('categories', 'tags', 'attributes'));
    }

    public function store(\App\Http\Requests\Admin\ProductRequest $r, HtmlSanitizer $sanitizer)
    {
        $data = $r->validated();
        // normalize translations first (will set $data['name'] to default language string)
        $this->mergeAndNormalizeTranslations($r, $data);
        $data = $this->collapsePrimaryTextFields($data);
        // generate base slug from default name after merge
        $defaultName = $data['name'] ?? '';
        if (is_array($defaultName)) { // safety guard
            $defaultName = array_values(array_filter($defaultName))[0] ?? '';
        }
        $slug = Str::slug($defaultName);
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $data['slug'] = $slug;
        // sanitize gallery if present
        if (isset($data['gallery'])) {
            $data['gallery'] = $this->cleanGalleryValue($data['gallery']);
        }
        // sanitize text/html translation fields
        foreach (['short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords'] as $f) {
            if (isset($data[$f]) && is_array($data[$f])) {
                $trans = array_filter($data[$f]);
                foreach ($trans as $lc => $val) {
                    $trans[$lc] = $sanitizer->clean($val);
                }
                $data[$f . '_translations'] = $trans;
                $data[$f] = $data[$f . '_translations'][config('app.fallback_locale')] ?? collect($data[$f . '_translations'])->first(fn ($v) => ! empty($v));
            }
        }
        // persist used_attributes if present
        if (isset($data['used_attributes']) && is_array($data['used_attributes'])) {
            // ensure unique values
            $data['used_attributes'] = array_values(array_unique(array_filter($data['used_attributes'])));
        }
        $product = Product::create($data);
        $product->tags()->sync($r->input('tag_ids', []));
        if ($r->filled('__serials_to_sync') || isset($data['__serials_to_sync'])) {
            $this->syncSerials($product, $data['__serials_to_sync'] ?? $r->input('__serials_to_sync'));
        }
        if ($product->type === 'variable') {
            $this->syncVariations($product, $r);
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created');
    }

    public function edit(Product $product)
    {
        $product->load('tags', 'variations');
        $categories = \Illuminate\Support\Facades\Cache::remember('product_categories_ordered', 3600, function () {
            return ProductCategory::orderBy('name')->get();
        });
        $tags = \Illuminate\Support\Facades\Cache::remember('product_tags_ordered', 3600, function () {
            return ProductTag::orderBy('name')->get();
        });
        $attributes = \Illuminate\Support\Facades\Cache::remember('product_attributes_with_values', 3600, function () {
            return ProductAttribute::with('values')->orderBy('name')->get();
        });

        return view('admin.products.products.edit', compact('product', 'categories', 'tags', 'attributes'));
    }

    public function update(\App\Http\Requests\Admin\ProductRequest $r, Product $product, HtmlSanitizer $sanitizer)
    {
        $data = $r->validated();
        // normalize translations first
        $this->mergeAndNormalizeTranslations($r, $data);
        $data = $this->collapsePrimaryTextFields($data, $product);
        $defaultName = $data['name'] ?? '';
        if (is_array($defaultName)) {
            $defaultName = array_values(array_filter($defaultName))[0] ?? '';
        }
        $slug = Str::slug($defaultName);
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $data['slug'] = $slug;
        if (isset($data['gallery'])) {
            $data['gallery'] = $this->cleanGalleryValue($data['gallery']);
        }
        // sanitize text/html translation fields
        foreach (['short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords'] as $f) {
            if (isset($data[$f]) && is_array($data[$f])) {
                $trans = array_filter($data[$f]);
                foreach ($trans as $lc => $val) {
                    $trans[$lc] = $sanitizer->clean($val);
                }
                $data[$f . '_translations'] = $trans;
                $data[$f] = $data[$f . '_translations'][config('app.fallback_locale')] ?? collect($data[$f . '_translations'])->first(fn ($v) => ! empty($v));
            }
        }
        if (isset($data['used_attributes']) && is_array($data['used_attributes'])) {
            $data['used_attributes'] = array_values(array_unique(array_filter($data['used_attributes'])));
        }
        $oldActive = $product->active;
        $product->update($data);
        $product->tags()->sync($r->input('tag_ids', []));
        if ($r->filled('__serials_to_sync') || isset($data['__serials_to_sync'])) {
            $this->syncSerials($product, $data['__serials_to_sync'] ?? $r->input('__serials_to_sync'));
        }
        if ($product->type === 'variable') {
            $this->syncVariations($product, $r);
        }

        // Notify admins if stock is low or out
        try {
            if ($product->manage_stock) {
                $available = (int) $product->stock_qty - (int) ($product->reserved_qty ?? 0);
                $low = (int) config('catalog.stock_low_threshold', 5);
                if ($available <= $low) {
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    if ($admins && $admins->count()) {
                        \Illuminate\Support\Facades\Notification::sendNow($admins, new \App\Notifications\AdminStockLowNotification($product, $available));
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed sending stock low notification: ' . $e->getMessage());
        }

        // If active flag changed, notify vendor
        try {
            if ($oldActive !== $product->active && $product->vendor) {
                if ($product->active) {
                    \Illuminate\Support\Facades\Mail::to($product->vendor->email)->queue(new \App\Mail\ProductApproved($product));
                } else {
                    // For now we send a generic rejection without reason; admin UI can send reason via separate flow
                    \Illuminate\Support\Facades\Mail::to($product->vendor->email)->queue(new \App\Mail\ProductRejected($product, null));
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed sending product status mail: ' . $e->getMessage());
        }

        return back()->with('success', 'Updated');
    }

    /**
     * Merge incoming translation arrays, set base field to default language value,
     * and for every active language ensure each translation key exists; if missing/blank use default.
     */
    protected function mergeAndNormalizeTranslations(Request $r, array &$data): void
    {
        // If translation JSON columns not present yet, skip silently
        $needed = ['name_translations', 'slug_translations', 'short_description_translations', 'description_translations'];
        $schema = \Illuminate\Support\Facades\Schema::getColumnListing('products');
        foreach ($needed as $col) {
            if (! in_array($col, $schema)) {
                return;
            }
        }
        $languages = Language::where('is_active', 1)->orderByDesc('is_default')->get();
        if ($languages->isEmpty()) {
            return; // nothing to normalize
        }
        $defaultCode = optional($languages->firstWhere('is_default', 1))->code ?? $languages->first()->code;
        foreach (['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords'] as $f) { // omit direct slug input
            if ($r->has($f) && is_array($r->input($f))) {
                $translations = $r->input($f);
                // ensure default exists
                $defaultVal = $translations[$defaultCode] ?? (array_values($translations)[0] ?? null);
                if ($defaultVal !== null && $defaultVal !== '') {
                    $data[$f] = $defaultVal; // store base column
                }
                // fill missing languages with default
                foreach ($languages as $lang) {
                    $code = $lang->code;
                    if (! isset($translations[$code]) || $translations[$code] === '') {
                        $translations[$code] = $defaultVal;
                    }
                }
                $data[$f . '_translations'] = $translations;
            }
        }
        // auto build slug translations from name_translations
        if (! empty($data['name_translations']) && in_array('slug_translations', $schema)) {
            $slugTranslations = [];
            foreach ($data['name_translations'] as $lc => $nm) {
                $slugTranslations[$lc] = Str::slug($nm ?? '');
            }
            $data['slug_translations'] = $slugTranslations;
        }
    }

    protected function syncSerials(Product $product, array $serials)
    {
        // create missing serials, keep existing sold status; avoid duplicates
        foreach ($serials as $s) {
            $s = trim($s);
            if ($s === '') {
                continue;
            }
            $exists = \App\Models\ProductSerial::where('product_id', $product->id)->where('serial', $s)->first();
            if (! $exists) {
                \App\Models\ProductSerial::create(['product_id' => $product->id, 'serial' => $s]);
            }
        }
    }

    protected function validateData(Request $r, $id = null)
    {
        // determine product type (incoming or existing)
        $type = $r->input('type');
        if (! $type && $id) {
            $p = Product::find($id);
            $type = $p?->type;
        }
        $rules = [
            'product_category_id' => 'required|exists:product_categories,id',
            'type' => 'required|in:simple,variable',
            'physical_type' => 'nullable|in:physical,digital',
            'sku' => 'nullable|unique:products,sku' . ($id ? ',' . $id : ''),
            'name' => 'required',
            'name_translations' => 'nullable|array',
            'slug_translations' => 'nullable|array',
            'short_description' => 'nullable',
            'description' => 'nullable',
            'price' => 'required_if:type,simple|numeric',
            'sale_price' => 'nullable|numeric',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after_or_equal:sale_start',
            'main_image' => 'nullable|string',
            'gallery' => 'nullable',
            'manage_stock' => 'boolean',
            'stock_qty' => 'nullable|integer',
            'reserved_qty' => 'nullable|integer',
            'backorder' => 'boolean',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'seo_title' => 'nullable',
            'seo_description' => 'nullable',
            'seo_keywords' => 'nullable',
            'active' => 'boolean',
            'used_attributes' => 'nullable|array',
            // logistics fields
            'refund_days' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ];
        // add basic variations validation when product is variable
        if ($type === 'variable') {
            $rules = array_merge($rules, [
                'variations' => 'sometimes|array',
                'variations.*.price' => 'required_if:type,variable|numeric',
                'variations.*.sale_price' => 'nullable|numeric',
                'variations.*.sale_start' => 'nullable|date',
                'variations.*.sale_end' => 'nullable|date',
                'variations.*.stock_qty' => 'nullable|integer|min:0',
                'variations.*.reserved_qty' => 'nullable|integer|min:0',
                'variations.*.sku' => 'nullable|string',
                'variations.*.manage_stock' => 'nullable|boolean',
                'variations.*.backorder' => 'nullable|boolean',
            ]);
        }
        $validated = $r->validate($rules, ['sale_end.after_or_equal' => 'End must be >= start']);

        // Digital product-specific validations
        $isDigital = ($r->input('physical_type') === 'digital') || ($r->input('type') === 'digital');
        if ($isDigital) {
            // require either download_file (path) or download_url
            if (! $r->filled('download_url') && ! $r->filled('download_file')) {
                throw \Illuminate\Validation\ValidationException::withMessages(['download' => ['Provide either a download file path or a download URL']]);
            }
            // if download_file provided, ensure extension is zip or pdf
            if ($r->filled('download_file')) {
                $ext = strtolower(pathinfo($r->input('download_file'), PATHINFO_EXTENSION));
                if (! in_array($ext, ['zip', 'pdf'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['download_file' => ['Download file must be a ZIP or PDF']]);
                }
            }
            // handle serials input: accept textarea with newline-separated serials or array 'serials[]'
            $serialsRaw = $r->input('serials');
            $serialList = [];
            if (is_string($serialsRaw) && trim($serialsRaw) !== '') {
                $lines = preg_split('/\r?\n/', $serialsRaw);
                foreach ($lines as $ln) {
                    $s = trim($ln);
                    if ($s !== '') {
                        $serialList[] = $s;
                    }
                }
            } elseif (is_array($serialsRaw)) {
                foreach ($serialsRaw as $s) {
                    $s = trim($s);
                    if ($s !== '') {
                        $serialList[] = $s;
                    }
                }
            }
            if (count($serialList) > 0) {
                $validated['has_serials'] = true;
                // basic validation: no duplicates in provided list
                $dups = array_diff_assoc($serialList, array_unique($serialList));
                if (! empty($dups)) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['serials' => ['Duplicate serials provided in input']]);
                }
                // attach serialList to validated for use by store/update
                $validated['__serials_to_sync'] = $serialList;
            } else {
                $validated['has_serials'] = false;
            }
        }

        // Additional server-side checks for variations: attributes must be array or valid JSON and combinations unique
        if (($type === 'variable') && $r->has('variations')) {
            $seen = [];
            $errors = [];
            // load attribute map once (keyed by slug) to validate attribute keys/values
            $attributesMap = \App\Models\ProductAttribute::with('values')->get()->keyBy('slug');
            foreach ($r->input('variations', []) as $idx => $v) {
                $attrRaw = $v['attributes'] ?? [];
                if (is_string($attrRaw)) {
                    $decoded = json_decode($attrRaw, true);
                    $attrRaw = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                }
                if (! is_array($attrRaw)) {
                    $errors["variations.{$idx}.attributes"] = ['Must be a JSON array or PHP array'];

                    continue;
                }
                // validate attribute slugs and values against DB lists
                $localAttrError = false;
                foreach ($attrRaw as $attrSlug => $attrVal) {
                    if (! isset($attributesMap[$attrSlug])) {
                        $errors["variations.{$idx}.attributes.{$attrSlug}"] = ["Unknown attribute '{$attrSlug}'"];
                        $localAttrError = true;

                        continue;
                    }
                    if ($attrVal !== null && $attrVal !== '') {
                        $validValues = $attributesMap[$attrSlug]->values->pluck('slug')->all();
                        if (! in_array($attrVal, $validValues)) {
                            $errors["variations.{$idx}.attributes.{$attrSlug}"] = ["Invalid attribute value '{$attrVal}' for '{$attrSlug}'"];
                            $localAttrError = true;
                        }
                    }
                }
                if ($localAttrError) {
                    // skip duplicate-detection for this row since its attributes are invalid
                    continue;
                }
                // If no attributes were selected (all values empty/null), skip duplicate detection
                $hasAnyAttrValue = false;
                foreach ($attrRaw as $av) {
                    if ($av !== null && $av !== '') {
                        $hasAnyAttrValue = true;
                        break;
                    }
                }
                if (! $hasAnyAttrValue) {
                    // nothing to validate for uniqueness
                    $seen[json_encode($attrRaw)] = $idx; // still record to avoid later collisions if needed

                    continue;
                }
                // Build a normalized key for attribute combination (sort by key)
                try {
                    ksort($attrRaw);
                } catch (\Throwable $e) {
                }
                $key = json_encode($attrRaw);
                if (isset($seen[$key])) {
                    $first = $seen[$key];
                    $errors['variations'][] = "Duplicate attribute combination at rows {$first} and {$idx}";
                }
                $seen[$key] = $idx;
            }
            if (! empty($errors)) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }
        }

        return $validated;
    }

    protected function syncVariations(Product $product, Request $r)
    {
        $payload = $r->input('variations', []);
        $ids = [];
        foreach ($payload as $v) {
            // require price
            if (! isset($v['price']) || $v['price'] === '') {
                continue;
            }
            $id = $v['id'] ?? null;
            // normalize attribute data (could come as JSON string)
            $attrRaw = $v['attributes'] ?? [];
            if (is_string($attrRaw)) {
                $decoded = json_decode($attrRaw, true);
                $attrRaw = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
            }
            // validate sale dates: if both present ensure end >= start
            if (! empty($v['sale_start']) && ! empty($v['sale_end'])) {
                try {
                    if (strtotime($v['sale_end']) < strtotime($v['sale_start'])) {
                        continue;
                    }
                } catch (\Throwable $e) {
                    continue;
                }
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
                try {
                    $languages = \App\Models\Language::where('is_active', 1)->orderByDesc('is_default')->get();
                    if ($languages->count()) {
                        $default = optional($languages->firstWhere('is_default', 1))->code ?? $languages->first()->code;
                        $translations = $v['name'];
                        $defaultVal = $translations[$default] ?? collect($translations)->first(fn ($val) => trim((string) $val) !== '');
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
                }
            }
            if ($id) {
                $variation = ProductVariation::where('product_id', $product->id)->where('id', $id)->first();
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

    public function export(Request $r)
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
            \App\Models\Product::with('tags')->chunk(200, function ($items) use ($out) {
                foreach ($items as $p) {
                    fputcsv($out, [
                        $p->id,
                        $p->name,
                        $p->sku,
                        $p->type,
                        $p->price,
                        $p->sale_price,
                        $p->active ? 1 : 0,
                        $p->created_at,
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export variation-level inventory as CSV
     */
    public function variationsExport(Request $r)
    {
        $fileName = 'variations_inventory_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];
        $columns = ['product_id', 'product_name', 'variation_id', 'sku', 'manage_stock', 'stock_qty', 'reserved_qty', 'available_stock'];
        $callback = function () use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            \App\Models\ProductVariation::with('product')->chunk(200, function ($items) use ($out) {
                foreach ($items as $v) {
                    $available = ($v->stock_qty ?? 0) - ($v->reserved_qty ?? 0);
                    fputcsv($out, [
                        $v->product_id,
                        $v->product?->name,
                        $v->id,
                        $v->sku,
                        $v->manage_stock ? 1 : 0,
                        $v->stock_qty ?? 0,
                        $v->reserved_qty ?? 0,
                        $available,
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Deleted');
    }

    protected function cleanGalleryValue($raw)
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
        $arr = array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : '', $arr), fn ($v) => $v !== ''));

        return $arr; // let Eloquent cast (array) -> json
    }

    protected function collapsePrimaryTextFields(array $data, ?Product $existing = null): array
    {
        foreach (['name', 'short_description', 'description', 'seo_title', 'seo_description', 'seo_keywords'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $first = null;
                foreach ($data[$field] as $val) {
                    if (is_string($val) && trim($val) !== '') {
                        $first = $val;
                        break;
                    }
                }
                if ($first === null) {
                    $first = (string) ($existing?->$field ?? '');
                }
                $data[$field] = $first;
            }
        }

        return $data;
    }

    /**
     * Generate AI description & SEO fields based on product name (and optional category) for form use.
     */
    public function aiSuggest(Request $request)
    {
        $this->authorize('access-admin');
        $request->validate([
            'name' => 'required|string|min:3',
            'locale' => 'nullable|string|max:10',
        ]);
        $userId = auth()->id() ?: 0;
        $setting = \App\Models\Setting::first();
        if (! ($setting?->ai_enabled) || ($setting?->ai_provider !== 'openai')) {
            return response()->json(['error' => 'AI disabled'], 422);
        }
        if (! $setting->ai_openai_api_key) {
            return response()->json(['error' => 'Missing API key'], 422);
        }
        $apiKey = $setting->ai_openai_api_key; // decrypted accessor
        $locale = $request->locale ?: app()->getLocale();
        // Response cache to avoid duplicate calls for same (name, locale)
        // Bump cache version (v2) after adding short_description field to avoid stale structures
        $cacheKey = 'ai_suggest_cache_v2:' . md5($request->name . '|' . $locale);
        if ($cached = Cache::get($cacheKey)) {
            return response()->json($cached + ['cached' => true, 'source' => 'cache']);
        }

        // Rate limit AFTER confirming not cached
        $perMinuteLimit = (int) env('AI_SUGGEST_RATE_PER_MIN', 10);
        $rateKey = 'ai_suggest_rate:' . $userId . ':' . now()->format('YmdHi');
        $count = Cache::increment($rateKey);
        if ($count === 1) {
            Cache::put($rateKey, 1, 65);
        }
        if ($count > $perMinuteLimit) {
            return response()->json([
                'error' => 'rate_limited_local',
                'source' => 'local',
                'message' => __('Too many AI requests. Please wait a minute and try again.'),
                'retry_after' => 60,
                'limit' => $perMinuteLimit,
            ], 429);
        }
        $prompt = 'Generate JSON with keys short_description (<=200 chars compelling summary), '
            . 'seo_description (<=160 chars), seo_keywords (<=12 comma keywords), '
            . 'description (2 paragraphs rich) based on product name: "' . $request->name . '" '
            . 'Language: ' . $locale . '. Return ONLY JSON.';
        $model = config('services.openai.model', 'gpt-4o-mini');
        $chatPayload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a product copy assistant. Output concise valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.6,
        ];
        try {
            $resp = Http::withToken($apiKey)->acceptJson()->timeout(25)->post('https://api.openai.com/v1/chat/completions', $chatPayload);
        } catch (\Throwable $e) {
            Log::warning('AI HTTP exception (chat): ' . $e->getMessage());

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
        // Continue to parsing
        if (! $rawText) {
            return response()->json([
                'error' => 'empty_output',
                'provider_status' => $providerStatus,
                'provider_body' => $providerBody,
            ], 502);
        }

        // Try parse JSON first
        $shortDescription = '';
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
            $shortDescription = (string) ($parsed['short_description'] ?? '');
            $seoDescription = (string) ($parsed['seo_description'] ?? '');
            $seoKeywords = (string) ($parsed['seo_keywords'] ?? '');
            $description = (string) ($parsed['description'] ?? '');
        } else {
            // fallback to heuristic parsing
            $lines = preg_split('/\n+/', trim($rawText));
            foreach ($lines as $l) {
                $ll = trim($l);
                if ($shortDescription === '' && mb_strlen($ll) <= 200) {
                    $shortDescription = $ll;

                    continue;
                }
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
        if ($shortDescription === '' && $description !== '') {
            // First sentence or first 200 chars from description
            $plain = preg_replace('/\s+/', ' ', trim($description));
            $firstSentence = preg_split('/(?<=[.!?])\s+/', $plain, 2)[0] ?? '';
            $shortDescription = mb_substr($firstSentence !== '' ? $firstSentence : $plain, 0, 200);
        }
        if ($seoDescription === '' && $description !== '') {
            $seoDescription = mb_substr(preg_replace('/\s+/', ' ', trim($description)), 0, 160);
        }
        $result = [
            'short_description' => mb_substr($shortDescription, 0, 200),
            'seo_description' => mb_substr($seoDescription, 0, 160),
            'seo_keywords' => $seoKeywords,
            'description' => trim($description),
            'provider_status' => $providerStatus,
            'source' => 'live',
        ];
        Cache::put($cacheKey, $result, 600); // cache 10 minutes

        return response()->json($result);
    }
}
