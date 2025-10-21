@extends('layouts.admin')

@section('title', __('Top Interested Products'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('Top Interested Products') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Products with highest customer interest and demand') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notify.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    {{ __('All Notifications') }}
                </a>
            </div>
        </div>

        <!-- Limit Filter -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <h3 class="admin-card-title">{{ __('Display Settings') }}</h3>
            </div>
            <div class="admin-card-body">
                <form class="admin-form-grid-limit">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Limit') }}</label>
                        <input type="number" name="limit" value="{{ $limit }}" class="admin-form-input" min="5" max="200" placeholder="{{ __('Number of products') }}">
                    </div>
                    <button class="admin-btn admin-btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Apply') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Top Products List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <h3 class="admin-card-title">{{ __('Most Requested Products') }}</h3>
                <span class="admin-badge-count">{{ count($rows) }}</span>
            </div>
            <div class="admin-card-body">
                @if(count($rows))
                <div class="admin-items-list">
                    @foreach($rows as $i => $row)
                    @php
                    $product = $ntpResolvedProducts[$i] ?? null;
                    $colors = ['#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a', '#feca57'];
                    $color = $colors[$i % count($colors)];
                    @endphp
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-placeholder-dynamic admin-relative">
                                <div class="admin-rank-badge">
                                    {{ $i + 1 }}
                                </div>
                                @if($product)
                                {{ strtoupper(substr($product->name, 0, 2)) }}
                                @else
                                #{{ $row->product_id }}
                                @endif
                            </div>
                            <div class="admin-item-details">
                                <div class="admin-item-name">
                                    @if($product)
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="admin-product-link">
                                        {{ $product->name }}
                                    </a>
                                    @else
                                    {{ __('Product') }} #{{ $row->product_id }}
                                    @endif
                                </div>
                                <div class="admin-payment-details admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <strong>{{ $row->total }}</strong> {{ __('interests') }}
                                    </span>
                                    @if($product)
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ __('Product ID') }}: #{{ $product->id }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <div class="payment-amount-display admin-rank-display">
                                #{{ $i + 1 }}
                            </div>
                            @if($product)
                            <a href="{{ route('admin.notify.priceChart', $product) }}" class="admin-btn-small admin-btn-primary admin-mt-half">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                {{ __('Price Chart') }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p>{{ __('No top products found') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection