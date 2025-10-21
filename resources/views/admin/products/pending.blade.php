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
                        <h1 class="admin-order-title">{{ __('pending_products_heading') }}
                            @isset($totalFiltered)
                            <span class="admin-badge admin-badge-primary ms-2">{{ $totalFiltered }} / {{ $totalOverall }}</span>
                            @endisset
                        </h1>
                        <p class="admin-order-subtitle">{{ __('pending_products_subtitle') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Filters') }}
                </h3>
            </div>
            <form method="get" class="admin-card-body">
                <div class="admin-form-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('pending_products_filter_vendor') }}</label>
                        <select name="vendor_id" class="admin-form-input" data-placeholder="{{ __('pending_products_filter_vendor') }}">
                            <option value="">-- {{ __('pending_products_filter_vendor') }} --</option>
                            @foreach($vendors ?? [] as $v)
                            <option value="{{ $v->id }}" @selected((string)$v->id === (string)($selectedVendorId ?? ''))>{{ $v->name }} (#{{ $v->id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('pending_products_filter_search') }}</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="admin-form-input" placeholder="{{ __('pending_products_filter_search_ph') }}">
                    </div>
                    <div class="admin-form-group">
                        <div class="admin-flex-end">
                            <button class="admin-btn admin-btn-primary">{{ __('pending_products_filter_apply') }}</button>
                            <a href="{{ route('admin.products.pending') }}" class="admin-btn admin-btn-secondary">{{ __('pending_products_filter_reset') }}</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ __('Pending Products') }}
                </h3>
                <div class="admin-badge-count">{{ $products->count() }} {{ __('products') }}</div>
            </div>
            <div class="admin-card-body">
                @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('pending_products_id') }}</th>
                                <th>{{ __('pending_products_vendor') }}</th>
                                <th>{{ __('pending_products_name') }}</th>
                                <th>{{ __('pending_products_created_at') }}</th>
                                <th>{{ __('pending_products_approved_at') }}</th>
                                <th>{{ __('pending_products_rejection_reason') }}</th>
                                <th>{{ __('pending_products_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $p)
                            <tr @class(['table-warning'=> (bool)$p->rejection_reason])>
                                <td>
                                    <span class="admin-badge">{{ $p->id }}</span>
                                </td>
                                <td>
                                    <div class="admin-item-name">{{ $p->vendor?->name }}</div>
                                    <div class="admin-text-muted">#{{ $p->vendor_id }}</div>
                                </td>
                                <td>
                                    <div class="admin-item-name">{{ $p->name }}</div>
                                </td>
                                <td>
                                    <div class="admin-stock-value">{{ $p->created_at }}</div>
                                </td>
                                <td>
                                    <div class="admin-stock-value">{{ optional($p->approved_at)->format('Y-m-d H:i') }}</div>
                                </td>
                                <td>
                                    <div class="admin-text-muted max-w-260 ws-pre-line">{{ $p->rejection_reason }}</div>
                                </td>
                                <td>
                                    <div class="admin-actions-flex">
                                        <form method="post" action="{{ route('admin.products.approve', $p->id) }}" class="d-inline">
                                            @csrf
                                            <button class="admin-btn admin-btn-small admin-btn-success">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                                {{ __('pending_products_approve') }}
                                            </button>
                                        </form>
                                        <button class="admin-btn admin-btn-small admin-btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                                <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" />
                                                <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" />
                                            </svg>
                                            {{ __('pending_products_reject_delete') }}
                                        </button>
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
                    <h3>{{ __('No pending products') }}</h3>
                    <p>{{ __('All products have been reviewed.') }}</p>
                </div>
                @endif
            </div>
            @if($products->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ $products->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Reject Modals -->
@foreach($products as $p)
<div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('admin.products.reject', $p->id) }}" class="reject-delete-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('pending_products_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('pending_products_action_label') }}</label>
                        <select name="mode" class="admin-form-input action-mode" data-target="rejectFields{{ $p->id }}">
                            <option value="reject">{{ __('pending_products_action_reject_keep') }}</option>
                            <option value="delete">{{ __('pending_products_action_delete') }}</option>
                        </select>
                    </div>
                    <div id="rejectFields{{ $p->id }}" class="reject-fields">
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('pending_products_reason_label') }} <span class="text-danger">*</span></label>
                            <textarea name="reason" class="admin-form-input" rows="3" placeholder="{{ __('pending_products_reason_placeholder') }}" required></textarea>
                            <div class="admin-text-muted">{{ __('pending_products_reason_help') }}</div>
                        </div>
                    </div>
                    <div class="alert alert-warning d-none mt-3 delete-warning">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.29 3.86L1.82 18C1.64547 18.3024 1.5729 18.6453 1.61211 18.9873C1.65132 19.3294 1.80051 19.6507 2.03512 19.8995C2.26973 20.1483 2.57796 20.3127 2.91336 20.3683C3.24875 20.4239 3.5947 20.3683 3.9 20.21L12 16.77L20.1 20.21C20.4053 20.3683 20.7512 20.4239 21.0866 20.3683C21.422 20.3127 21.7303 20.1483 21.9649 19.8995C22.1995 19.6507 22.3487 19.3294 22.3879 18.9873C22.4271 18.6453 22.3545 18.3024 22.18 18L13.71 3.86C13.5318 3.56631 13.2807 3.32311 12.9812 3.15447C12.6817 2.98584 12.3438 2.89725 12 2.89725C11.6562 2.89725 11.3183 2.98584 11.0188 3.15447C10.7193 3.32311 10.4682 3.56631 10.29 3.86Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 9V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 17H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('pending_products_delete_warning') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="admin-btn admin-btn-secondary" data-bs-dismiss="modal">{{ __('pending_products_cancel') }}</button>
                    <button class="admin-btn admin-btn-danger submit-action">{{ __('pending_products_submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection