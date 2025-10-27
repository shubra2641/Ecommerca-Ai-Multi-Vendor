<?php

namespace App\Traits;

trait ProductVariationDataTrait
{
    private function getVariationsData($model): array
    {
        if (!$model) {
            return [];
        }

        try {
            return $model->variations->map(fn ($v) => $this->mapVariation($v))
                ->values()->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function mapVariation($v): array
    {
        return [
            'id' => $v->id,
            'active' => (bool) $v->active,
            'attributes' => $v->attribute_data ?? [],
            'image' => $v->image ?? null,
            'name' => $v->name,
            'name_translations' => $v->name_translations ?? [],
            'sku' => $v->sku,
            'price' => $v->price,
            'sale_price' => $v->sale_price,
            'sale_start' => $v->sale_start ?
                $v->sale_start->format('Y-m-d\\TH:i') : null,
            'sale_end' => $v->sale_end ?
                $v->sale_end->format('Y-m-d\\TH:i') : null,
            'manage_stock' => (bool) $v->manage_stock,
            'stock_qty' => $v->stock_qty,
            'reserved_qty' => $v->reserved_qty,
            'backorder' => (bool) $v->backorder,
        ];
    }

    private function mapAttribute($a): array
    {
        return [
            'id' => $a->id,
            'name' => $a->name,
            'slug' => $a->slug,
            'values' => $a->values->map(fn ($v) => $this->mapAttributeValue($v))
                ->values()->all(),
        ];
    }

    private function mapAttributeValue($v): array
    {
        return [
            'id' => $v->id,
            'value' => $v->value,
            'slug' => $v->slug,
        ];
    }
}
