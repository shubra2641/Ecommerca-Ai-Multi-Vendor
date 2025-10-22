@extends('layouts.admin')

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-cog"></i>
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
                    <i class="fas fa-filter"></i>
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
                    <i class="fas fa-cog"></i>
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
                                                <i class="fas fa-check"></i>
                                                {{ __('pending_products_approve') }}
                                            </button>
                                        </form>
                                        <button class="admin-btn admin-btn-small admin-btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}">
                                            <i class="fas fa-times"></i>
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
                        <i class="fas fa-cog"></i>
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
                        <i class="fas fa-exclamation-triangle"></i>
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