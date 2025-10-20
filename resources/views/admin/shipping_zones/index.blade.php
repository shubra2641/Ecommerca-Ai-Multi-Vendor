@extends('layouts.admin')

@section('title', __('Shipping Zones'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Shipping Zones') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage shipping zones and their rules') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.shipping-zones.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Create Zone') }}
                </a>
            </div>
        </div>

        <!-- Zones List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="admin-card-title">{{ __('All Zones') }}</h3>
                <span class="admin-badge-count">{{ $zones->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($zones->count())
                <div class="admin-items-list">
                    @foreach($zones as $zone)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-item-placeholder-cyan">
                                #{{ $zone->id }}
                            </div>
                            <div class="admin-item-details">
                                <div class="admin-item-name">{{ $zone->name }}</div>
                                <div class="admin-payment-details admin-mt-half">
                                    @if($zone->code)
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ __('Code') }}: {{ $zone->code }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    @endif
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <strong>{{ $zone->rules_count }}</strong> {{ __('rules') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <span class="admin-status-badge {{ $zone->active ? 'status-completed' : 'status-pending' }}">
                                {{ $zone->active ? __('Active') : __('Inactive') }}
                            </span>
                            <div class="admin-actions-flex">
                                <a href="{{ route('admin.shipping-zones.edit',$zone) }}" class="admin-btn-small admin-btn-primary">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ __('Edit') }}
                                </a>
                                <form method="POST" action="{{ route('admin.shipping-zones.destroy',$zone) }}" class="js-confirm-delete" data-confirm="{{ __('Delete?') }}">
                                    @csrf @method('DELETE')
                                    <button class="admin-btn-small admin-btn-danger">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                        <path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>{{ __('No shipping zones found') }}</p>
                </div>
                @endif
            </div>
            @if($zones->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $zones->firstItem() }} - {{ $zones->lastItem() }} {{ __('of') }} {{ $zones->total() }}
                </div>
                <div class="pagination-links">
                    {{ $zones->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>

@section('scripts')
<script src="{{ asset('admin/js/confirm-delete.js') }}"></script>
@endsection
@endsection
