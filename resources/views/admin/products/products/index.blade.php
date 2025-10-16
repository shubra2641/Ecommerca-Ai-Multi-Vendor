@extends('layouts.admin')

@section('title', __('Products Management'))

@section('content')
@include('admin.partials.page-header', [
'title' => __('Products Management'),
'subtitle' => __('Manage all products in the catalog'),
'actions' => '<a href="'.route('admin.products.export').'" class="btn btn-outline-secondary d-none d-sm-inline-block"><i
        class="fas fa-download"></i> '.e(__('Export')).'</a> <a href="'.route('admin.products.export').'"
    class="btn btn-outline-secondary d-sm-none"><i class="fas fa-download"></i></a> <a
    href="'.route('admin.products.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> <span
        class="d-none d-sm-inline">'.e(__('Add Product')).'</span></a>'
])

<!-- Stats Cards -->
<div class="row mb-4 g-3">
    <div class="col-6 col-lg-3">
    <div class="stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$products->total() }}">{{ $products->total() }}</div>
                    <div class="stats-label">{{ __('Total Products') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-box"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
    <div class="stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$products->where('active', true)->count() }}">{{ $products->where('active', true)->count() }}</div>
                    <div class="stats-label">{{ __('Active Products') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
    <div class="stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$products->where('is_featured', true)->count() }}">{{ $products->where('is_featured', true)->count() }}</div>
                    <div class="stats-label">{{ __('Featured Products') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card stats-card-warning h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$products->where('is_best_seller', true)->count() }}">{{ $products->where('is_best_seller', true)->count() }}</div>
                    <div class="stats-label">{{ __('Best Sellers') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-trophy"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="modern-card">
    <div
        class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h5 class="card-title mb-0">{{ __('Products List') }}</h5>
            <small class="text-muted">{{ __('Browse and manage your product catalog') }}</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <select name="per_page" class="form-select form-select-sm js-per-page-select" data-url-prefix="{{ request()->url() }}?per_page=" data-url-suffix="&{{ http_build_query(request()->except('per_page')) }}">
                <option value="10" @selected(request('per_page', 10)==10)>10 {{ __('per page') }}</option>
                <option value="25" @selected(request('per_page', 10)==25)>25 {{ __('per page') }}</option>
                <option value="50" @selected(request('per_page', 10)==50)>50 {{ __('per page') }}</option>
                <option value="100" @selected(request('per_page', 10)==100)>100 {{ __('per page') }}</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4 small align-items-end" autocomplete="off">
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">{{ __('Search') }}</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                    placeholder="{{ __('Name / SKU') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">{{ __('Category') }}</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">-- {{ __('All') }} --</option>
                    @foreach(\App\Models\ProductCategory::orderBy('name')->get() as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">{{ __('Type') }}</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">-- {{ __('All') }} --</option>
                    <option value="simple" @selected(request('type')==='simple' )>{{ __('Simple') }}</option>
                    <option value="variable" @selected(request('type')==='variable' )>{{ __('Variable') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">{{ __('Stock Status') }}</label>
                <select name="stock" class="form-select form-select-sm">
                    <option value="">-- {{ __('All') }} --</option>
                    <option value="low" @selected(request('stock')==='low' )>{{ __('Low') }}</option>
                    <option value="soon" @selected(request('stock')==='soon' )>{{ __('Soon') }}</option>
                    <option value="in" @selected(request('stock')==='in' )>{{ __('In Stock') }}</option>
                    <option value="na" @selected(request('stock')==='na' )>{{ __('N/A') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">{{ __('Flags') }}</label>
                <select name="flag" class="form-select form-select-sm">
                    <option value="">-- {{ __('All') }} --</option>
                    <option value="featured" @selected(request('flag')==='featured' )>{{ __('Featured') }}</option>
                    <option value="best" @selected(request('flag')==='best' )>{{ __('Best Seller') }}</option>
                    <option value="inactive" @selected(request('flag')==='inactive' )>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                <button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> <span
                        class="d-none d-sm-inline">{{ __('Filter') }}</span></button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary"
                    title="{{ __('Clear') }}">×</a>
            </div>
        </form>
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
                                {{ number_format($p->sale_price,2) }}</div>
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
            {{ __('of') }} {{ $products->total() }}</div>
        <div class="pagination-links">{{ $products->links() }}</div>
    </div>
    @endif
</div>
@endsection