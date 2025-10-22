@extends('layouts.admin')

@section('title', __('Product Details'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Product Details') }}</h1>
                        <p class="admin-order-subtitle">{{ $product->name ?? '' }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Back to Products') }}
                </a>
                <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13M18.5 2.5C18.8978 2.10218 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10218 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Edit Product') }}
                </a>
            </div>
        </div>

        <!-- Product Information -->
        <div class="admin-order-grid-modern">
            <!-- Basic Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Basic Information') }}
                    </h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 7L9 18L4 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Name') }}
                            </div>
                            <div class="admin-info-value">{{ $product->name }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                                </svg>
                                {{ __('SKU') }}
                            </div>
                            <div class="admin-info-value">{{ $product->sku ?? '-' }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                                    <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                                </svg>
                                {{ __('Type') }}
                            </div>
                            <div class="admin-info-value">
                                <span class="admin-badge admin-badge-secondary">{{ $product->type }}</span>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Category') }}
                            </div>
                            <div class="admin-info-value">{{ $product->category->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2" />
                            <path d="M17 5H9.5C8.11929 5 7 6.11929 7 7.5S8.11929 10 9.5 10H14.5C15.8807 10 17 11.1193 17 12.5S15.8807 15 14.5 15H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Pricing Information') }}
                    </h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2" />
                                    <path d="M17 5H9.5C8.11929 5 7 6.11929 7 7.5S8.11929 10 9.5 10H14.5C15.8807 10 17 11.1193 17 12.5S15.8807 15 14.5 15H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Effective Price') }}
                            </div>
                            <div class="admin-info-value">
                                <span class="admin-badge admin-badge-primary">{{ number_format($product->effectivePrice(),2) }}</span>
                            </div>
                        </div>
                        @if($product->isOnSale())
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                                </svg>
                                {{ __('On Sale') }}
                            </div>
                            <div class="admin-info-value">
                                <span class="admin-badge admin-badge-success">{{ number_format($product->sale_price,2) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                        <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Product Gallery') }}
                </h3>
            </div>
            <div class="admin-card-body">
                @if(!empty($psGallery))
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($psGallery as $img)
                    <div class="admin-item-placeholder">
                        <img src="{{ $img }}" class="obj-cover w-100 h-100 rounded" alt="Product Image">
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                            <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h5>{{ __('No gallery images') }}</h5>
                    <p class="admin-text-muted">{{ __('This product has no gallery images.') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Variations -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Product Variations') }}
                </h3>
                <div class="admin-badge-count">{{ $product->variations->count() }} {{ __('variations') }}</div>
            </div>
            <div class="admin-card-body">
                @if($product->variations->count())
                <div class="table-responsive">
                    <table class="table table-striped admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('SKU') }}</th>
                                <th>{{ __('Attributes') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Stock') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variations as $v)
                            <tr>
                                <td>
                                    <span class="admin-code">{{ $v->sku ?? '-' }}</span>
                                </td>
                                <td>
                                    @foreach($v->attribute_data ?? [] as $k=>$val)
                                    <div class="admin-item-meta">
                                        <strong>{{ $k }}:</strong> {{ $val }}
                                    </div>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="admin-stock-value">{{ number_format($v->effectivePrice(),2) }}</span>
                                </td>
                                <td>
                                    @if($v->manage_stock)
                                    <span class="admin-stock-value">{{ $v->availableStock ?? $v->stock_qty }}</span>
                                    @else
                                    <span class="admin-text-muted">{{ __('N/A') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h5>{{ __('No variations') }}</h5>
                    <p class="admin-text-muted">{{ __('This product has no variations.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection