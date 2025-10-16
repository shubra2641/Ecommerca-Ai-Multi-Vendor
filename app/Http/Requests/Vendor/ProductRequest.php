<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Vendor must be authenticated; additional policy checks remain in controller
        return auth()->check();
    }

    public function rules(): array
    {
        $id = null;
        $product = $this->route('product');
        if ($product && isset($product->id)) {
            $id = $product->id;
        }

        $type = $this->input('type') ?? 'simple';
        $rules = [
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'type' => ['required', Rule::in(['simple', 'variable'])],
            'physical_type' => ['nullable', Rule::in(['physical', 'digital'])],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'.($id ? ','.$id : '')],
            'name' => ['required'],
            'short_description' => ['nullable'],
            'description' => ['nullable'],
            'price' => ['required_if:type,simple', 'numeric'],
            'sale_price' => ['nullable', 'numeric'],
            'sale_start' => ['nullable', 'date'],
            'sale_end' => ['nullable', 'date', 'after_or_equal:sale_start'],
            'main_image' => ['nullable', 'string'],
            'gallery' => ['nullable'],
            'manage_stock' => ['boolean'],
            'stock_qty' => ['nullable', 'integer'],
            'reserved_qty' => ['nullable', 'integer'],
            'backorder' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_best_seller' => ['boolean'],
            'seo_title' => ['nullable'],
            'seo_description' => ['nullable'],
            'seo_keywords' => ['nullable'],
            'active' => ['boolean'],
            'used_attributes' => ['nullable', 'array'],
            'refund_days' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric'],
            'length' => ['nullable', 'numeric'],
            'width' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
        ];
        if ($type === 'variable') {
            $rules['variations'] = ['sometimes', 'array'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'sale_end.after_or_equal' => __('End must be >= start'),
        ];
    }
}
