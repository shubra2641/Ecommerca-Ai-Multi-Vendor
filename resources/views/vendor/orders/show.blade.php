@extends('vendor.layout')

@section('title', __('Order Details'))

@section('content')
<div class="vendor-order-details" id="main-content">
    <!-- Skip to content link for accessibility -->
    <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>
    
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav" aria-label="{{ __('Breadcrumb navigation') }}">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('vendor.dashboard') }}" class="breadcrumb-link">
                    <i class="fas fa-home" aria-hidden="true"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('vendor.orders.index') }}" class="breadcrumb-link">
                    <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                    <span>{{ __('Orders') }}</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-receipt" aria-hidden="true"></i>
                <span>{{ __('Order Details') }}</span>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <header class="page-header" role="banner">
        <div class="header-content">
            <div class="header-title">
                <div class="title-icon" aria-hidden="true">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="title-text">
                    <h1 class="page-title">
                        {{ __('Order Item Details') }}
                        <span class="order-badge" data-tooltip="{{ __('Order Item ID') }}">#{{ $item->id }}</span>
                    </h1>
                    <p class="page-subtitle">{{ __('Complete information about this order item') }}</p>
                    <div class="header-meta">
                        <span class="meta-item">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                            <time datetime="{{ $item->created_at?->toISOString() }}">{{ $item->created_at?->format('M d, Y H:i') ?? __('N/A') }}</time>
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-hashtag" aria-hidden="true"></i>
                            {{ __('Order') }} #{{ $item->order_id }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary btn-with-icon" data-tooltip="{{ __('Return to orders list') }}">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>{{ __('Back to Orders') }}</span>
            </a>
            <button type="button" class="btn btn-primary btn-with-icon" data-action="print" data-tooltip="{{ __('Print order details') }}">
                <i class="fas fa-print" aria-hidden="true"></i>
                <span>{{ __('Print Details') }}</span>
            </button>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="content-grid" role="main">
        <!-- Order Summary Card -->
        <section class="order-summary-card card" aria-labelledby="order-summary-title">
            <div class="card-header">
                <h2 class="card-title" id="order-summary-title">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    {{ __('Order Summary') }}
                </h2>
                <div class="card-actions">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-action="refresh-summary" data-tooltip="{{ __('Refresh summary data') }}">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                        <span class="sr-only">{{ __('Refresh') }}</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="summary-stats">
                    <div class="stats-card stats-card-danger" data-animation-delay="0.1s">
                        <div class="stats-card-body">
                            <div class="stats-icon" aria-hidden="true">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <div class="stats-card-content">
                                <div class="stats-label">{{ __('Order ID') }}</div>
                                <div class="stats-number" data-tooltip="{{ __('Click to copy') }}" data-copy="#{{ $item->order_id }}">{{ $item->order_id }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card stats-card-success" data-animation-delay="0.2s">
                        <div class="stats-card-body">
                            <div class="stats-icon" aria-hidden="true">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stats-card-content">
                                <div class="stats-label">{{ __('Total Amount') }}</div>
                                <div class="stats-number">
                                    <span class="amount">{{ number_format((float)(($item->price ?? 0) * ($item->qty ?? $item->quantity ?? 1)), 2) }}</span>
                                    <span class="currency">{{ config('app.currency', 'USD') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4" data-animation-delay="0.3s">
                        <div class="card modern-card stats-card">
                            <div class="stats-card-body">
                            <div class="stats-icon" aria-hidden="true">
                                <i class="fas fa-cubes"></i>
                            </div>
                            <div class="stats-card-content">
                                <div class="stats-label">{{ __('Quantity') }}</div>
                                <div class="stats-number">{{ $item->qty ?? $item->quantity ?? 1 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card stats-card-primary" data-animation-delay="0.4s">
                        <div class="stats-card-body">
                            <div class="stats-icon" aria-hidden="true">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="stats-card-content">
                                <div class="stats-label">{{ __('Unit Price') }}</div>
                                <div class="stats-number">
                                    <span class="amount">{{ number_format((float)($item->price ?? 0), 2) }}</span>
                                    <span class="currency">{{ config('app.currency', 'USD') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="order-meta">
                    <div class="meta-items">
                        <div class="meta-item" data-animation-delay="0.5s">
                            <div class="meta-icon" aria-hidden="true">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="meta-content">
                                <span class="meta-label">{{ __('Order Date') }}</span>
                                <time class="meta-value" datetime="{{ $item->created_at?->toISOString() }}">{{ $item->created_at?->format('M d, Y H:i') ?? __('N/A') }}</time>
                            </div>
                        </div>
                        <div class="meta-item" data-animation-delay="0.6s">
                            <div class="meta-icon" aria-hidden="true">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="meta-content">
                                <span class="meta-label">{{ __('Status') }}</span>
                                <span class="status-badge status-{{ strtolower($item->order?->status ?? $item->status ?? 'pending') }}" 
                                      role="status" 
                                      aria-label="{{ __('Order status') }}: {{ ucfirst($item->order?->status ?? $item->status ?? 'Pending') }}">
                                    <i class="fas fa-{{ $item->order?->status === 'delivered' ? 'check-circle' : ($item->order?->status === 'shipped' ? 'truck' : ($item->order?->status === 'processing' ? 'cog' : 'clock')) }}" aria-hidden="true"></i>
                                    <span>{{ ucfirst($item->order?->status ?? $item->status ?? 'Pending') }}</span>
                                </span>
                            </div>
                        </div>
                        @if($item->order?->tracking_number)
                        <div class="meta-item" data-animation-delay="0.7s">
                            <div class="meta-icon" aria-hidden="true">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="meta-content">
                                <span class="meta-label">{{ __('Tracking Number') }}</span>
                                <span class="meta-value tracking-number" data-tooltip="{{ __('Click to copy') }}" data-copy="{{ $item->order->tracking_number }}">{{ $item->order->tracking_number }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Information Card -->
        @if($item->product)
        <section class="product-info-card card" aria-labelledby="product-info-title">
            <div class="card-header">
                <h2 class="card-title" id="product-info-title">
                    <i class="fas fa-box" aria-hidden="true"></i>
                    {{ __('Product Information') }}
                </h2>
                <div class="card-actions">
                    @if($item->product->slug && Route::has('product.show'))
                    <a href="{{ route('product.show', $item->product->slug) }}" 
                       class="btn btn-sm btn-outline-primary" 
                       target="_blank" 
                       data-tooltip="{{ __('View product page') }}">
                        <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                        <span class="sr-only">{{ __('View Product') }}</span>
                    </a>
                    @endif
                </div>
            </div>
            
            <div class="card-body">
                <div class="product-showcase">
                    <div class="product-image-container">
                        @if($item->product->image)
                            <div class="product-image">
                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="product-thumbnail"
                                     loading="lazy"
                                     data-src="{{ asset('storage/' . $item->product->image) }}">
                                <div class="image-overlay">
                                    <button type="button" 
                                            class="btn btn-light btn-sm" 
                                            data-modal-target="imageModal"
                                            data-tooltip="{{ __('View full size image') }}"
                                            aria-label="{{ __('View full size image') }}">
                                        <i class="fas fa-search-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="no-image-placeholder">
                                <div class="placeholder-icon" aria-hidden="true">
                                    <i class="fas fa-image"></i>
                                </div>
                                <span class="placeholder-text">{{ __('No Image Available') }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="product-details">
                        <div class="product-header">
                            <h3 class="product-name">{{ $item->product->name }}</h3>
                            @if($item->product->sku)
                                <div class="product-sku">
                                    <span class="sku-label">{{ __('SKU') }}:</span>
                                    <span class="sku-value" data-tooltip="{{ __('Click to copy') }}" data-copy="{{ $item->product->sku }}">{{ $item->product->sku }}</span>
                                </div>
                            @endif
                            @if($item->product->brand)
                                <div class="product-brand">
                                    <span class="brand-label">{{ __('Brand') }}:</span>
                                    <span class="brand-value">{{ $item->product->brand }}</span>
                                </div>
                            @endif
                        </div>
                        
                        @if($item->product->description)
                            <div class="product-description">
                                <div class="description-content">
                                    <p>{{ Str::limit($item->product->description, 250) }}</p>
                                </div>
                                @if(strlen($item->product->description) > 250)
                                    <button type="button" 
                                            class="btn btn-link btn-sm p-0" 
                                            data-modal-target="descriptionModal"
                                            aria-label="{{ __('Read full description') }}">
                                        <i class="fas fa-expand-alt" aria-hidden="true"></i>
                                        {{ __('Read More') }}
                                    </button>
                                @endif
                            </div>
                        @endif
                        
                        <div class="product-attributes">
                            <h4 class="attributes-title">{{ __('Product Specifications') }}</h4>
                            <div class="attributes-grid">
                                @if($item->product->category)
                                    <div class="attribute-item" data-animation-delay="0.1s">
                                        <div class="attribute-icon" aria-hidden="true">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div class="attribute-content">
                                            <span class="attribute-label">{{ __('Category') }}</span>
                                            <span class="attribute-value">{{ $item->product->category->name ?? $item->product->category }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->product->weight)
                                    <div class="attribute-item" data-animation-delay="0.2s">
                                        <div class="attribute-icon" aria-hidden="true">
                                            <i class="fas fa-weight-hanging"></i>
                                        </div>
                                        <div class="attribute-content">
                                            <span class="attribute-label">{{ __('Weight') }}</span>
                                            <span class="attribute-value">{{ $item->product->weight }} kg</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->product->dimensions)
                                    <div class="attribute-item" data-animation-delay="0.3s">
                                        <div class="attribute-icon" aria-hidden="true">
                                            <i class="fas fa-ruler-combined"></i>
                                        </div>
                                        <div class="attribute-content">
                                            <span class="attribute-label">{{ __('Dimensions') }}</span>
                                            <span class="attribute-value">{{ $item->product->dimensions }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->product->stock_quantity !== null)
                                    <div class="attribute-item" data-animation-delay="0.4s">
                                        <div class="attribute-icon" aria-hidden="true">
                                            <i class="fas fa-boxes"></i>
                                        </div>
                                        <div class="attribute-content">
                                            <span class="attribute-label">{{ __('Stock') }}</span>
                                            <span class="attribute-value stock-{{ $item->product->stock_quantity > 10 ? 'high' : ($item->product->stock_quantity > 0 ? 'low' : 'out') }}">{{ $item->product->stock_quantity }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="attribute-item" data-animation-delay="0.5s">
                                    <div class="attribute-icon" aria-hidden="true">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div class="attribute-content">
                                        <span class="attribute-label">{{ __('Added Date') }}</span>
                                        <time class="attribute-value" datetime="{{ $item->product->created_at?->toISOString() }}">{{ $item->product->created_at?->format('M d, Y') ?? __('N/A') }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <section class="product-info-card card" aria-labelledby="product-info-title">
            <div class="card-header">
                <h2 class="card-title" id="product-info-title">
                    <i class="fas fa-box" aria-hidden="true"></i>
                    {{ __('Product Information') }}
                </h2>
            </div>
            
            <div class="card-body">
                <div class="empty-state" role="alert">
                    <div class="empty-icon" aria-hidden="true">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="empty-title">{{ __('Product Not Found') }}</h3>
                    <p class="empty-description">{{ __('The product information for this order item is not available or has been removed.') }}</p>
                    <div class="empty-actions">
                        <a href="{{ route('vendor.orders.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            {{ __('Back to Orders') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Customer Information Card -->
        @if($item->order && $item->order->user)
        <section class="customer-info-card card" aria-labelledby="customer-info-title">
            <div class="card-header">
                <h2 class="card-title" id="customer-info-title">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    {{ __('Customer Information') }}
                </h2>
                <div class="card-actions">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-tooltip="{{ __('Customer profile') }}">
                        <i class="fas fa-user-circle" aria-hidden="true"></i>
                        <span class="sr-only">{{ __('View Profile') }}</span>
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <div class="customer-profile">
                    <div class="customer-avatar">
                        @if($item->order->user->avatar)
                            <img src="{{ asset('storage/' . $item->order->user->avatar) }}" 
                                 alt="{{ $item->order->user->name }}" 
                                 class="avatar-image"
                                 loading="lazy">
                        @else
                            <div class="avatar-placeholder" aria-hidden="true">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div class="customer-status" data-tooltip="{{ __('Active customer') }}">
                            <span class="status-indicator active" aria-label="{{ __('Customer is active') }}"></span>
                        </div>
                    </div>
                    
                    <div class="customer-details">
                        <div class="customer-header">
                            <h3 class="customer-name">{{ $item->order->user->name }}</h3>
                            <div class="customer-badges">
                                <span class="customer-badge primary">{{ __('Customer') }}</span>
                                @if($item->order->user->email_verified_at)
                                    <span class="customer-badge success" data-tooltip="{{ __('Verified email') }}">
                                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                                        {{ __('Verified') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="customer-contact">
                            <div class="contact-grid">
                                <div class="contact-item" data-animation-delay="0.1s">
                                    <div class="contact-icon" aria-hidden="true">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="contact-content">
                                        <span class="contact-label">{{ __('Email') }}</span>
                                        <a href="mailto:{{ $item->order->user->email }}" 
                                           class="contact-value" 
                                           data-tooltip="{{ __('Send email') }}">
                                            {{ $item->order->user->email }}
                                        </a>
                                    </div>
                                </div>
                                
                                @if($item->order->user->phone)
                                    <div class="contact-item" data-animation-delay="0.2s">
                                        <div class="contact-icon" aria-hidden="true">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="contact-content">
                                            <span class="contact-label">{{ __('Phone') }}</span>
                                            <a href="tel:{{ $item->order->user->phone }}" 
                                               class="contact-value" 
                                               data-tooltip="{{ __('Call customer') }}">
                                                {{ $item->order->user->phone }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="contact-item" data-animation-delay="0.3s">
                                    <div class="contact-icon" aria-hidden="true">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="contact-content">
                                        <span class="contact-label">{{ __('Member Since') }}</span>
                                        <time class="contact-value" datetime="{{ $item->order->user->created_at?->toISOString() }}">{{ $item->order->user->created_at?->format('M d, Y') ?? __('N/A') }}</time>
                                    </div>
                                </div>
                                
                                <div class="contact-item" data-animation-delay="0.4s">
                                    <div class="contact-icon" aria-hidden="true">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div class="contact-content">
                                        <span class="contact-label">{{ __('Total Orders') }}</span>
                                        <span class="contact-value">{{ $item->order->user->orders_count ?? 0 }}</span>
                                    </div>
                                </div>
                                
                                @if($item->order->user->last_login_at)
                                <div class="contact-item" data-animation-delay="0.5s">
                                    <div class="contact-icon" aria-hidden="true">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="contact-content">
                                        <span class="contact-label">{{ __('Last Login') }}</span>
                                        <time class="contact-value" datetime="{{ $item->order->user->last_login_at?->toISOString() }}">{{ $item->order->user->last_login_at?->diffForHumans() ?? __('N/A') }}</time>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Order Actions Card -->
        <section class="order-actions-card card" aria-labelledby="order-actions-title">
            <div class="card-header">
                <h2 class="card-title" id="order-actions-title">
                    <i class="fas fa-cogs" aria-hidden="true"></i>
                    {{ __('Order Actions') }}
                </h2>
                <div class="card-actions">
                    <span class="actions-count" data-tooltip="{{ __('Available actions') }}">{{ __(':count actions', ['count' => 4]) }}</span>
                </div>
            </div>
            
            <div class="card-body">
                <div class="actions-grid">
                    @if($item->order_id && Route::has('vendor.orders.invoice'))
                        <a href="{{ route('vendor.orders.invoice', $item->order_id) }}" 
                           class="action-button success" 
                           data-action="download-invoice" 
                           data-order-id="{{ $item->order_id }}"
                           data-animation-delay="0.1s"
                           aria-label="{{ __('Download PDF invoice for order :id', ['id' => $item->order_id]) }}">
                            <div class="action-icon" aria-hidden="true">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="action-content">
                                <span class="action-title">{{ __('Download Invoice') }}</span>
                                <span class="action-description">{{ __('Get PDF invoice for this order') }}</span>
                            </div>
                            <div class="action-arrow" aria-hidden="true">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    @endif
                    
                    @if(in_array($item->order?->status ?? $item->status, ['shipped', 'delivered']))
                        <button type="button" 
                                class="action-button info" 
                                data-action="track-shipment" 
                                data-order-id="{{ $item->order_id }}"
                                data-animation-delay="0.2s"
                                aria-label="{{ __('Track shipment for order :id', ['id' => $item->order_id]) }}">
                            <div class="action-icon" aria-hidden="true">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="action-content">
                                <span class="action-title">{{ __('Track Shipment') }}</span>
                                <span class="action-description">{{ __('View shipment tracking details') }}</span>
                            </div>
                            <div class="action-arrow" aria-hidden="true">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                    @endif
                    
                    <button type="button" 
                            class="action-button primary" 
                            data-action="print-details"
                            data-animation-delay="0.3s"
                            aria-label="{{ __('Print order details') }}">
                        <div class="action-icon" aria-hidden="true">
                            <i class="fas fa-print"></i>
                        </div>
                        <div class="action-content">
                            <span class="action-title">{{ __('Print Details') }}</span>
                            <span class="action-description">{{ __('Print order item details') }}</span>
                        </div>
                        <div class="action-arrow" aria-hidden="true">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </button>
                    
                    <a href="{{ route('vendor.orders.index') }}" 
                       class="action-button secondary"
                       data-animation-delay="0.4s"
                       aria-label="{{ __('View all orders') }}">
                        <div class="action-icon" aria-hidden="true">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="action-content">
                            <span class="action-title">{{ __('All Orders') }}</span>
                            <span class="action-description">{{ __('View all order items') }}</span>
                        </div>
                        <div class="action-arrow" aria-hidden="true">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Order Timeline (if available) -->
    @if(isset($orderHistory) && $orderHistory->count() > 0)
    <section class="order-timeline" aria-labelledby="order-timeline-title">
        <h2 class="section-title" id="order-timeline-title">
            <i class="fas fa-history" aria-hidden="true"></i>
            {{ __('Order History') }}
        </h2>
        
        <div class="timeline-container" role="list">
            @foreach($orderHistory as $index => $history)
            <div class="timeline-item" role="listitem" data-animation-delay="{{ ($index + 1) * 0.1 }}s">
                <div class="timeline-icon status-{{ strtolower($history->status) }}" aria-hidden="true">
                    <i class="fas fa-{{ $history->status === 'delivered' ? 'check-circle' : ($history->status === 'shipped' ? 'truck' : ($history->status === 'processing' ? 'cog' : 'clock')) }}"></i>
                </div>
                <div class="timeline-content">
                    <h4 class="timeline-title">{{ ucfirst($history->status) }}</h4>
                    @if($history->note)
                        <p class="timeline-description">{{ $history->note }}</p>
                    @endif
                    <time class="timeline-date" datetime="{{ $history->created_at->toISOString() }}">{{ $history->created_at->format('M d, Y H:i') }}</time>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

<!-- Image Modal -->
<div class="modal" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel">{{ __('Product Image') }}</h5>
            <button type="button" class="modal-close" data-modal-close aria-label="{{ __('Close modal') }}">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <div class="modal-body">
            @if($item->product && $item->product->image)
                <div class="modal-image-container">
                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                         alt="{{ $item->product->name }}" 
                         class="modal-image"
                         loading="lazy">
                </div>
                <div class="modal-description">
                    <h6>{{ $item->product->name }}</h6>
                    @if($item->product->sku)
                        <p class="text-muted">{{ __('SKU') }}: {{ $item->product->sku }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Full Description Modal -->
<div class="modal" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="descriptionModalLabel">{{ __('Product Description') }}</h5>
            <button type="button" class="modal-close" data-modal-close aria-label="{{ __('Close modal') }}">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-description">
                @if($item->product && $item->product->description)
                    <div class="description-content">
                        {!! nl2br(e($item->product->description)) !!}
                    </div>
                @else
                    <p class="text-muted">{{ __('No description available') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
