<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

class OrderViewBuilder
{
    public function build(Order $order): array
    {
        // Items summary
        $items = $order->items->map(function ($it) {
            return [
                'id' => $it->id,
                'name' => $it->name,
                'qty' => $it->qty,
                'price' => $it->price,
                'line_total' => $it->qty * $it->price,
                'description' => $it->description,
            ];
        });
        $subtotal = $items->sum('line_total');
        $shipping = $order->shipping_price;
        $total = $order->total ?? $subtotal + ($shipping ?? 0);

        // Attachments from payments
        $attachments = collect();
        try {
            foreach ($order->payments as $p) {
                foreach ($p->attachments as $a) {
                    $attachments->push([
                        'id' => $a->id,
                        'path' => $a->path,
                        'ext' => strtoupper(pathinfo($a->path, PATHINFO_EXTENSION)),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed to process payment attachments: '.$e->getMessage());
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'attachments' => $attachments,
        ];
    }
}
