<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class OrderRefunded
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(public Order $order) {}
}
