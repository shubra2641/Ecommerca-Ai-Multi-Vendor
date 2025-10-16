@extends('layouts.admin')

@section('title', __('Product Attributes Management'))

@section('content')
<!-- Page Header -->
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Product Attributes Management') }}</h1>
        <p class="page-description mb-0 text-muted">{{ __('Define selectable product characteristics') }}</p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-secondary" data-action="export-attributes">
            <i class="fas fa-download"></i>
            <span class="d-none d-sm-inline ms-1">{{ __('Export') }}</span>
        </button>
        <a href="{{ route('admin.product-attributes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span class="d-none d-sm-inline ms-1">{{ __('Add Attribute') }}</span>
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $attributes->count() }}</div>
                    <div class="stats-label">{{ __('Total Attributes') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $attributes->where('active', true)->count() }}</div>
                    <div class="stats-label">{{ __('Active Attributes') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card stats-card-warning h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $attributes->where('type', 'select')->count() }}</div>
                    <div class="stats-label">{{ __('Select Attributes') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-list"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $attributes->where('type', 'color')->count() }}</div>
                    <div class="stats-label">{{ __('Color Attributes') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-palette"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>
            {{ __('Filter & Search') }}
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.product-attributes.index') }}">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search attributes...') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="select" @selected(request('type') === 'select')>{{ __('Select') }}</option>
                        <option value="color" @selected(request('type') === 'color')>{{ __('Color') }}</option>
                        <option value="text" @selected(request('type') === 'text')>{{ __('Text') }}</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('Active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search"></i>
                            <span class="d-none d-sm-inline ms-1">{{ __('Filter') }}</span>
                        </button>
                        <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="card modern-card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <div>
            <h5 class="card-title mb-0">{{ __('Attributes List') }}</h5>
            <small class="text-muted">{{ __('Browse and manage your product attributes') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 small">{{ __('Per Page') }}:</label>
            <select class="form-select form-select-sm js-per-page-select" data-url-prefix="{{ route('admin.product-attributes.index') }}?per_page=" data-url-suffix="" >
                <option value="12" @selected(request('per_page', 12) == 12)>12</option>
                <option value="24" @selected(request('per_page') == 24)>24</option>
                <option value="48" @selected(request('per_page') == 48)>48</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        @if($attributes->count() > 0)
            <div class="row g-3">
                @foreach($attributes as $attr)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="attribute-card card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="attribute-name card-title mb-0">{{ $attr->name }}</h6>
                                    @if($attr->active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                                
                                <div class="attribute-details mb-3 flex-grow-1">
                                    <div class="small text-muted mb-1">
                                        <strong>{{ __('Slug') }}:</strong> {{ $attr->slug }}
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <strong>{{ __('Type') }}:</strong> 
                                        <span class="badge bg-light text-dark">{{ ucfirst($attr->type ?? 'select') }}</span>
                                    </div>
                                    @if($attr->values && $attr->values->count() > 0)
                                        <div class="small text-muted">
                                            <strong>{{ __('Values') }}:</strong> {{ $attr->values->count() }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="attribute-actions mt-auto">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.product-attributes.edit', $attr) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-sm-inline ms-1">{{ __('Edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.product-attributes.destroy',$attr) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this attribute?') }}">@csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($attributes instanceof \Illuminate\Pagination\LengthAwarePaginator && $attributes->hasPages())
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-4 gap-3">
                    <div class="pagination-info text-muted small">
                        {{ __('Showing') }} {{ $attributes->firstItem() }} {{ __('to') }} {{ $attributes->lastItem() }} {{ __('of') }} {{ $attributes->total() }} {{ __('results') }}
                    </div>
                    <div class="pagination-links">
                        {{ $attributes->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-3">
                    <i class="fas fa-tags fa-3x text-muted"></i>
                </div>
                <h4 class="empty-title">{{ __('No attributes found') }}</h4>
                <p class="empty-description text-muted mb-4">{{ __('Start by creating your first product attribute to define selectable characteristics.') }}</p>
                <a href="{{ route('admin.product-attributes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    {{ __('Add First Attribute') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Inline deletion script removed; handled via delegated js-confirm listener --}}
@endpush
