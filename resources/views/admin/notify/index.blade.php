@extends('layouts.admin')

@section('title', __('Product Notifications'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    {{ __('Product Notifications') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage customer interest notifications and alerts') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notify.topProducts') }}" class="admin-btn admin-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('Top Products') }}
                </a>
            </div>
        </div>

        <!-- Summary -->
        @isset($breakdown)
        @include('admin.notify.summary',['breakdown'=>$breakdown])
        @endisset

        <!-- Filters -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="admin-card-title">{{ __('Filters') }}</h3>
            </div>
            <div class="admin-card-body">
                <form method="get" class="admin-form-grid-auto">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Email') }}</label>
                        <input name="email" value="{{ request('email') }}" class="admin-form-input" placeholder="{{ __('Email') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Product ID') }}</label>
                        <input name="product" value="{{ request('product') }}" class="admin-form-input" placeholder="{{ __('Product ID') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Status') }}</label>
                        <select name="status" class="admin-form-select">
                            <option value="">{{ __('All Statuses') }}</option>
                            @foreach(['pending','notified','cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Type') }}</label>
                        <select name="type" class="admin-form-select">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach(['stock','back_in_stock','price_drop'] as $t)
                            <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-group admin-flex-end">
                        <button class="admin-btn admin-btn-primary admin-btn-block">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h3 class="admin-card-title">{{ __('All Notifications') }}</h3>
                <span class="admin-badge-count">{{ $interests->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($interests->count())
                <div class="admin-items-list">
                    @foreach($interests as $interest)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-item-placeholder-cyan">
                                #{{ $interest->id }}
                            </div>
                            <div class="admin-item-details">
                                <div class="admin-item-name">
                                    <a href="{{ route('admin.products.show',$interest->product_id) }}" class="admin-product-link">
                                        {{ $interest->product?->name ?? __('Product') . ' #' . $interest->product_id }}
                                    </a>
                                </div>
                                <div class="admin-payment-details admin-mt-half">
                                    @if($interest->email)
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $interest->email }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    @endif
                                    @if($interest->phone)
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $interest->phone }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    @endif
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $interest->type)) }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                        {{ $interest->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <span class="admin-status-badge status-{{ $interest->status }}">
                                {{ ucfirst($interest->status) }}
                            </span>
                            <div class="admin-actions-flex">
                                @if($interest->status==='pending')
                                <form method="post" action="{{ route('admin.notify.mark',$interest) }}">
                                    @csrf @method('put')
                                    <button class="admin-btn-small admin-btn-success">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('Mark') }}
                                    </button>
                                </form>
                                @endif
                                <form method="post" action="{{ route('admin.notify.delete',$interest) }}" class="js-confirm" data-confirm="{{ __('Delete?') }}">
                                    @csrf @method('delete')
                                    <button class="admin-btn-small admin-btn-danger">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p>{{ __('No interests found') }}</p>
                </div>
                @endif
            </div>
            @if($interests->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $interests->firstItem() }} - {{ $interests->lastItem() }} {{ __('of') }} {{ $interests->total() }}
                </div>
                <div class="pagination-links">
                    {{ $interests->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection