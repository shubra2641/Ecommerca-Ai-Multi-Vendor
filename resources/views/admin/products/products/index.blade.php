@extends('layouts.admin')

@section('title', __('Products Management'))

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
                        <h1 class="admin-order-title">{{ __('Products Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage all products in the catalog') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Product') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ $totalProducts }}">{{ $totalProducts }}</div>
                    <div class="admin-stat-label">{{ __('Total Products') }}</div>
                    <div class="admin-stat-description">{{ __('All products in catalog') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Growing') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ $activeProducts }}">{{ $activeProducts }}</div>
                    <div class="admin-stat-label">{{ __('Active Products') }}</div>
                    <div class="admin-stat-description">{{ __('Currently available') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Active') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ $featuredProducts }}">{{ $featuredProducts }}</div>
                    <div class="admin-stat-label">{{ __('Featured Products') }}</div>
                    <div class="admin-stat-description">{{ __('Highlighted products') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Featured') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ $bestSellers }}">{{ $bestSellers }}</div>
                    <div class="admin-stat-label">{{ __('Best Sellers') }}</div>
                    <div class="admin-stat-description">{{ __('Top performing products') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Top sellers') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ __('Products List') }}
                </h3>
                <div class="admin-badge-count">{{ $products->count() }} {{ __('products') }}</div>
            </div>
            <div class="admin-card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th class="d-none d-md-table-cell">{{ __('Type') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Category') }}</th>
                                <th>{{ __('Pricing') }}</th>
                                <th class="d-none d-md-table-cell">{{ __('Flags') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Stock') }}</th>
                                <th width="120">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $p)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $p->name }}</div>
                                    <div class="text-muted small">SKU: {{ $p->sku ?: '-' }}</div>
                                    <div class="d-md-none mt-1">
                                        <span class="badge bg-secondary text-capitalize me-1">{{ $p->type }}</span>
                                        @if($p->category)<span
                                            class="badge bg-light text-dark">{{ $p->category->name }}</span>@endif
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-secondary text-capitalize">{{ $p->type }}</span>
                                    <span class="badge bg-info text-capitalize">{{ $p->physical_type }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">{{ $p->category->name ?? '-' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ number_format($p->price,2) }}</div>
                                    @if($p->isOnSale())
                                    <div class="small"><span class="badge bg-success">{{ __('Sale') }}</span>
                                        {{ number_format($p->sale_price,2) }}
                                    </div>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($p->is_featured)<span
                                            class="badge bg-warning text-dark">{{ __('Featured') }}</span>@endif
                                        @if($p->is_best_seller)<span class="badge bg-primary">{{ __('Best') }}</span>@endif
                                        @if(!$p->active)<span class="badge bg-danger">{{ __('Inactive') }}</span>@endif
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @if($p->type === 'variable' && $p->variations->isNotEmpty())
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                                        data-bs-target="#variations-{{ $p->id }}" aria-expanded="false"
                                        aria-controls="variations-{{ $p->id }}">
                                        {{ __('Show Variations') }}
                                    </button>
                                    <div class="collapse mt-2" id="variations-{{ $p->id }}">
                                        <div class="card card-body p-2">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('SKU') }}</th>
                                                        <th>{{ __('Name') }}</th>
                                                        <th>{{ __('Manage Stock') }}</th>
                                                        <th>{{ __('Available') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($p->variations as $v)
                                                    <tr>
                                                        <td class="small">{{ $v->sku ?: '-' }}</td>
                                                        <td class="small">{{ $v->name ?? '-' }}</td>
                                                        <td class="small">
                                                            @if($v->manage_stock){{ __('Yes') }}@else{{ __('No') }}@endif</td>
                                                        <td class="small">
                                                            @if($v->manage_stock)
                                                            <span class="fw-semibold {{ $apiStockVariations[$v->id]['class'] ?? '' }}">{{ $apiStockVariations[$v->id]['available'] ?? (($v->stock_qty ?? 0)-($v->reserved_qty ?? 0)) }}</span>
                                                            <span class="text-muted small">/{{ $apiStockVariations[$v->id]['stock_qty'] ?? ($v->stock_qty ?? 0) }}</span>
                                                            @if(($apiStockVariations[$v->id]['badge'] ?? null)==='low') <span class="badge bg-danger">{{ __('Low') }}</span>
                                                            @elseif(($apiStockVariations[$v->id]['badge'] ?? null)==='soon') <span class="badge bg-warning text-dark">{{ __('Soon') }}</span>@endif
                                                            @else
                                                            <span class="text-muted small">{{ __('N/A') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @elseif($p->manage_stock)
                                    <div>
                                        <span class="fw-semibold {{ $apiStockProducts[$p->id]['class'] ?? '' }}">{{ $apiStockProducts[$p->id]['available'] ?? $p->availableStock() }}</span>
                                        <span class="text-muted small">/{{ $apiStockProducts[$p->id]['stock_qty'] ?? ($p->stock_qty ?? 0) }}</span>
                                    </div>
                                    @if(($apiStockProducts[$p->id]['badge'] ?? null)==='low') <span class="badge bg-danger">{{ __('Low') }}</span>
                                    @elseif(($apiStockProducts[$p->id]['badge'] ?? null)==='soon') <span class="badge bg-warning text-dark">{{ __('Soon') }}</span>@endif
                                    @if(!empty($apiStockProducts[$p->id]['backorder']))<span class="badge bg-outline-secondary border">BO</span>@endif
                                    @else
                                    <span class="text-muted small">{{ __('N/A') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($isAdmin)
                                        <a href="{{ route('admin.products.toggleStatus', $p) }}" class="btn btn-sm btn-outline-secondary" title="{{ $p->active ? __('Deactivate') : __('Activate') }}">
                                            <i class="fas fa-{{ $p->active ? 'eye-slash' : 'eye' }}"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('admin.products.edit',$p) }}" class="btn btn-sm btn-outline-primary"
                                            title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy',$p) }}"
                                            class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="{{ __('Delete') }}" data-confirm="{{ __('Delete this product?') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="d-lg-none mt-2">
                                        @if($p->type === 'variable' && $p->variations->isNotEmpty())
                                        <div class="small">
                                            <strong>{{ __('Variations') }}:</strong>
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach($p->variations as $v)
                                                <li class="mb-1">
                                                    <span class="fw-semibold">{{ $v->sku ?: '-' }}</span>
                                                    —
                                                    @if($v->manage_stock)
                                                    <span class="{{ $apiStockVariations[$v->id]['class'] ?? '' }}">{{ $apiStockVariations[$v->id]['available'] ?? (($v->stock_qty ?? 0)-($v->reserved_qty ?? 0)) }}</span>
                                                    <small class="text-muted">/{{ $apiStockVariations[$v->id]['stock_qty'] ?? ($v->stock_qty ?? 0) }}</small>
                                                    @else
                                                    <small class="text-muted">{{ __('N/A') }}</small>
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @else
                                        @if($p->manage_stock)
                                        <small class="text-muted">{{ __('Stock') }}:
                                            <span class="fw-semibold {{ $apiStockProducts[$p->id]['class'] ?? '' }}">{{ $apiStockProducts[$p->id]['available'] ?? $p->availableStock() }}</span>
                                        </small>
                                        @endif
                                        <div class="d-md-none mt-1">
                                            @if($p->is_featured)<span
                                                class="badge bg-warning text-dark me-1">{{ __('Featured') }}</span>@endif
                                            @if($p->is_best_seller)<span
                                                class="badge bg-primary me-1">{{ __('Best') }}</span>@endif
                                            @if(!$p->active)<span class="badge bg-danger">{{ __('Inactive') }}</span>@endif
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3"></i>
                                        <h5>{{ __('No products found.') }}</h5>
                                        <p class="mb-3">{{ __('Start by adding your first product.') }}</p>
                                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> {{ __('Add Product') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($products->hasPages())
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-muted small">{{ __('Showing') }} {{ $products->firstItem() }} - {{ $products->lastItem() }}
                    {{ __('of') }} {{ $products->total() }}
                </div>
                <div class="pagination-links">{{ $products->links() }}</div>
            </div>
            @endif
        </div>
        @endsection