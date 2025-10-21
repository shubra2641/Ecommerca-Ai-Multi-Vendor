@extends('layouts.admin')
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Review') }} #{{ $review->id }}</h1>
                        <p class="admin-order-subtitle">{{ __('Review details and moderation') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Back to Reviews') }}
                </a>
            </div>
        </div>

        <!-- Review Details -->
        <div class="admin-order-grid-modern">
            <!-- Review Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                        {{ __('Review Information') }}
                    </h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                                    <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                                </svg>
                                {{ __('Product') }}
                            </div>
                            <div class="admin-info-value">{{ $review->product?->name }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                                </svg>
                                {{ __('User') }}
                            </div>
                            <div class="admin-info-value">
                                {{ $review->user?->email ?? __('Guest') }}
                                @if($review->user?->name)
                                <div class="admin-text-muted">{{ $review->user?->name }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Rating') }}
                            </div>
                            <div class="admin-info-value">
                                <span class="admin-badge admin-badge-primary">{{ $review->rating }}/5</span>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Comment') }}
                            </div>
                            <div class="admin-info-value">
                                <div class="admin-text-muted p-3 bg-light rounded">{{ $review->comment }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Images -->
            @if($review->images && count($review->images) > 0)
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                            <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Review Images') }}
                    </h3>
                </div>
                <div class="admin-card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($review->images as $img)
                        <img src="{{ asset($img) }}" class="rounded obj-cover" style="width: 120px; height: 120px;" />
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                        <path d="M12 1V3M12 21V23M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M1 12H3M21 12H23M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Review Actions') }}
                </h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-actions-flex">
                    @if(!$review->approved)
                    <form method="post" action="{{ route('admin.reviews.approve',$review) }}" class="d-inline">
                        @csrf
                        <button class="admin-btn admin-btn-success">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                            </svg>
                            {{ __('Approve') }}
                        </button>
                    </form>
                    @else
                    <form method="post" action="{{ route('admin.reviews.unapprove',$review) }}" class="d-inline">
                        @csrf
                        <button class="admin-btn admin-btn-warning">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" />
                            </svg>
                            {{ __('Unapprove') }}
                        </button>
                    </form>
                    @endif
                    <form method="post" action="{{ route('admin.reviews.destroy',$review) }}" class="d-inline js-confirm" data-confirm="{{ __('Delete?') }}">
                        @csrf @method('delete')
                        <button class="admin-btn admin-btn-danger">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" />
                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" />
                                <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2" />
                                <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2" />
                            </svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection