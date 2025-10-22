<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function create(array $data): Order
    {
        $order = new Order($data);
        $order->save();
        return $order;
    }
}