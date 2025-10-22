<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="{{ asset('front/css/invoice.css') }}" />
</head>

<body>
	<h1>{{ __('Invoice') }} #{{ $order->id }}</h1>
	<p><strong>{{ __('Date') }}:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
	<p><strong>{{ __('Status') }}:</strong> {{ ucfirst($order->status) }} | <strong>{{ __('Payment') }}:</strong> {{ ucfirst($order->payment_status) }}</p>
	<table>
		<thead>
			<tr>
				<th>{{ __('Product') }}</th>
				<th>{{ __('Qty') }}</th>
				<th>{{ __('Price') }}</th>
				<th>{{ __('Line Total') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($order->items as $it)
			<tr>
				<td>{{ $it->name }}</td>
				<td>{{ $it->qty }}</td>
				<td>{{ number_format($it->price,2) }} {{ $order->currency }}</td>
				<td>{{ number_format($it->price*$it->qty,2) }} {{ $order->currency }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<table class="totals mt-15">
		<tr>
			<td class="w-70 text-right">{{ __('Items Subtotal') }}</td>
			<td>{{ number_format($order->items_subtotal,2) }} {{ $order->currency }}</td>
		</tr>
		@if($order->shipping_price !== null)<tr>
			<td class="text-right">{{ __('Shipping') }}</td>
			<td>{{ number_format($order->shipping_price,2) }} {{ $order->currency }}</td>
		</tr>@endif
		<tr>
			<td class="text-right">{{ __('Grand Total') }}</td>
			<td>{{ number_format($order->total,2) }} {{ $order->currency }}</td>
		</tr>
	</table>
</body>

</html>