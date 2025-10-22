<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository
{
    public function create(array $data): OrderItem
    {
        $item = new OrderItem($data);
        $item->save();
        return $item;
    }
}