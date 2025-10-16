@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3>{{ __('pending_products_heading') }}
        @isset($totalFiltered)
            <span class="badge bg-primary ms-2">{{ $totalFiltered }} / {{ $totalOverall }}</span>
        @endisset
    </h3>
    <p class="text-muted">{{ __('pending_products_subtitle') }}</p>
    <form method="get" class="row g-2 align-items-end mt-2">
        <div class="col-md-3">
            <label class="form-label mb-0 small">{{ __('pending_products_filter_vendor') }}</label>
            <select name="vendor_id" class="form-select" data-placeholder="{{ __('pending_products_filter_vendor') }}">
                <option value="">-- {{ __('pending_products_filter_vendor') }} --</option>
                @foreach($vendors ?? [] as $v)
                    <option value="{{ $v->id }}" @selected((string)$v->id === (string)($selectedVendorId ?? ''))>{{ $v->name }} (#{{ $v->id }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label mb-0 small">{{ __('pending_products_filter_search') }}</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('pending_products_filter_search_ph') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-sm btn-primary mt-3">{{ __('pending_products_filter_apply') }}</button>
            <a href="{{ route('admin.products.pending') }}" class="btn btn-sm btn-outline-secondary mt-3">{{ __('pending_products_filter_reset') }}</a>
        </div>
    </form>
    <div class="card modern-card mt-3">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0">{{ __('Pending Products') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-striped mb-0">
        <thead><tr>
            <th>{{ __('pending_products_id') }}</th>
            <th>{{ __('pending_products_vendor') }}</th>
            <th>{{ __('pending_products_name') }}</th>
            <th>{{ __('pending_products_created_at') }}</th>
            <th>{{ __('pending_products_approved_at') }}</th>
            <th>{{ __('pending_products_rejection_reason') }}</th>
            <th>{{ __('pending_products_actions') }}</th>
        </tr></thead>
            <tbody>
                @foreach($products as $p)
                <tr @class(['table-warning'=> (bool)$p->rejection_reason])>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->vendor?->name }} (#{{ $p->vendor_id }})</td>
                    <td>{{ $p->name }}</td>
            <td>{{ $p->created_at }}</td>
            <td>{{ optional($p->approved_at)->format('Y-m-d H:i') }}</td>
            <td class="max-w-260 ws-pre-line">{{ $p->rejection_reason }}</td>
                    <td>
                        <form method="post" action="{{ route('admin.products.approve', $p->id) }}" class="d-inline-block">@csrf<button class="btn btn-sm btn-success">{{ __('pending_products_approve') }}</button></form>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}">{{ __('pending_products_reject_delete') }}</button>
                        <!-- reject modal -->
                        <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" action="{{ route('admin.products.reject', $p->id) }}" class="reject-delete-form">@csrf
                                    <div class="modal-header"><h5 class="modal-title">{{ __('pending_products_modal_title') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('pending_products_action_label') }}</label>
                                            <select name="mode" class="form-select action-mode" data-target="rejectFields{{ $p->id }}">
                                                <option value="reject">{{ __('pending_products_action_reject_keep') }}</option>
                                                <option value="delete">{{ __('pending_products_action_delete') }}</option>
                                            </select>
                                        </div>
                                        <div id="rejectFields{{ $p->id }}" class="reject-fields">
                                            <label class="form-label">{{ __('pending_products_reason_label') }} <span class="text-danger">*</span></label>
                                            <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('pending_products_reason_placeholder') }}" required></textarea>
                                            <small class="text-muted d-block mt-1">{{ __('pending_products_reason_help') }}</small>
                                        </div>
                                        <div class="alert alert-warning d-none mt-3 delete-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i> {{ __('pending_products_delete_warning') }}
                                        </div>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pending_products_cancel') }}</button><button class="btn btn-danger submit-action">{{ __('pending_products_submit') }}</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

{{-- No per-page JS imports allowed; behavior should be provided by centralized admin.js adapters. --}}
