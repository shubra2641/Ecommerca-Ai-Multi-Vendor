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
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Product Tags') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Organize and filter products with tags') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-tags.create') }}" class="admin-btn admin-btn-primary" title="{{ __('Add Tag') }}">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Tag') }}
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
                    <div class="admin-stat-value" data-countup data-target="{{ $tags->total() }}">{{ $tags->total() }}</div>
                    <div class="admin-stat-label">{{ __('Total Tags') }}</div>
                    <div class="admin-stat-description">{{ __('All created tags') }}</div>
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
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ $tags->where('products_count', '>', 0)->count() }}">{{ $tags->where('products_count', '>', 0)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Used Tags') }}</div>
                    <div class="admin-stat-description">{{ __('Tags with products') }}</div>
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
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ $tags->where('products_count', 0)->count() }}">{{ $tags->where('products_count', 0)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Unused Tags') }}</div>
                    <div class="admin-stat-description">{{ __('Tags without products') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('Needs attention') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ $tags->where('created_at', '>=', now()->subDays(30))->count() }}">{{ $tags->where('created_at', '>=', now()->subDays(30))->count() }}</div>
                    <div class="admin-stat-label">{{ __('New This Month') }}</div>
                    <div class="admin-stat-description">{{ __('Recently created') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Recent activity') }}</span>
                    </div>
                </div>
            </div>
        </div>



        <!-- Tags List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-tags"></i>
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
                                            <i class="fas fa-edit"></i>
                                            {{ __('Edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('admin.product-tags.destroy',$tag) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this tag?') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-small admin-btn-danger" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
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
                        <i class="fas fa-tags admin-notification-icon"></i>
                    </div>
                    <h3>{{ __('No tags found') }}</h3>
                    <p>{{ __('Start by creating your first tag') }}</p>
                    <a href="{{ route('admin.product-tags.create') }}" class="admin-btn admin-btn-primary" title="{{ __('Add Tag') }}">
                        <i class="fas fa-plus"></i>
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