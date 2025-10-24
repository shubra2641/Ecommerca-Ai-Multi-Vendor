@extends('layouts.admin')

@section('title', __('Orders Management'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Orders Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('View and manage customer orders') }}</p>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-list"></i>
                    {{ __('Orders List') }}
                </h3>
                <p class="admin-card-subtitle">{{ __('Browse and manage customer orders') }}</p>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Shipping') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="d-none d-md-table-cell">{{ __('Created') }}</th>
                                <th width="150">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="admin-badge admin-badge-secondary">#{{ $order->id }}</span>
                                    </div>
                                </td>
                                <td class="max-w-220">
                                    @if(($ordersPrepared[$order->id]['firstItem'] ?? null))
                                    <div class="fw-semibold">{{ $ordersPrepared[$order->id]['firstItem']->name }}</div>
                                    @if($ordersPrepared[$order->id]['variantLabel'])<div class="admin-text-muted small">{{ $ordersPrepared[$order->id]['variantLabel'] }}</div>@endif
                                    @if($order->items->count()>1)
                                    <div class="admin-text-muted small">+ {{ $order->items->count()-1 }} {{ __('more') }}</div>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $order->user->name ?? __('Guest') }}</div>
                                    <div class="admin-text-muted small">{{ $order->user->email ?? '' }}</div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="admin-text-muted small">{{ e($ordersPrepared[$order->id]['shipText'] ?? '') }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ number_format($order->total,2) }} {{ $order->currency }}</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="admin-badge admin-badge-info">{{ ucfirst($order->status) }}</span>
                                        <span class="admin-badge admin-badge-{{ $order->payment_status==='paid' ? 'success':'warning' }}">{{ ucfirst($order->payment_status) }}</span>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="admin-text-muted small">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                <div class="admin-card-footer">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                        <div class="admin-text-muted small">
                            {{ __('Showing') }} {{ $orders->firstItem() ?? 0 }} {{ __('to') }} {{ $orders->lastItem() ?? 0 }}
                            {{ __('of') }} {{ $orders->total() }} {{ __('results') }}
                        </div>
                        <div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection