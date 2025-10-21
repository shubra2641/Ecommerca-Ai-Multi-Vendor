@extends('vendor.layout')

@section('title', __('Orders for my Products'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7ZM12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Orders for my Products') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage and track your product orders') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('vendor.orders.export', request()->only(['q','status','start_date','end_date'])) }}" class="admin-btn admin-btn-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Export CSV') }}
                </a>
                <form method="post" action="{{ route('vendor.orders.export.request') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="{{ request('status') }}" />
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}" />
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}" />
                    <button class="admin-btn admin-btn-outline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Request Export (email)') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Filters') }}
                </h3>
            </div>
            <form method="get" class="admin-card-body" autocomplete="off">
                <div class="admin-filter-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Search') }}</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search order id...') }}" class="admin-form-input" />
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Status') }}</label>
                        <select name="status" class="admin-form-input">
                            <option value="">{{ __('All statuses') }}</option>
                            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>pending</option>
                            <option value="processing" {{ request('status')=='processing'?'selected':'' }}>processing</option>
                            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>completed</option>
                            <option value="refunded" {{ request('status')=='refunded'?'selected':'' }}>refunded</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="admin-form-input" />
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="admin-form-input" />
                    </div>
                    <div class="admin-filter-actions">
                        <button class="admin-btn admin-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('vendor.orders.index') }}" class="admin-btn admin-btn-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7ZM12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Orders List') }}
                </h3>
                <div class="admin-badge-count">{{ $items->count() }} {{ __('orders') }}</div>
            </div>
            <div class="admin-card-body">
                @if($items->count() > 0)
                <div class="admin-table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Order #') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 2V5M16 2V5M3 10H21M5 4H19C20.1046 4 21 4.89543 21 6V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V6C3 4.89543 3.89543 4 5 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Date') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 3V21H21V3H3ZM19 19H5V5H19V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Product') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 3V21H21V3H3ZM19 19H5V5H19V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Qty') }}
                                </th>
                                <th class="text-end">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Total') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Status') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $it)
                            <tr>
                                <td>
                                    <a href="{{ route('vendor.orders.show', $it->id) }}" class="admin-fw-semibold">#{{ $it->order_id }}</a>
                                </td>
                                <td>
                                    <div class="admin-fw-semibold">{{ optional($it->order?->created_at)->format('Y-m-d H:i') }}</div>
                                </td>
                                <td>
                                    <div class="admin-fw-semibold">{{ $it->product?->name ?? __('-') }}</div>
                                </td>
                                <td>
                                    <span class="admin-badge admin-badge-secondary">{{ $it->qty ?? $it->quantity ?? 1 }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="admin-fw-semibold">{{ number_format((float)(($it->price ?? 0) * ($it->qty ?? $it->quantity ?? 1)), 2) }} {{ config('app.currency', 'USD') }}</div>
                                </td>
                                <td>
                                    <span class="admin-badge admin-badge-{{ $it->order?->status === 'completed' ? 'success' : ($it->order?->status === 'processing' ? 'info' : ($it->order?->status === 'refunded' ? 'danger' : 'warning')) }}">
                                        {{ ucfirst($it->order?->status ?? '') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('vendor.orders.show', $it->id) }}" class="admin-btn admin-btn-sm admin-btn-outline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="admin-empty-state">
                                        <div class="admin-notification-icon">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7ZM12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <h4>{{ __('No orders found for your products yet.') }}</h4>
                                        <p class="admin-text-muted">{{ __('Start by managing your products to receive orders.') }}</p>
                                        <a href="{{ route('vendor.products.index') }}" class="admin-btn admin-btn-primary">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 3V21H21V3H3ZM19 19H5V5H19V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            {{ __('Manage Products') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @else
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7ZM12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h4>{{ __('No orders found for your products yet.') }}</h4>
                    <p class="admin-text-muted">{{ __('Start by managing your products to receive orders.') }}</p>
                    <a href="{{ route('vendor.products.index') }}" class="admin-btn admin-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3V21H21V3H3ZM19 19H5V5H19V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Manage Products') }}
                    </a>
                </div>
                @endif
            </div>
            @if($items->hasPages())
            <div class="admin-card-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="admin-text-muted small">{{ __('Showing') }} {{ $items->firstItem() }} - {{ $items->lastItem() }} {{ __('of') }} {{ $items->total() }}</div>
                <div class="admin-pagination-links">{{ $items->withQueryString()->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection