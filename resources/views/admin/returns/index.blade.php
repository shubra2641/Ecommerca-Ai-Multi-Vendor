@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>{{ __('Return Requests') }}</h1>
    <div class="card modern-card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Order</th>
                <th>User</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td><a href="{{ route('admin.orders.show', $item->order) }}">#{{ $item->order_id }}</a></td>
                <td>{{ $item->order->user?->name ?? $item->order->user_id }}</td>
                <td>{{ $item->updated_at->toDateTimeString() }}</td>
                        <td><span class="badge bg-info text-dark">{{ $item->return_status }}</span></td>
                        <td><a class="btn btn-sm btn-primary" href="{{ route('admin.returns.show', $item) }}">{{ __('View') }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
        </div>
    </div>
    {{ $items->links() }}
</div>
@endsection