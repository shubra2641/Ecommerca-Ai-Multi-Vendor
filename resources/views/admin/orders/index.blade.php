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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L18 21H6L5 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 15V15.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Orders Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('View and manage customer orders') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary" data-action="export-orders">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 19.6642C20.0391 19.2893 19.5304 19 19 19H5C4.46957 19 3.96086 19.2893 3.58579 19.6642C3.21071 20.0391 3 20.5304 3 21V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Export') }}
                </button>
            </div>
        </div>

        <!-- Orders List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
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
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" />
                                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                                        </svg>
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