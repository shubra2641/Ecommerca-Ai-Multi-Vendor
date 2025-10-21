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
                        <h1 class="admin-order-title">{{ __('Product Reviews') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage and moderate product reviews') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ __('Reviews List') }}
                </h3>
                <div class="admin-badge-count">{{ $reviews->count() }} {{ __('reviews') }}</div>
            </div>
            <div class="admin-card-body">
                @if($reviews->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Rating') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Images') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Approved') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $r)
                            <tr>
                                <td>
                                    <span class="admin-badge">{{ $r->id }}</span>
                                </td>
                                <td>
                                    <div class="admin-item-name">{{ $r->product?->name }}</div>
                                </td>
                                <td>
                                    <div class="admin-item-name">{{ $r->user?->email ?? __('Guest') }}</div>
                                </td>
                                <td>
                                    <div class="admin-stock-value">{{ $r->rating }}/5</div>
                                </td>
                                <td>
                                    <div class="admin-item-name">{{ Str::limit($r->title,40) }}</div>
                                </td>
                                <td>
                                    @if($r->images && count($r->images)>0)
                                    <div class="d-flex gap-1">
                                        @foreach($r->images as $img)
                                        <img src="{{ asset($img) }}" class="rounded obj-cover" style="width: 48px; height: 48px;" />
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="admin-text-muted">{{ __('No images') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-stock-value">{{ $r->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    @if($r->approved)
                                    <span class="admin-status-badge admin-status-badge-completed">{{ __('Yes') }}</span>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-warning">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-actions-flex">
                                        <a href="{{ route('admin.reviews.show',$r) }}" class="admin-btn admin-btn-small admin-btn-secondary">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" />
                                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                                            </svg>
                                            {{ __('View') }}
                                        </a>
                                        @if($r->approved)
                                        <form method="post" action="{{ route('admin.reviews.unapprove',$r) }}" class="d-inline">
                                            @csrf
                                            <button class="admin-btn admin-btn-small admin-btn-warning">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                                    <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" />
                                                    <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                                {{ __('Unapprove') }}
                                            </button>
                                        </form>
                                        @else
                                        <form method="post" action="{{ route('admin.reviews.approve',$r) }}" class="d-inline">
                                            @csrf
                                            <button class="admin-btn admin-btn-small admin-btn-success">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                                {{ __('Approve') }}
                                            </button>
                                        </form>
                                        @endif
                                        <form method="post" action="{{ route('admin.reviews.destroy',$r) }}" class="d-inline js-confirm" data-confirm="{{ __('Delete?') }}">
                                            @csrf @method('delete')
                                            <button class="admin-btn admin-btn-small admin-btn-danger">
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
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <h3>{{ __('No reviews yet') }}</h3>
                    <p>{{ __('Product reviews will appear here when customers submit them.') }}</p>
                </div>
                @endif
            </div>
            @if($reviews->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ $reviews->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection