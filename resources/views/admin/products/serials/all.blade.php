@extends('layouts.admin')
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
                        <h1 class="admin-order-title">{{ __('All Product Serials') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage all product serials and track sales') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Serials Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ __('All Product Serials') }}
                </h3>
                <div class="admin-badge-count">{{ $serials->count() }} {{ __('serials') }}</div>
            </div>
            <div class="admin-card-body">
                @if($serials->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Serial') }}</th>
                                <th>{{ __('Sold At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serials as $s)
                            <tr>
                                <td>
                                    <span class="admin-badge">{{ $s->id }}</span>
                                </td>
                                <td>
                                    <div class="admin-item-name">
                                        <a href="{{ route('admin.products.edit', $s->product) }}">{{ $s->product->name }}</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-item-name">{{ $s->serial }}</div>
                                </td>
                                <td>
                                    @if($s->sold_at)
                                    <div class="admin-stock-value">{{ $s->sold_at }}</div>
                                    @else
                                    <div class="admin-text-muted">{{ __('Available') }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if(!$s->sold_at)
                                    <form method="post" action="{{ route('admin.products.serials.markSold',[$s->product,$s]) }}" class="d-inline">
                                        @csrf
                                        <button class="admin-btn admin-btn-small admin-btn-success">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                                            </svg>
                                            {{ __('Mark Sold') }}
                                        </button>
                                    </form>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-completed">{{ __('Sold') }}</span>
                                    @endif
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
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <h3>{{ __('No serials found') }}</h3>
                    <p>{{ __('No product serials available.') }}</p>
                </div>
                @endif
            </div>
            @if($serials->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ $serials->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection