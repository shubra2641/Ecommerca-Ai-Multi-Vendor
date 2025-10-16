@extends('vendor.layout')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h2 class="mb-0">{{ __('Orders for my Products') }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('vendor.orders.export', request()->only(['q','status','start_date','end_date'])) }}" class="btn btn-success btn-sm">{{ __('Export CSV') }}</a>
            <form method="post" action="{{ route('vendor.orders.export.request') }}">
                @csrf
                <input type="hidden" name="status" value="{{ request('status') }}" />
                <input type="hidden" name="start_date" value="{{ request('start_date') }}" />
                <input type="hidden" name="end_date" value="{{ request('end_date') }}" />
                <button class="btn btn-outline-primary btn-sm">{{ __('Request Export (email)') }}</button>
            </form>
        </div>
    </div>

    <form method="get" class="mb-3 row gx-2 gy-2">
        <div class="col-md-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search order id...') }}" class="form-control" />
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">{{ __('All statuses') }}</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>pending</option>
                <option value="processing" {{ request('status')=='processing'?'selected':'' }}>processing</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>completed</option>
                <option value="refunded" {{ request('status')=='refunded'?'selected':'' }}>refunded</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" />
        </div>
        <div class="col-md-2">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" />
        </div>
        <div class="col-md-3 d-flex">
            <button class="btn btn-primary me-2">{{ __('Filter') }}</button>
            <a href="{{ route('vendor.orders.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
        </div>
    </form>

    <div class="table-responsive">
    <table class="table table-hover mt-4">
        <thead><tr><th>{{ __('Order #') }}</th><th>{{ __('Date') }}</th><th>{{ __('Product') }}</th><th>{{ __('Qty') }}</th><th class="text-end">{{ __('Total') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
        <tbody>
        @forelse($items as $it)
            <tr>
                <td><a href="{{ route('vendor.orders.show', $it->id) }}">#{{ $it->order_id }}</a></td>
                <td>{{ optional($it->order?->created_at)->format('Y-m-d H:i') }}</td>
                <td>{{ $it->product?->name ?? __('-') }}</td>
                <td>{{ $it->qty ?? $it->quantity ?? 1 }}</td>
                <td class="text-end">{{ number_format((float)(($it->price ?? 0) * ($it->qty ?? $it->quantity ?? 1)), 2) }} {{ config('app.currency', 'USD') }}</td>
                <td>{{ ucfirst($it->order?->status ?? '') }}</td>
                <td><a href="{{ route('vendor.orders.show', $it->id) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a></td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <p class="mb-2">{{ __('No orders found for your products yet.') }}</p>
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-primary">{{ __('Manage Products') }}</a>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    {{ $items->withQueryString()->links() }}
</div>
@endsection
