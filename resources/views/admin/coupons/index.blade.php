@extends('layouts.admin')
@section('title', __('Coupons'))
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Coupons') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage discount coupons and promotions') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.coupons.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Create Coupon') }}
                </a>
            </div>
        </div>

        <!-- Coupons Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-ticket-alt"></i>
                    {{ __('Coupons List') }}
                </h2>
                <div class="admin-badge-count">{{ $coupons->count() }} {{ __('coupons') }}</div>
            </div>
            <div class="admin-card-body">
                @if($coupons->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Value') }}</th>
                                <th>{{ __('Uses') }}</th>
                                <th>{{ __('Expires') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th width="150">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupons as $coupon)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="admin-item-placeholder admin-item-placeholder-warning me-3">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div class="fw-bold">{{ $coupon->code }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($coupon->type) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">
                                        {{ $coupon->value }}{{ $coupon->type === 'percent' ? '%' : '' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-text-muted">{{ $coupon->uses ?? 0 }} / {{ $coupon->max_uses ?? '∞' }}</span>
                                </td>
                                <td>
                                    @if($coupon->ends_at)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar me-1"></i>
                                        <span class="admin-text-muted">{{ $coupon->ends_at->format('Y-m-d') }}</span>
                                    </div>
                                    @else
                                    <span class="admin-text-muted">{{ __('Never') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this coupon?') }}">
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
                    <i class="fas fa-ticket-alt" style="font-size: 48px;"></i>
                    <h3>{{ __('No Coupons Found') }}</h3>
                    <p>{{ __('Get started by creating your first discount coupon') }}</p>
                    <a href="{{ route('admin.coupons.create') }}" class="admin-btn admin-btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('Create First Coupon') }}
                    </a>
                </div>
                @endif
            </div>
            @if($coupons->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $coupons->firstItem() }} {{ __('to') }} {{ $coupons->lastItem() }} {{ __('of') }} {{ $coupons->total() }} {{ __('results') }}
                </div>
                <div class="pagination-links">
                    {{ $coupons->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection