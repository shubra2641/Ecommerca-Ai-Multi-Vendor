@extends('layouts.admin')

@section('title', __('Vendor Exports'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    {{ __('Vendor Exports') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage and download vendor export files') }}</p>
            </div>
        </div>

        <!-- Exports List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('Export Files') }}</h2>
                <span class="admin-badge-count">{{ $items->total() }}</span>
            </div>

            <div class="admin-items-list">
                @forelse($items as $it)
                <div class="admin-item-card">
                    <div class="admin-item-main">
                        <div class="admin-item-placeholder admin-item-placeholder-cyan">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="admin-item-details">
                            <h3 class="admin-item-name">{{ $it->filename }}</h3>
                            <div class="admin-payment-details admin-mt-half">
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ __('ID') }}: <strong>#{{ $it->id }}</strong>
                                </span>
                                <span class="payment-detail-separator">•</span>
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('Vendor') }}: <strong>{{ $it->vendor?->name ?? __('N/A') }}</strong>
                                </span>
                                @if($it->completed_at)
                                <span class="payment-detail-separator">•</span>
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Completed') }}: {{ $it->completed_at->format('Y-m-d H:i') }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="admin-item-meta">
                        <span class="admin-status-badge admin-status-badge-{{ $it->status === 'completed' ? 'completed' : ($it->status === 'failed' ? 'cancelled' : 'warning') }}">
                            {{ ucfirst($it->status) }}
                        </span>
                        <div class="admin-actions-flex">
                            @if($it->path)
                            <a href="{{ route('admin.vendor_exports.download', $it->id) }}" class="admin-btn admin-btn-small admin-btn-primary">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                {{ __('Download') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <p>{{ __('No export files found') }}</p>
                </div>
                @endforelse
            </div>

            @if($items->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing :from to :to of :total results', ['from' => $items->firstItem(), 'to' => $items->lastItem(), 'total' => $items->total()]) }}
                </div>
                <div class="pagination-links">
                    {{ $items->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection