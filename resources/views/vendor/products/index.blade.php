@extends('vendor.layout')

@section('title', __('Products Management'))

@section('content')
@include('admin.partials.page-header', [
    'title' => __('Products Management'),
    'subtitle' => __('Manage your product catalog'),
    'actions' => '<a href="'.route('vendor.products.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> <span class="d-none d-sm-inline">'.e(__('Add Product')).'</span></a>'
])

<div class="vendor-stats-wrapper">
    <div class="row mb-4 g-3 vendor-stats">
    <div class="col-6 col-lg-3">
        <div class="stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $products->total() }}</div>
                    <div class="stats-label">{{ __('Total Products') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-box"></i></div>
            </div>
        </div>
    </div>
    <!-- ... other stat cards can remain or be removed as needed ... -->
    </div>
</div>

<!-- Floating Add button for mobile -->
<a href="{{ route('vendor.products.create') }}" class="floating-add-btn d-md-none" aria-label="Add Product">
    <span class="fab-icon">+</span>
    <span class="fab-text">{{ __('Add Product') }}</span>
</a>

<div class="card modern-card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
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
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('Name / SKU') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">{{ __('Category') }}</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">-- {{ __('All') }} --</option>
                    {{-- Categories provided by VendorProductsIndexComposer: $vendorProductCategories --}}
                    @foreach($vendorProductCategories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                <button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> <span class="d-none d-sm-inline">{{ __('Filter') }}</span></button>
                <a href="{{ route('vendor.products.index') }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Clear') }}">×</a>
            </div>
        </form>

        {{-- Mobile card grid: visible on small screens, hidden on md+ --}}
        <div class="d-md-none vendor-mobile-grid">
            @foreach($products as $p)
                <div class="card modern-card vendor-mobile-card">
                    <div class="vendor-mobile-thumb">
                        @if($p->main_image)
                            <a href="{{ route('products.show',$p->slug) }}"><img src="{{ asset('storage/' . $p->main_image) }}" alt="{{ $p->name }}"></a>
                        @else
                            <a href="{{ route('products.show',$p->slug) }}"><div class="vendor-mobile-placeholder"></div></a>
                        @endif
                    </div>
                    <div class="vendor-mobile-body">
                        <div class="vendor-mobile-title"><a href="{{ route('products.show',$p->slug) }}">{{ Str::limit($p->name, 60) }}</a></div>
                        <div class="vendor-mobile-meta small text-muted">SKU: {{ $p->sku ?: '-' }} • {{ $p->category->name ?? '-' }}</div>
                        <div class="vendor-mobile-price fw-semibold">{{ number_format($p->price,2) }}</div>
                        <div class="vendor-mobile-actions">
                            <a href="{{ route('vendor.products.edit',$p) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('vendor.products.destroy',$p) }}" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" data-confirm="{{ __('Delete this product?') }}"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-responsive d-none d-md-block">
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
                            </td>
                            <td class="d-none d-md-table-cell"><span class="badge bg-secondary text-capitalize">{{ $p->type }}</span></td>
                            <td class="d-none d-lg-table-cell">{{ $p->category->name ?? '-' }}</td>
                            <td>
                                <div class="fw-semibold">{{ number_format($p->price,2) }}</div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div class="d-flex flex-wrap gap-1">
                                    @if($p->is_featured)<span class="badge bg-warning text-dark">{{ __('Featured') }}</span>@endif
                                    @if($p->is_best_seller)<span class="badge bg-primary">{{ __('Best') }}</span>@endif
                                    @if(!$p->active)<span class="badge bg-danger">{{ __('Inactive') }}</span>@endif
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($p->manage_stock)
                                    @if(isset($vendorProductStocks[$p->id]))
                                        <span class="fw-semibold">{{ $vendorProductStocks[$p->id]['available'] }}</span>
                                        <span class="text-muted small">/{{ $vendorProductStocks[$p->id]['stock_qty'] }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                @else
                                    <span class="text-muted small">{{ __('N/A') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('vendor.products.edit',$p) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('vendor.products.destroy',$p) }}" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" data-confirm="{{ __('Delete this product?') }}"><i class="fas fa-trash"></i></button>
                                    </form>
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
                                    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{ __('Add Product') }}</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-muted small">{{ __('Showing') }} {{ $products->firstItem() }} - {{ $products->lastItem() }} {{ __('of') }} {{ $products->total() }}</div>
                <div class="pagination-links">{{ $products->links() }}</div>
            </div>
        @endif
    </div>
</div>

@endsection
