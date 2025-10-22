<p>Your payment for Order #{{ $order->id }} is now: {{ $status }}.</p>
<p>Amount: {{ $payment->amount }} {{ $payment->currency }}</p>
<p>View order: <a href="{{ route('orders.show', $order) }}">Order #{{ $order->id }}</a></p>
