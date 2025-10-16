@extends('layouts.admin')
@section('content')
<div class="admin-container">
    <h1>{{ __('Withdrawal #') }}{{ $withdrawal->id }}</h1>
    <p><strong>{{ __('User') }}:</strong> {{ $withdrawal->user?->name }} ({{ $withdrawal->user?->email }})</p>
    <p><strong>{{ __('Amount') }}:</strong> {{ $withdrawal->amount }} {{ $withdrawal->currency }}</p>
    <p><strong>{{ __('Status') }}:</strong> {{ ucfirst($withdrawal->status) }}</p>

    @if($withdrawal->status === 'pending')
        <form method="post" action="{{ route('admin.vendor.withdrawals.approve', $withdrawal) }}" class="d-inline-block" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
                <label class="form-label">{{ __('Admin Note') }}</label>
                <textarea name="admin_note" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">{{ __('Proof (optional)') }}</label>
                <input type="file" name="proof" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-success">{{ __('Approve') }}</button>
        </form>
        <form method="post" action="{{ route('admin.vendor.withdrawals.reject', $withdrawal) }}" class="d-inline-block ml-2" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
                <label class="form-label">{{ __('Admin Note') }}</label>
                <textarea name="admin_note" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">{{ __('Proof (optional)') }}</label>
                <input type="file" name="proof" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-danger">{{ __('Reject') }}</button>
        </form>
    @endif

    @if($withdrawal->status === 'approved' && $withdrawal->payout)
        <div class="mt-3">
            <strong>{{ __('Payout') }}:</strong> #{{ $withdrawal->payout->id }} - {{ ucfirst($withdrawal->payout->status) }}
                @if($withdrawal->payout->status === 'pending')
                <form method="post" action="{{ route('admin.vendor.withdrawals.payouts.execute', $withdrawal->payout) }}" class="d-inline-block ml-2" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">{{ __('Admin Note') }}</label>
                        <textarea name="admin_note" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">{{ __('Proof (optional)') }}</label>
                        <input type="file" name="proof" class="form-control" accept="image/*">
                    </div>
                    <button class="btn btn-sm btn-success">{{ __('Execute Payout') }}</button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
