<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        // authorization should be handled by middleware / gates; allow here
        return true;
    }

    public function rules()
    {
        $id = $this->route('product')?->id ?? null;
        $type = $this->input('type') ? $this->input('type') : null;

        // Allow tests (which submit a simple string name) OR multilingual UI (array)
        $nameInput = $this->input('name');
        $rules = [
            'product_category_id' => 'required|exists:product_categories,id',
            'type' => 'required|in:simple,variable',
            'physical_type' => 'nullable|in:physical,digital',
            'sku' => 'nullable|unique:products,sku' . ($id ? ',' . $id : ''),
            // Accept either string or array
            'name' => is_array($nameInput) ? 'required|array' : 'required|string',
            'name.*' => 'nullable|string',
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
            'stock_qty' => 'nullable|integer|min:0',
            'reserved_qty' => 'nullable|integer|min:0',
            'backorder' => 'boolean',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'seo_title' => 'nullable',
            'seo_description' => 'nullable',
            'seo_keywords' => 'nullable',
            'active' => 'boolean',
            'used_attributes' => 'nullable|array',
            'refund_days' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ];

        if ($this->input('type') === 'variable' || $type === 'variable') {
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

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v): void {
            // Ensure default language name not empty
            try {
                $defaultLang = \App\Models\Language::where('is_active', 1)->where('is_default', 1)->first();
                if ($defaultLang) {
                    $code = $defaultLang->code;
                    $names = $this->input('name');
                    if (is_array($names)) {
                        $val = trim((string) ($names[$code] ?? ''));
                        if ($val === '') {
                            $v->errors()->add("name.{$code}", __('Name in default language is required'));
                        }
                    } else { // simple string case
                        $val = trim((string) $names);
                        if ($val === '') {
                            $v->errors()->add('name', __('Name is required'));
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore validation errors for attributes
                null;
            }

            // Stock validation for simple products
            if ($this->input('type') === 'simple' && $this->input('manage_stock')) {
                if (! $this->filled('stock_qty') || $this->input('stock_qty') < 0) {
                    $v->errors()->add('stock_qty', __('Stock quantity is required for simple products with stock management enabled'));
                }
            }

            // Digital product validations
            $isDigital = ($this->input('physical_type') === 'digital') || ($this->input('type') === 'digital');
            if ($isDigital) {
                if (! $this->filled('download_url') && ! $this->filled('download_file')) {
                    $v->errors()->add('download', 'Provide either a download file path or a download URL');
                }
                if ($this->filled('download_file')) {
                    $ext = strtolower(pathinfo($this->input('download_file'), PATHINFO_EXTENSION));
                    if (! in_array($ext, ['zip', 'pdf'])) {
                        $v->errors()->add('download_file', 'Download file must be a ZIP or PDF');
                    }
                }

                // Serial parsing/dup detection
                $serialsRaw = $this->input('serials');
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
                    $dups = array_diff_assoc($serialList, array_unique($serialList));
                    if (! empty($dups)) {
                        $v->errors()->add('serials', 'Duplicate serials provided in input');
                    } else {
                        // Attach processed serials back to the validator so controller can access
                        $this->merge(['__serials_to_sync' => $serialList, 'has_serials' => true]);
                    }
                } else {
                    $this->merge(['has_serials' => false]);
                }
            }

            // Variations uniqueness and attribute validation (basic sanity checks)
            if ($this->input('type') === 'variable' && $this->has('variations')) {
                $seen = [];
                $errors = [];
                $attributesMap = \App\Models\ProductAttribute::with('values')->get()->keyBy('slug');
                foreach ($this->input('variations', []) as $idx => $vrow) {
                    $attrRaw = $vrow['attributes'] ?? [];
                    if (is_string($attrRaw)) {
                        $decoded = json_decode($attrRaw, true);
                        $attrRaw = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                    }
                    if (! is_array($attrRaw)) {
                        $errors["variations.{$idx}.attributes"] = 'Must be a JSON array or PHP array';

                        continue;
                    }
                    $localAttrError = false;
                    $normalized = [];
                    foreach ($attrRaw as $attrSlug => $attrVal) {
                        if (! isset($attributesMap[$attrSlug])) {
                            $errors["variations.{$idx}.attributes.{$attrSlug}"] = "Unknown attribute '{$attrSlug}'";
                            $localAttrError = true;

                            continue;
                        }
                        $value = is_array($attrVal) ? ($attrVal['value'] ?? null) : $attrVal;
                        $value = is_string($value) ? trim($value) : $value;
                        if ($value === '' || $value === null) {
                            continue;
                        }
                        $normalized[$attrSlug] = $value;
                    }
                    if ($localAttrError) {
                        continue;
                    }
                    if (empty($normalized)) {
                        continue;
                    }
                    ksort($normalized);
                    // uniqueness check based on json-encoded attributes map
                    $key = json_encode($normalized);
                    if (isset($seen[$key])) {
                        $errors["variations.{$idx}"] = 'Duplicate variation attributes found';
                    }
                    $seen[$key] = true;
                }
                if (! empty($errors)) {
                    foreach ($errors as $k => $m) {
                        $v->errors()->add($k, $m);
                    }
                }
            }
        });
    }
}
