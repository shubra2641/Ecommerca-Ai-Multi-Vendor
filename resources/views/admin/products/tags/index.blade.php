@extends('layouts.admin')

@section('title', __('Product Tags'))

@section('content')
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-8">
        <h1 class="page-title mb-1">{{ __('Product Tags') }}</h1>
        <p class="text-muted mb-0">{{ __('Organize and filter products with tags') }}</p>
    </div>
    <div class="col-12 col-md-4 mt-3 mt-md-0">
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
            <button class="btn btn-outline-secondary" title="{{ __('Export Tags') }}">
                <i class="fas fa-download me-1"></i>
                <span class="d-none d-sm-inline">{{ __('Export Tags') }}</span>
                <span class="d-sm-none">{{ __('Export') }}</span>
            </button>
            <a href="{{ route('admin.product-tags.create') }}" class="btn btn-primary" title="{{ __('Add Tag') }}">
                <i class="fas fa-plus me-1"></i>
                <span class="d-none d-sm-inline">{{ __('Add Tag') }}</span>
                <span class="d-sm-none">{{ __('Add') }}</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-6 col-lg-3 mb-3">
    <div class="stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $tags->total() }}</div>
                    <div class="stats-label">{{ __('Total Tags') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
    <div class="stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $tags->where('products_count', '>', 0)->count() }}</div>
                    <div class="stats-label">{{ __('Used Tags') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-box"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
    <div class="stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $tags->where('products_count', 0)->count() }}</div>
                    <div class="stats-label">{{ __('Unused Tags') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
            <div class="stats-card stats-card-warning h-100">
                <div class="card modern-card stats-card h-100">
                    <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number">{{ $tags->where('created_at', '>=', now()->subDays(30))->count() }}</div>
                    <div class="stats-label">{{ __('New This Month') }}</div>
                </div>
                <div class="stats-icon"><i class="fas fa-calendar-plus"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.product-tags.index') }}">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label">{{ __('Search Tags') }}</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="{{ __('Search by name or slug...') }}">
                </div>
                <div class="col-12 col-md-3">
                    <label for="usage" class="form-label">{{ __('Usage Status') }}</label>
                    <select class="form-select" id="usage" name="usage">
                        <option value="">{{ __('All Tags') }}</option>
                        <option value="used" {{ request('usage') == 'used' ? 'selected' : '' }}>{{ __('Used Tags') }}</option>
                        <option value="unused" {{ request('usage') == 'unused' ? 'selected' : '' }}>{{ __('Unused Tags') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="per_page" class="form-label">{{ __('Per Page') }}</label>
                    <select class="form-select" id="per_page" name="per_page">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>{{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.product-tags.index') }}" class="btn btn-outline-secondary" title="{{ __('Clear') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tags List -->
<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title mb-0">{{ __('All Tags') }}</h3>
    </div>
    <div class="card-body">
        @if($tags->count() > 0)
            <!-- Desktop Table View -->
            <div class="d-none d-md-block">
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
                                <td class="fw-semibold">{{ $tag->name }}</td>
                                <td class="text-muted small">{{ $tag->slug }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $tag->products_count > 0 ? 'success' : 'secondary' }}">
                                        {{ $tag->products_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.product-tags.edit', $tag) }}" 
                                           class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.product-tags.destroy',$tag) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this tag?') }}">@csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                <div class="row">
                    @foreach($tags as $tag)
                    <div class="col-12 mb-3">
                        <div class="card border">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $tag->name }}</h6>
                                        <p class="text-muted small mb-1">{{ $tag->slug }}</p>
                                        <span class="badge bg-{{ ($tag->products_count ?? 0) > 0 ? 'success' : 'secondary' }} small">
                                            {{ $tag->products_count ?? 0 }} {{ __('Products') }}
                                        </span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Actions') }}">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.product-tags.edit', $tag) }}">
                                                    <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.product-tags.destroy',$tag) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this tag?') }}">@csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>{{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($tags->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                    <div class="pagination-info text-muted small">
                        {{ __('Showing') }} {{ $tags->firstItem() }} {{ __('to') }} {{ $tags->lastItem() }} 
                        {{ __('of') }} {{ $tags->total() }} {{ __('results') }}
                    </div>
                    <div class="pagination-wrapper">
                        {{ $tags->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-tags fa-3x mb-3"></i>
                <div class="h5">{{ __('No tags found') }}</div>
                <p class="mb-3">{{ __('Start by creating your first tag') }}</p>
                <a href="{{ route('admin.product-tags.create') }}" class="btn btn-primary" title="{{ __('Add Tag') }}">
                    <i class="fas fa-plus me-1"></i>{{ __('Add Tag') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Inline script removed in favor of delegated handlers --}}
@endpush
