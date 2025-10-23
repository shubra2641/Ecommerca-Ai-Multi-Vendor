<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class OrderCancelled
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(public Order $order)
    {
    }
}
