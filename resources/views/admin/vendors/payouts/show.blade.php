@extends('layouts.admin')
@section('content')
<div class="admin-container">
    <h1>{{ __('Payout #') }}{{ $payout->id }}</h1>
    <p><strong>{{ __('User') }}:</strong> {{ $payout->user?->name }} ({{ $payout->user?->email }})</p>
    <p><strong>{{ __('Amount') }}:</strong> {{ $payout->amount }} {{ $payout->currency }}</p>
    <p><strong>{{ __('Status') }}:</strong> {{ ucfirst($payout->status) }}</p>
    <p><strong>{{ __('Admin Note') }}:</strong> {{ $payout->admin_note }}</p>
    @if($payout->status === 'pending')
        <form method="post" action="{{ route('admin.vendor.withdrawals.payouts.execute', $payout) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
                <label class="form-label">{{ __('Admin Note') }}</label>
                <textarea name="admin_note" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">{{ __('Proof (optional)') }}</label>
                <input type="file" name="proof" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-success">{{ __('Execute Payout') }}</button>
        </form>
    @endif
    @if(!empty($payout->proof_path))
        <div class="mt-3">
            <label class="form-label">{{ __('Proof Image') }}</label>
            <div><img src="{{ asset('storage/'.$payout->proof_path) }}" alt="proof" class="payout-proof-img"></div>
        </div>
    @endif
</div>
@endsection
