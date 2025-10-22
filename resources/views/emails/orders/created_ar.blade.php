<p>Thank you for your order #{{ $order->id }}.</p>
<p>Total: {{ $order->total }} {{ $order->currency }}</p>
<p>You can view your order here: <a href="{{ route('orders.show', $order) }}">Order #{{ $order->id }}</a></p>
