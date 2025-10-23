<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

class AdminProductShowComposer
{
    public function compose(View $view): void
    {
        $product = $view->getData()['product'] ?? null;
        if ($product) {
            // Precompute header actions & gallery decoding
            $editUrl = route('admin.products.edit', $product->id);
            $subtitle = $product->name;
            $actionsHtml = '<a href="' . e($editUrl) . '" class="btn btn-primary">' . e(__('Edit')) . '</a>';
            $galleryRaw = $product->gallery;
            $gallery = is_array($galleryRaw) ? $galleryRaw : (json_decode($galleryRaw, true) ? json_decode($galleryRaw, true) : []);
            $view->with('psEditUrl', $editUrl)
                ->with('psSubtitle', $subtitle)
                ->with('psActionsHtml', $actionsHtml)
                ->with('psGallery', $gallery);
        }
    }
}
