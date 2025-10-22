@extends('layouts.admin')
@section('title', __('Brands'))
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Brands') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage product brands and manufacturers') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.brands.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Create Brand') }}
                </a>
            </div>
        </div>

        <!-- Brands Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-tag"></i>
                    {{ __('Brands List') }}
                </h2>
                <div class="admin-badge-count">{{ $brands->count() }} {{ __('brands') }}</div>
            </div>
            <div class="admin-card-body">
                @if($brands->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Active') }}</th>
                                <th width="150">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $brand)
                            <tr>
                                <td>
                                    <span class="admin-badge">{{ $brand->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="admin-item-placeholder admin-item-placeholder-primary me-3">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div class="fw-bold">{{ $brand->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($brand->active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this brand?') }}">
                                            @csrf
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
                @else
                <div class="admin-empty-state">
                    <i class="fas fa-tag" style="font-size: 48px;"></i>
                    <h3>{{ __('No Brands Found') }}</h3>
                    <p>{{ __('Get started by creating your first brand') }}</p>
                    <a href="{{ route('admin.brands.create') }}" class="admin-btn admin-btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('Create First Brand') }}
                    </a>
                </div>
                @endif
            </div>
            @if($brands->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $brands->firstItem() }} {{ __('to') }} {{ $brands->lastItem() }} {{ __('of') }} {{ $brands->total() }} {{ __('results') }}
                </div>
                <div class="pagination-links">
                    {{ $brands->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection