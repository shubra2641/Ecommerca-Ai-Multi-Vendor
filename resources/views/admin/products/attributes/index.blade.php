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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Product Attributes Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Define selectable product characteristics') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary" data-action="export-attributes">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 19.6642C20.0391 19.2893 19.5304 19 19 19H5C4.46957 19 3.96086 19.2893 3.58579 19.6642C3.21071 20.0391 3 20.5304 3 21V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Export') }}
                </button>
                <a href="{{ route('admin.product-attributes.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Add Attribute') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.59 13.41L13.42 20.58C12.05 21.95 10.1 21.95 8.73 20.58L3.42 15.27C2.05 13.9 2.05 11.95 3.42 10.58L10.59 3.41C11.95 2.05 13.9 2.05 15.27 3.41L20.58 8.72C21.95 10.09 21.95 12.04 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7 13L9 15L13 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $attributes->count() }}">{{ $attributes->count() }}</div>
                    <div class="admin-stat-label">{{ __('Total Attributes') }}</div>
                    <div class="admin-stat-description">{{ __('All attributes in system') }}</div>
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
                    <div class="admin-stat-value" data-countup="{{ $attributes->where('active', true)->count() }}">{{ $attributes->where('active', true)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Active Attributes') }}</div>
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
                            <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $attributes->where('type', 'select')->count() }}">{{ $attributes->where('type', 'select')->count() }}</div>
                    <div class="admin-stat-label">{{ __('Select Attributes') }}</div>
                    <div class="admin-stat-description">{{ __('Dropdown options') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Select') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12S6.48 22 12 22 22 17.52 22 12 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12S7.59 4 12 4 20 7.59 20 12 16.41 20 12 20Z" stroke="currentColor" stroke-width="2" />
                            <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $attributes->where('type', 'color')->count() }}">{{ $attributes->where('type', 'color')->count() }}</div>
                    <div class="admin-stat-label">{{ __('Color Attributes') }}</div>
                    <div class="admin-stat-description">{{ __('Color pickers') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Colors') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.product-attributes.index') }}" class="admin-btn admin-btn-outline admin-btn-clear">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13M18.5 2.5C18.8978 2.10218 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10218 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="d-none d-sm-inline ms-1">{{ __('Edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.product-attributes.destroy',$attr) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this attribute?') }}">@csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-danger">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
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
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.59 13.41L13.42 20.58C12.05 21.95 10.1 21.95 8.73 20.58L3.42 15.27C2.05 13.9 2.05 11.95 3.42 10.58L10.59 3.41C11.95 2.05 13.9 2.05 15.27 3.41L20.58 8.72C21.95 10.09 21.95 12.04 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7 13L9 15L13 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h5>{{ __('No attributes found') }}</h5>
                    <p class="admin-text-muted mb-4">{{ __('Start by creating your first product attribute to define selectable characteristics.') }}</p>
                    <a href="{{ route('admin.product-attributes.create') }}" class="admin-btn admin-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Add First Attribute') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection