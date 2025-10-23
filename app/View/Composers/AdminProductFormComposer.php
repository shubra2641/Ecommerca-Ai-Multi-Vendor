<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Language;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use Illuminate\View\View;

final class AdminProductFormComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $model = $data['model'] ?? ($data['m'] ?? null);

        $languages = $this->getLanguages();
        $categories = $this->getCategories();
        $pfAttrData = $this->getAttributeData();
        $pfUsedAttributes = $model && is_array($model->used_attributes ?? null) ? $model->used_attributes : (is_array(old('used_attributes')) ? old('used_attributes') : []);
        $pfClientVariations = $this->getClientVariations($model);
        $pfHasSerials = (bool) ($model->has_serials ?? old('has_serials', false));
        $pfLangMeta = $this->getLanguageMeta($languages, $model);

        $isVendorForm = (bool) ($data['isVendorForm'] ?? false);
        $pfShowActive = ! $isVendorForm;
        $currentLocale = app()->getLocale();
        $defaultLocale = $languages->firstWhere('is_default', 1)?->code ?? ($languages->first()?->code);
        $formSettings = [
            'm' => $model,
            'pfShowActive' => $pfShowActive,
            'isVendorForm' => $isVendorForm,
            'currentLocale' => $currentLocale,
            'defaultLocale' => $defaultLocale,
        ];

        $view->with(array_merge(compact(
            'languages',
            'categories',
            'pfAttrData',
            'pfUsedAttributes',
            'pfClientVariations',
            'pfHasSerials',
            'pfLangMeta'
        ), $formSettings));
    }

    private function getLanguages()
    {
        return Language::where('is_active', 1)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    private function getCategories()
    {
        return ProductCategory::where('active', 1)
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    private function getAttributeData()
    {
        return ProductAttribute::with('values')
            ->orderBy('name')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'slug' => $a->slug,
                    'values' => $a->values
                        ->map(fn($v) => [
                            'id' => $v->id,
                            'value' => $v->value,
                            'slug' => $v->slug,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }



    private function getClientVariations($model): array
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
                    'image' => $v->image,
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

    private function getLanguageMeta($languages, $model): array
    {
        return $languages->mapWithKeys(function ($lang) use ($model) {
            $code = $lang->code;
            $isDefault = (bool) $lang->is_default;
            return [$code => [
                'name_val' => old(
                    "name.{$code}",
                    $model?->translate('name', $code) ?? ''
                ),
                'short_val' => old(
                    "short_description.{$code}",
                    $model?->translate('short_description', $code) ?? ''
                ),
                'desc_val' => old(
                    "description.{$code}",
                    $model?->translate('description', $code) ?? ''
                ),
                'seo_title' => old(
                    "seo_title.{$code}",
                    $model?->translate('seo_title', $code) ?? ''
                ),
                'seo_keywords' => old(
                    "seo_keywords.{$code}",
                    $model?->translate('seo_keywords', $code) ?? ''
                ),
                'seo_description' => old(
                    "seo_description.{$code}",
                    $model?->translate('seo_description', $code) ?? ''
                ),
                'ph_name' => $isDefault ? __('Main name') :
                    __('Name') . ' (' . $code . ')',
                'ph_short' => $isDefault ? __('Short description') :
                    __('Short') . ' (' . $code . ')',
                'ph_desc' => $isDefault ? __('Full description') :
                    __('Description') . ' (' . $code . ')',
                'ph_seo_title' => __('SEO Title') . ' (' . $code . ')',
                'ph_seo_keywords' => __('SEO Keywords') . ' (' . $code . ')',
                'ph_seo_description' => __('SEO Description') . ' (' . $code . ')',
            ]];
        })->all();
    }
}
