@extends('layouts.admin')

@section('title', __('Top Interested Products'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-chart-bar"></i>
                    {{ __('Top Interested Products') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Products with highest customer interest and demand') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notify.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-bell"></i>
                    {{ __('All Notifications') }}
                </a>
            </div>
        </div>

        <!-- Limit Filter -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <i class="fas fa-sliders-h"></i>
                <h3 class="admin-card-title">{{ __('Display Settings') }}</h3>
            </div>
            <div class="admin-card-body">
                <form class="admin-form-grid-limit">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Limit') }}</label>
                        <input type="number" name="limit" value="{{ $limit }}" class="admin-form-input" min="5" max="200" placeholder="{{ __('Number of products') }}">
                    </div>
                    <button class="admin-btn admin-btn-primary">
                        <i class="fas fa-check"></i>
                        {{ __('Apply') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Top Products List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <i class="fas fa-star"></i>
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
                                        <i class="fas fa-bell"></i>
                                        <strong>{{ $row->total }}</strong> {{ __('interests') }}
                                    </span>
                                    @if($product)
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <i class="fas fa-tag"></i>
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
                                <i class="fas fa-chart-line"></i>
                                {{ __('Price Chart') }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <i class="fas fa-chart-bar admin-notification-icon"></i>
                    <p>{{ __('No top products found') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection