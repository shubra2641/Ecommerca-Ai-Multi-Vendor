<p>Order #{{ $order->id }} status updated to: {{ $status }}.</p>
@if(!empty($tracking))
    <p>Tracking info:</p>
    <ul>
        @if(!empty($tracking['carrier']))<li>Carrier: {{ $tracking['carrier'] }}</li>@endif
        @if(!empty($tracking['tracking_number']))<li>Tracking number: {{ $tracking['tracking_number'] }}</li>@endif
        @if(!empty($tracking['tracking_url']))<li><a href="{{ $tracking['tracking_url'] }}">Track shipment</a></li>@endif
    </ul>
@endif
<p>View your order: <a href="{{ route('orders.show', $order) }}">Order #{{ $order->id }}</a></p>
