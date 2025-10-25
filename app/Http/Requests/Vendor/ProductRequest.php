<?php

declare(strict_types=1);

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $productId = $this->getProductId();
        $productType = $this->input('type', 'simple');

        return array_merge(
            $this->getBasicRules($productId),
            $this->getTypeSpecificRules($productType),
            $this->getOptionalRules()
        );
    }

    private function getProductId(): ?int
    {
        return $this->route('product')?->id;
    }

    private function getBasicRules(?int $productId): array
    {
        return [
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'type' => ['required', Rule::in(['simple', 'variable'])],
            'physical_type' => ['nullable', Rule::in(['physical', 'digital'])],
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId)
            ],
            'name' => ['required'],
            'price' => ['required_if:type,simple', 'numeric'],
        ];
    }

    private function getTypeSpecificRules(string $type): array
    {
        if ($type === 'variable') {
            return ['variations' => ['sometimes', 'array']];
        }

        return [];
    }

    private function getOptionalRules(): array
    {
        return [
            'short_description' => ['nullable'],
            'description' => ['nullable'],
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
    }

    public function messages(): array
    {
        return [
            'sale_end.after_or_equal' => __('End date must be after or equal to start date'),
        ];
    }
}
