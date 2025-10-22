@extends('layouts.admin')

@section('title', __('Product Categories Management'))

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
                        <h1 class="admin-order-title">{{ __('Product Categories Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage product categories and subcategories') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-categories.export') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-download"></i>
                    {{ __('Export') }}
                </a>
                <a href="{{ route('admin.product-categories.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Category') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $aciTotals['total'] }}">{{ $aciTotals['total'] }}</div>
                    <div class="admin-stat-label">{{ __('Total Categories') }}</div>
                    <div class="admin-stat-description">{{ __('All categories in system') }}</div>
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
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $aciTotals['active'] }}">{{ $aciTotals['active'] }}</div>
                    <div class="admin-stat-label">{{ __('Active Categories') }}</div>
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
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $aciTotals['parent'] }}">{{ $aciTotals['parent'] }}</div>
                    <div class="admin-stat-label">{{ __('Parent Categories') }}</div>
                    <div class="admin-stat-description">{{ __('Top level categories') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Parents') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ $aciTotals['child'] }}">{{ $aciTotals['child'] }}</div>
                    <div class="admin-stat-label">{{ __('Subcategories') }}</div>
                    <div class="admin-stat-description">{{ __('Child categories') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Children') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.product-categories.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="{{ __('Search categories...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label">{{ __('Type') }}</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="parent" {{ request('type') == 'parent' ? 'selected' : '' }}>{{ __('Parent Categories') }}</option>
                            <option value="child" {{ request('type') == 'child' ? 'selected' : '' }}>{{ __('Subcategories') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill" title="{{ __('Filter') }}">
                                <i class="fas fa-search d-md-none"></i>
                                <span class="d-none d-md-inline">{{ __('Filter') }}</span>
                            </button>
                            <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary" title="{{ __('Clear') }}">
                                <i class="fas fa-times d-md-none"></i>
                                <span class="d-none d-md-inline">{{ __('Clear') }}</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card modern-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h5 class="mb-0">{{ __('Categories List') }}</h5>
                    <small class="text-muted">{{ __('Browse and manage your product categories') }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm js-per-page-select" data-url-prefix="{{ route('admin.product-categories.index') }}?per_page=" data-url-suffix="{{ request()->except('per_page') ? '&'.http_build_query(request()->except('per_page')) : '' }}" title="{{ __('Per Page') }}">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 {{ __('per page') }}</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 {{ __('per page') }}</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 {{ __('per page') }}</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 {{ __('per page') }}</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">{{ __('Category') }}</th>
                                <th class="border-0 d-none d-md-table-cell">{{ __('Slug') }}</th>
                                <th class="border-0 d-none d-lg-table-cell">{{ __('Position') }}</th>
                                <th class="border-0 d-none d-lg-table-cell">{{ __('Commission %') }}</th>
                                <th class="border-0">{{ __('Status') }}</th>
                                <th class="border-0 d-none d-md-table-cell">{{ __('Children') }}</th>
                                <th class="border-0 text-end w-120">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $renderTree = function($nodes,$level=0) use (&$renderTree) {
                            foreach($nodes as $cat) {
                            echo '<tr data-level="'.$level.'">';
                                echo '<td>';
                                    echo '<div class="d-flex align-items-center gap-2">';
                                        if($cat->children->count()) {
                                        echo '<button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1 js-toggle-node" data-node="cat-'.$cat->id.'"><i class="fas fa-minus"></i></button>';
                                        } else {
                                        echo '<span class="btn btn-sm btn-outline-light px-2 py-1 disabled opacity-50"><i class="fas fa-circle"></i></span>';
                                        }
                                        if($cat->image) {
                                        echo '<img src="'.asset($cat->image).'" class="rounded flex-shrink-0 obj-cover w-34 h-34" alt="">';
                                        } else {
                                        echo '<span class="badge bg-secondary flex-shrink-0">'.strtoupper(substr($cat->name,0,2)).'</span>';
                                        }
                                        echo '<div class="min-w-0">';
                                            echo '<div class="fw-semibold text-truncate">'.e($cat->name).'</div>';
                                            if($level>0) { echo '<div class="small text-muted">'.__('Child of').': '.e(optional($cat->parent)->name).'</div>'; }
                                            echo '</div>
                                    </div>';
                                    echo '</td>';
                                echo '<td class="text-muted small d-none d-md-table-cell">
                                    <div class="text-truncate max-w-150">'.e($cat->slug).'</div>
                                </td>';
                                echo '<td class="d-none d-lg-table-cell">'.e($cat->position).'</td>';
                                $commission = $cat->commission_rate !== null ? number_format((float)$cat->commission_rate,2) : '<span class="text-muted small">'.__('—').'</span>';
                                echo '<td class="d-none d-lg-table-cell">'.$commission.'</td>';
                                echo '<td>'.($cat->active ? '<span class="badge bg-success">'.__('Active').'</span>' : '<span class="badge bg-danger">'.__('Inactive').'</span>').'</td>';
                                echo '<td class="d-none d-md-table-cell">'.($cat->children->count() ? '<span class="badge bg-info">'.$cat->children->count().'</span>' : '<span class="text-muted small">0</span>').'</td>';
                                echo '<td class="text-end">';
                                    echo '<div class="btn-group btn-group-sm">';
                                        echo '<a href="'.route('admin.product-categories.edit',$cat).'" class="btn btn-outline-primary" title="'.__('Edit').'"><i class="fas fa-edit"></i></a>';
                                        echo '<form method="POST" action="'.route('admin.product-categories.destroy',$cat).'" class="d-inline js-confirm" data-confirm="'.__('Are you sure you want to delete this category?').'"><input type="hidden" name="_token" value="'.csrf_token().'"><input type="hidden" name="_method" value="DELETE"><button class="btn btn-outline-danger" title="'.__('Delete').'"><i class="fas fa-trash"></i></button></form>';
                                        echo '</div>';
                                    echo '</td>';
                                echo '</tr>';
                            if($cat->children->count()) {
                            echo '<tr class="child-row" data-parent="cat-'.$cat->id.'">
                                <td colspan="7" class="p-0 border-0">';
                                    echo '<table class="table table-sm mb-0">
                                        <tbody>';
                                            $renderTree($cat->children, $level+1);
                                            echo '</tbody>
                                    </table>';
                                    echo '</td>
                            </tr>';
                            }
                            }
                            };
                            @endphp
                            @forelse($categories as $root)
                            @php($renderTree([$root]))
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-muted opacity-50"></i>
                                    <div class="h5">{{ __('No categories found') }}</div>
                                    <p class="mb-3">{{ __('Start by creating your first product category') }}</p>
                                    <a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary" title="{{ __('Add Category') }}">
                                        <i class="fas fa-plus me-1"></i>{{ __('Add Category') }}
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($categories, 'links'))
                <div class="card-footer border-0 bg-transparent">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="text-muted small">
                            {{ __('Showing') }} {{ $categories->firstItem() ?? 0 }} {{ __('to') }} {{ $categories->lastItem() ?? 0 }}
                            {{ __('of') }} {{ $categories->total() }} {{ __('results') }}
                        </div>
                        <div>
                            {{ $categories->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endsection