@extends('layouts.admin')

@section('content')
<h1>{{ __('Orders') }}</h1>
<table class="table align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('Items') }}</th>
            <th>{{ __('Customer') }}</th>
            <th>{{ __('Shipping') }}</th>
            <th>{{ __('Total') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Created') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td><span class="badge bg-secondary">#{{ $order->id }}</span></td>
            <td class="max-w-220">
                @if(($ordersPrepared[$order->id]['firstItem'] ?? null))
                <strong>{{ $ordersPrepared[$order->id]['firstItem']->name }}</strong>
                @if($ordersPrepared[$order->id]['variantLabel'])<div class="text-muted small">{{ $ordersPrepared[$order->id]['variantLabel'] }}</div>@endif
                @if($order->items->count()>1)
                <div class="small text-muted">+ {{ $order->items->count()-1 }} {{ __('more') }}</div>
                @endif
                @endif
            </td>
            <td>
                <div class="small fw-bold">{{ $order->user->name ?? __('Guest') }}</div>
                <div class="text-muted small">{{ $order->user->email ?? '' }}</div>
            </td>
            <td>
                <div class="small">{{ e($ordersPrepared[$order->id]['shipText'] ?? '') }}</div>
            </td>
            <td>{{ number_format($order->total,2) }} {{ $order->currency }}</td>
            <td>
                <span class="badge bg-info text-dark">{{ ucfirst($order->status) }}</span><br>
                <span
                    class="badge bg-{{ $order->payment_status==='paid' ? 'success':'warning' }} mt-1">{{ ucfirst($order->payment_status) }}</span>
            </td>
            <td class="small">{{ $order->created_at->format('Y-m-d H:i') }}</td>
            <td><a class="btn btn-sm btn-outline-primary"
                    href="{{ route('admin.orders.show', $order->id) }}">{{ __('View') }}</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $orders->links() }}
@endsection