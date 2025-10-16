<p>Payment status for order #{{ $order->id }} has been updated to: {{ $status }}.</p>
<p>Amount: {{ $payment->amount }} {{ $payment->currency }}</p>
<p>View Order: <a href="{{ route('orders.show', $order) }}">Order #{{ $order->id }}</a></p>
