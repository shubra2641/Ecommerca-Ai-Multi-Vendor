@extends('layouts.admin')

@section('title', __('Product Attributes Management'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Product Attributes Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Define selectable product characteristics') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-attributes.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Attribute') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $attributes->count() }}">{{ $attributes->count() }}</div>
                    <div class="admin-stat-label">{{ __('Total Attributes') }}</div>
                    <div class="admin-stat-description">{{ __('All attributes in system') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Growing') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $attributes->where('active', true)->count() }}">{{ $attributes->where('active', true)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Active Attributes') }}</div>
                    <div class="admin-stat-description">{{ __('Currently available') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Active') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $attributes->where('type', 'select')->count() }}">{{ $attributes->where('type', 'select')->count() }}</div>
                    <div class="admin-stat-label">{{ __('Select Attributes') }}</div>
                    <div class="admin-stat-description">{{ __('Dropdown options') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Select') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $attributes->where('type', 'color')->count() }}">{{ $attributes->where('type', 'color')->count() }}</div>
                    <div class="admin-stat-label">{{ __('Color Attributes') }}</div>
                    <div class="admin-stat-description">{{ __('Color pickers') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Colors') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-search"></i>
                    {{ __('Filter & Search') }}
                </h3>
                <p class="admin-card-subtitle">{{ __('Search and filter attributes') }}</p>
            </div>
            <div class="admin-card-body">
                <form method="GET" action="{{ route('admin.product-attributes.index') }}" class="admin-filter-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Search') }}</label>
                        <div class="admin-input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="admin-form-input admin-form-input-search" placeholder="{{ __('Search attributes...') }}">
                            <div class="admin-input-icon">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Type') }}</label>
                        <select name="type" class="admin-form-input">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="select" @selected(request('type')==='select' )>{{ __('Select') }}</option>
                            <option value="color" @selected(request('type')==='color' )>{{ __('Color') }}</option>
                            <option value="text" @selected(request('type')==='text' )>{{ __('Text') }}</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Status') }}</label>
                        <select name="status" class="admin-form-input">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="active" @selected(request('status')==='active' )>{{ __('Active') }}</option>
                            <option value="inactive" @selected(request('status')==='inactive' )>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="admin-filter-actions">
                        <div class="admin-filter-buttons">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-filter">
                                <i class="fas fa-search"></i>
                                {{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.product-attributes.index') }}" class="admin-btn admin-btn-outline admin-btn-clear">
                                <i class="fas fa-times"></i>
                                {{ __('Clear') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-modern-card">
            <div class="admin-card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div>
                    <h3 class="admin-card-title">{{ __('Attributes List') }}</h3>
                    <p class="admin-card-subtitle">{{ __('Browse and manage your product attributes') }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="admin-form-label mb-0 small">{{ __('Per Page') }}:</label>
                    <select class="admin-form-input admin-form-input-sm js-per-page-select" data-url-prefix="{{ route('admin.product-attributes.index') }}?per_page=" data-url-suffix="">
                        <option value="12" @selected(request('per_page', 12)==12)>12</option>
                        <option value="24" @selected(request('per_page')==24)>24</option>
                        <option value="48" @selected(request('per_page')==48)>48</option>
                    </select>
                </div>
            </div>
            <div class="admin-card-body">
                @if($attributes->count() > 0)
                <div class="row g-3">
                    @foreach($attributes as $attr)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="admin-modern-card h-100">
                            <div class="admin-card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="admin-card-title mb-0">{{ $attr->name }}</h6>
                                    @if($attr->active)
                                    <span class="admin-badge admin-badge-success">{{ __('Active') }}</span>
                                    @else
                                    <span class="admin-badge admin-badge-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </div>

                                <div class="attribute-details mb-3 flex-grow-1">
                                    <div class="admin-text-muted small mb-2">
                                        <strong>{{ __('Slug') }}:</strong> {{ $attr->slug }}
                                    </div>
                                    <div class="admin-text-muted small mb-2">
                                        <strong>{{ __('Type') }}:</strong>
                                        <span class="admin-badge admin-badge-info">{{ ucfirst($attr->type ?? 'select') }}</span>
                                    </div>
                                    @if($attr->values && $attr->values->count() > 0)
                                    <div class="admin-text-muted small">
                                        <strong>{{ __('Values') }}:</strong> {{ $attr->values->count() }}
                                    </div>
                                    @endif
                                </div>

                                <div class="attribute-actions mt-auto">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.product-attributes.edit', $attr) }}" class="admin-btn admin-btn-outline flex-fill">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-sm-inline ms-1">{{ __('Edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.product-attributes.destroy',$attr) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this attribute?') }}">@csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-danger">
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
                <div class="admin-card-footer">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                        <div class="admin-text-muted small">
                            {{ __('Showing') }} {{ $attributes->firstItem() }} {{ __('to') }} {{ $attributes->lastItem() }} {{ __('of') }} {{ $attributes->total() }} {{ __('results') }}
                        </div>
                        <div>
                            {{ $attributes->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
                @else
                <div class="admin-empty-state text-center py-5">
                    <div class="admin-notification-icon mb-3">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h5>{{ __('No attributes found') }}</h5>
                    <p class="admin-text-muted mb-4">{{ __('Start by creating your first product attribute to define selectable characteristics.') }}</p>
                    <a href="{{ route('admin.product-attributes.create') }}" class="admin-btn admin-btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('Add First Attribute') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection