<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            // main translated fields (single-value for current/default language)
            'name' => method_exists($this, 'translated')
                ? $this->translated('name')
                : $this->name,
            'short_description' => method_exists($this, 'translated')
                ? $this->translated('short_description')
                : $this->short_description,
            'description' => method_exists($this, 'translated')
                ? $this->translated('description')
                : $this->description,
            'slug' => $this->slug,
            'product_category_id' => $this->product_category_id,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'sale_start' => $this->sale_start?->toIso8601String(),
            'sale_end' => $this->sale_end?->toIso8601String(),
            'effective_price' => method_exists($this, 'effectivePrice') ? $this->effectivePrice() : $this->price,
            'main_image' => $this->main_image ? storage_image_url($this->main_image) : null,
            'gallery' => $this->gallery ? array_map(fn ($img) => storage_image_url($img), $this->gallery) : [],
            'manage_stock' => (bool) $this->manage_stock,
            'stock_qty' => $this->stock_qty,
            'reserved_qty' => $this->reserved_qty,
            'available_stock' => method_exists($this, 'availableStock') ? $this->availableStock() : $this->stock_qty,
            'backorder' => (bool) $this->backorder,
            // feature flags (duplicate keys for mobile client expectations)
            'is_featured' => (bool) $this->is_featured,
            'is_best_seller' => (bool) $this->is_best_seller,
            'featured' => (bool) $this->is_featured,
            'best_seller' => (bool) $this->is_best_seller,
            'physical_type' => $this->physical_type,
            'physical' => $this->physical_type !== 'digital',
            'has_serials' => (bool) $this->has_serials,
            'download_url' => $this->download_url,
            'download_file' => $this->download_file,
            'refund_days' => $this->refund_days,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            // product category with translations and seo fields
            'product_category' => $this->whenLoaded('category', function () {
                $c = $this->category;

                return [
                    'id' => $c?->id,
                    'name' => method_exists($c, 'translated') ? $c->translated('name') : $c?->name,
                    'slug' => $c?->slug,
                    'name_translations' => $c?->name_translations ?? [],
                    'description_translations' => $c?->description_translations ?? [],
                    'seo_title_translations' => $c?->seo_title_translations ?? [],
                    'seo_description_translations' => $c?->seo_description_translations ?? [],
                    'seo_keywords_translations' => $c?->seo_keywords_translations ?? [],
                ];
            }),
            'variations_count' => isset($this->variations_count) ? (int) $this->variations_count : null,
            // include variations when relation is loaded (detailed view)
            'variations' => $this->whenLoaded('variations', function () {
                return $this->variations->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'active' => (bool) $v->active,
                        'attributes' => $v->attribute_data ?? [],
                        'image' => $v->image ? storage_image_url($v->image) : null,
                        'name' => $v->name,
                        'name_translations' => $v->name_translations ?? [],
                        'sku' => $v->sku,
                        'price' => $v->price,
                        'effective_price' => method_exists($v, 'effectivePrice')
                            ? $v->effectivePrice()
                            : ($v->sale_price ?? $v->price),
                        'sale_price' => $v->sale_price,
                        'sale_start' => $v->sale_start ? $v->sale_start->toIso8601String() : null,
                        'sale_end' => $v->sale_end ? $v->sale_end->toIso8601String() : null,
                        'manage_stock' => (bool) $v->manage_stock,
                        'stock_qty' => $v->stock_qty,
                        'reserved_qty' => $v->reserved_qty,
                        'available_stock' => method_exists($v, 'availableStock') ? $v->availableStock() : $v->stock_qty,
                        'backorder' => (bool) $v->backorder,
                    ];
                })->values()->all();
            }),
            'used_attributes' => $this->used_attributes,
            'rejection_reason' => $this->rejection_reason ?? null,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'status' => $this->active ? 'approved' : ($this->rejection_reason ? 'rejected' : 'pending'),
            'vendor_id' => $this->vendor_id,
            'active' => (bool) $this->active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            // flat seo fields for mobile form expectations
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            // tag ids when loaded
            'tag_ids' => $this->whenLoaded('tags', fn () => $this->tags->pluck('id')->values()),
            // include full translations maps (for client to render multilingual fields)
            'name_translations' => $this->name_translations ?? [],
            'short_description_translations' => $this->short_description_translations ?? [],
            'description_translations' => $this->description_translations ?? [],
            'seo_title_translations' => $this->seo_title_translations ?? [],
            'seo_description_translations' => $this->seo_description_translations ?? [],
            'seo_keywords_translations' => $this->seo_keywords_translations ?? [],
            // grouped structure expected by mobile (convenience wrapper)
            'translations' => [
                'name' => $this->name_translations ?? [],
                'short_description' => $this->short_description_translations ?? [],
                'description' => $this->description_translations ?? [],
                'seo_title' => $this->seo_title_translations ?? [],
                'seo_description' => $this->seo_description_translations ?? [],
                'seo_keywords' => $this->seo_keywords_translations ?? [],
            ],
        ];
    }
}
