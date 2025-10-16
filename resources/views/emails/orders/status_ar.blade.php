<p>تم تحديث حالة الطلب رقم {{ $order->id }} إلى: {{ $status }}.</p>
@if(!empty($tracking))
    <p>بيانات التتبع:</p>
    <ul>
        @if(!empty($tracking['carrier']))<li>الشركة: {{ $tracking['carrier'] }}</li>@endif
        @if(!empty($tracking['tracking_number']))<li>رقم التتبع: {{ $tracking['tracking_number'] }}</li>@endif
        @if(!empty($tracking['tracking_url']))<li><a href="{{ $tracking['tracking_url'] }}">تتبع الشحنة</a></li>@endif
    </ul>
@endif
<p>عرض الطلب: <a href="{{ route('orders.show', $order) }}">الطلب #{{ $order->id }}</a></p>
