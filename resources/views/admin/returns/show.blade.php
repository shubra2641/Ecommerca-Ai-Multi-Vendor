@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card modern-card">
        <div class="card-body">
            <h3 class="mb-3">{{ __('Return Request') }} #{{ $item->id }}</h3>

            <div class="row">
                <div class="col-md-8">
                    <p><strong>{{ __('Product') }}:</strong> {{ $item->name }}</p>
                    <p><strong>{{ __('Order') }}:</strong> <a href="{{ route('admin.orders.show', $item->order) }}">#{{ $item->order_id }}</a></p>
                    <p><strong>{{ __('User') }}:</strong> {{ $item->order->user?->name ?? $item->order->user_id }}</p>
                    <p><strong>{{ __('Reason') }}:</strong> {{ $item->return_reason }}</p>
                    <p><strong>{{ __('Status') }}:</strong> <span class="badge bg-info text-dark">{{ $item->return_status }}</span></p>
                </div>
                <div class="col-md-4">
                    <p><strong>{{ __('Amount') }}:</strong> {{ format_price($item->price ?? 0) }}</p>
                    <p><strong>{{ __('Purchased at') }}:</strong> {{ $item->purchased_at?->toDateTimeString() ?? '-' }}</p>
                    <p><strong>{{ __('Return until:') }}:</strong> {{ $item->refund_expires_at?->toDateString() ?? __('No return') }}</p>
                </div>
            </div>
            @if(!empty($item->meta['user_images']))
    <div><strong>User images:</strong>
        <div class="d-flex gap-2 mt-1">
            @foreach($item->meta['user_images'] as $img)
            <img src="{{ storage_image_url($img) }}" class="max-w-120" />
            @endforeach
        </div>
    </div>
    @endif
    @if(!empty($item->meta['admin_images']))
    <div class="mt-2"><strong>Admin images:</strong>
        <div class="d-flex gap-2 mt-1">
            @foreach($item->meta['admin_images'] as $img)
            <img src="{{ storage_image_url($img) }}" class="max-w-120" />
            @endforeach
        </div>
    </div>
    @endif
    @if(!empty($item->meta['history']))
    <div class="mt-2">
        <strong>History</strong>
        <ul>
            @foreach($item->meta['history'] as $h)
            <li>[{{ $h['when'] }}] {{ $h['actor'] }} - {{ $h['action'] }} {{ $h['note'] ? ': '.$h['note'] : '' }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="post" action="{{ route('admin.returns.update', $item) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-2">
            <label>{{ __('Status') }}</label>
            <select name="return_status" class="form-control">
                <option value="received" {{ $item->return_status === 'received' ? 'selected' : '' }}>received</option>
                <option value="in_repair" {{ $item->return_status === 'in_repair' ? 'selected' : '' }}>in_repair
                </option>
                <option value="shipped_back" {{ $item->return_status === 'shipped_back' ? 'selected' : '' }}>
                    shipped_back</option>
                <option value="delivered" {{ $item->return_status === 'delivered' ? 'selected' : '' }}>delivered
                </option>
                <option value="completed" {{ $item->return_status === 'completed' ? 'selected' : '' }}>completed
                </option>
                <option value="cancelled" {{ $item->return_status === 'cancelled' ? 'selected' : '' }}>cancelled
                </option>
                <option value="pending" {{ $item->return_status === 'pending' ? 'selected' : '' }}>pending</option>
                <option value="rejected" {{ $item->return_status === 'rejected' ? 'selected' : '' }}>rejected</option>
                <option value="approved" {{ $item->return_status === 'approved' ? 'selected' : '' }}>approved</option>
            </select>
        </div>
        <div class="mb-2">
            <label>{{ __('Admin note') }}</label>
            <textarea name="admin_note" class="form-control">{{ $item->meta['admin_note'] ?? '' }}</textarea>
        </div>
        <div class="mb-2">
            <label>{{ __('Attach image') }}</label>
            <input type="file" name="image" accept="image/*" class="form-control" />
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection