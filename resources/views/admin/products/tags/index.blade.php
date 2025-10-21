@extends('layouts.admin')

@section('title', __('Product Tags'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.59 13.41L13 20.99C12.7 21.3 12.3 21.3 12 20.99L3.41 12.42C3.07 12.08 3.07 11.52 3.41 11.18L11 3.59C11.3 3.29 11.7 3.29 12 3.59L20.59 12.18C20.93 12.52 20.93 13.08 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Product Tags') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Organize and filter products with tags') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button class="admin-btn admin-btn-secondary" title="{{ __('Export Tags') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 19.6642C20.0391 19.2893 19.5304 19 19 19H5C4.46957 19 3.96086 19.2893 3.58579 19.6642C3.21071 20.0391 3 20.5304 3 21V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Export Tags') }}
                </button>
                <a href="{{ route('admin.product-tags.create') }}" class="admin-btn admin-btn-primary" title="{{ __('Add Tag') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Add Tag') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.59 13.41L13 20.99C12.7 21.3 12.3 21.3 12 20.99L3.41 12.42C3.07 12.08 3.07 11.52 3.41 11.18L11 3.59C11.3 3.29 11.7 3.29 12 3.59L20.59 12.18C20.93 12.52 20.93 13.08 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
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
                    <div class="admin-stat-value" data-countup="{{ $tags->total() }}">{{ $tags->total() }}</div>
                    <div class="admin-stat-label">{{ __('Total Tags') }}</div>
                    <div class="admin-stat-description">{{ __('All created tags') }}</div>
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
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
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
                    <div class="admin-stat-value" data-countup="{{ $tags->where('products_count', '>', 0)->count() }}">{{ $tags->where('products_count', '>', 0)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Used Tags') }}</div>
                    <div class="admin-stat-description">{{ __('Tags with products') }}</div>
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
                            <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $tags->where('products_count', 0)->count() }}">{{ $tags->where('products_count', 0)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Unused Tags') }}</div>
                    <div class="admin-stat-description">{{ __('Tags without products') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 12H16M12 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Needs attention') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $tags->where('created_at', '>=', now()->subDays(30))->count() }}">{{ $tags->where('created_at', '>=', now()->subDays(30))->count() }}</div>
                    <div class="admin-stat-label">{{ __('New This Month') }}</div>
                    <div class="admin-stat-description">{{ __('Recently created') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Recent activity') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Filters') }}
                </h3>
            </div>
            <form method="GET" action="{{ route('admin.product-tags.index') }}" class="admin-card-body">
                <div class="admin-filter-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Search Tags') }}</label>
                        <input type="text" class="admin-form-input" name="search"
                            value="{{ request('search') }}" placeholder="{{ __('Search by name or slug...') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Usage Status') }}</label>
                        <select class="admin-form-input" name="usage">
                            <option value="">{{ __('All Tags') }}</option>
                            <option value="used" {{ request('usage') == 'used' ? 'selected' : '' }}>{{ __('Used Tags') }}</option>
                            <option value="unused" {{ request('usage') == 'unused' ? 'selected' : '' }}>{{ __('Unused Tags') }}</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Per Page') }}</label>
                        <select class="admin-form-input" name="per_page">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    <div class="admin-filter-actions">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.product-tags.index') }}" class="admin-btn admin-btn-secondary" title="{{ __('Clear') }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tags List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.59 13.41L13 20.99C12.7 21.3 12.3 21.3 12 20.99L3.41 12.42C3.07 12.08 3.07 11.52 3.41 11.18L11 3.59C11.3 3.29 11.7 3.29 12 3.59L20.59 12.18C20.93 12.52 20.93 13.08 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ __('All Tags') }}
                </h3>
                <div class="admin-badge-count">{{ $tags->count() }} {{ __('tags') }}</div>
            </div>
            <div class="admin-card-body">
                @if($tags->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th class="text-center">{{ __('Products Count') }}</th>
                                <th class="text-center" width="150">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tags as $tag)
                            <tr>
                                <td>
                                    <div class="admin-item-name">{{ $tag->name }}</div>
                                </td>
                                <td>
                                    <div class="admin-text-muted">{{ $tag->slug }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="admin-badge {{ $tag->products_count > 0 ? 'admin-badge-success' : 'admin-badge-secondary' }}">
                                        {{ $tag->products_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="admin-actions-flex">
                                        <a href="{{ route('admin.product-tags.edit', $tag) }}"
                                            class="admin-btn admin-btn-small admin-btn-secondary" title="{{ __('Edit') }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M18.5 2.5C18.8978 2.10218 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10218 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            {{ __('Edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('admin.product-tags.destroy',$tag) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this tag?') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-small admin-btn-danger" title="{{ __('Delete') }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" />
                                                    <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" />
                                                    <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2" />
                                                    <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
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
                            <path d="M20.59 13.41L13 20.99C12.7 21.3 12.3 21.3 12 20.99L3.41 12.42C3.07 12.08 3.07 11.52 3.41 11.18L11 3.59C11.3 3.29 11.7 3.29 12 3.59L20.59 12.18C20.93 12.52 20.93 13.08 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <h3>{{ __('No tags found') }}</h3>
                    <p>{{ __('Start by creating your first tag') }}</p>
                    <a href="{{ route('admin.product-tags.create') }}" class="admin-btn admin-btn-primary" title="{{ __('Add Tag') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Add Tag') }}
                    </a>
                </div>
                @endif
            </div>
            @if($tags->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $tags->firstItem() }} {{ __('to') }} {{ $tags->lastItem() }}
                    {{ __('of') }} {{ $tags->total() }} {{ __('results') }}
                </div>
                <div class="pagination-links">
                    {{ $tags->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection