<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;

final class AdminProductVariationsDataComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $model = $data['model'] ?? ($data['m'] ?? null);
        $attributes = $data['attributes'] ?? collect();

        $json = $this->getJsonData($model, $attributes);
        $view->with('productVariationsJson', $json);
    }

    private function getExistingVariations($model): array
    {
        if (! $model) {
            return [];
        }

        try {
            return $model->variations->map(function ($v) {
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
            })->values()->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getJsonData($model, $attributes): string
    {
        $existing = $this->getExistingVariations($model);

        $attributeData = $attributes->map(function ($a) {
            try {
                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'slug' => $a->slug,
                    'values' => $a->values->map(function ($v) {
                        return [
                            'id' => $v->id,
                            'value' => $v->value,
                            'slug' => $v->slug,
                        ];
                    })->values()->all(),
                ];
            } catch (\Throwable $e) {
                return [];
            }
        })->filter()->values()->all();

        $languageData = Language::where('is_active', 1)
            ->orderByDesc('is_default')
            ->get()
            ->map(fn($l) => [
                'code' => $l->code,
                'name' => $l->name,
                'is_default' => $l->is_default,
            ])
            ->values()
            ->all();

        return json_encode([
            'existing' => $existing,
            'attributes' => $attributeData,
            'languages' => $languageData,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
