<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Language;
use App\Traits\ProductVariationDataTrait;
use Illuminate\View\View;

final class AdminProductVariationsDataComposer
{
    use ProductVariationDataTrait;
    public function compose(View $view): void
    {
        $data = $view->getData();
        $model = $data['model'] ?? ($data['m'] ?? null);
        $attributes = $data['attributes'] ?? collect();

        $json = $this->getJsonData($model, $attributes);
        $view->with('productVariationsJson', $json);
    }

    private function getJsonData($model, $attributes): string
    {
        $existing = $this->getVariationsData($model);
        $attributeData = $this->getAttributeData($attributes);
        $languageData = $this->getLanguageData();

        return json_encode([
            'existing' => $existing,
            'attributes' => $attributeData,
            'languages' => $languageData,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function getAttributeData($attributes): array
    {
        return $attributes->map(fn($a) => $this->mapAttribute($a))
            ->filter()
            ->values()
            ->all();
    }

    private function getLanguageData(): array
    {
        return Language::where('is_active', 1)
            ->orderByDesc('is_default')
            ->get()
            ->map(fn($l) => [
                'code' => $l->code,
                'name' => $l->name,
                'is_default' => $l->is_default,
            ])
            ->values()
            ->all();
    }
}
