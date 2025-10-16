<?php

namespace App\Services;

class ReturnsViewBuilder
{
    public function build($paginator): array
    {
        // Expect paginator of OrderItem (or similar) models with product relation loaded.
        $items = collect();
        foreach ($paginator as $item) {
            $img = $item->product?->main_image;
            $userImages = (array) ($item->meta['user_images'] ?? []);
            $adminImages = (array) ($item->meta['admin_images'] ?? []);
            $history = (array) ($item->meta['history'] ?? []);
            $mergedImages = array_merge($userImages, $adminImages);
            $items->push([
                'id' => $item->id,
                'name' => $item->name,
                'order_id' => $item->order_id,
                'qty' => $item->qty,
                'price' => $item->price,
                'image' => $img,
                'purchased_at' => $item->purchased_at?->toDateString(),
                'return_expires' => $item->refund_expires_at?->toDateString(),
                'return_requested' => (bool) $item->return_requested,
                'return_status' => $item->return_status,
                'within_window' => method_exists($item, 'isWithinReturnWindow') ? $item->isWithinReturnWindow() : false,
                'images' => $mergedImages,
                'history' => $history,
            ]);
        }

        return [
            'items' => $items,
            'paginator' => $paginator,
        ];
    }
}
