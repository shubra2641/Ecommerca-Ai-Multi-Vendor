<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Language;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Traits\ProductVariationDataTrait;
use Illuminate\View\View;

final class AdminProductFormComposer
{
    use ProductVariationDataTrait;
    public function compose(View $view): void
    {
        $data = $view->getData();
        $model = $data['model'] ?? ($data['m'] ?? null);

        $languages = $this->getLanguages();
        $categories = $this->getCategories();
        $pfAttrData = $this->getAttributeData();
        $pfUsedAttributes = $this->getUsedAttributes($model);
        $pfClientVariations = $this->getVariationsData($model);
        $pfHasSerials = $this->getHasSerials($model);
        $pfLangMeta = $this->getLanguageMeta($languages, $model);
        $pfExistingSerials = $this->getExistingSerials($model);

        $formSettings = $this->getFormSettings($data, $languages);

        $view->with(array_merge(compact(
            'languages',
            'categories',
            'pfAttrData',
            'pfUsedAttributes',
            'pfClientVariations',
            'pfHasSerials',
            'pfLangMeta',
            'pfExistingSerials'
        ), $formSettings));
    }

    private function getUsedAttributes($model): array
    {
        if ($model && is_array($model->used_attributes ?? null)) {
            return $model->used_attributes;
        }

        return is_array(old('used_attributes')) ? old('used_attributes') : [];
    }

    private function getHasSerials($model): bool
    {
        return (bool) ($model->has_serials ?? old('has_serials', false));
    }

    private function getExistingSerials($model): string
    {
        if (! $model || ! $model->has_serials) {
            return (string) old('serials', '');
        }

        // Get unsold serials only
        try {
            $unsoldSerials = $model->serials()->whereNull('sold_at')->pluck('serial')->toArray();
            $serials = is_array($unsoldSerials) ? $unsoldSerials : [];
            return implode("\n", $serials);
        } catch (\Exception $e) {
            return '';
        }
    }

    private function getFormSettings(array $data, $languages): array
    {
        $isVendorForm = (bool) ($data['isVendorForm'] ?? false);
        $currentLocale = app()->getLocale();
        $defaultLocale = $languages->firstWhere('is_default', 1)?->code ?? ($languages->first()?->code);

        return [
            'm' => $data['model'] ?? ($data['m'] ?? null),
            'pfShowActive' => ! $isVendorForm,
            'isVendorForm' => $isVendorForm,
            'currentLocale' => $currentLocale,
            'defaultLocale' => $defaultLocale,
        ];
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
            ->map(fn ($a) => $this->mapAttribute($a))
            ->values()
            ->all();
    }

    private function getLanguageMeta($languages, $model): array
    {
        return $languages->mapWithKeys(function ($lang) use ($model) {
            $code = $lang->code;
            return [
                $code => [
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
                ],
            ];
        })->all();
    }
}
