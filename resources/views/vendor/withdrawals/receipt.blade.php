@extends('vendor.layout')

@section('title', __('vendor.withdrawals.title') . ' - ' . ($withdrawal->reference ?? ''))

@section('content')
<div class="receipt-box">
  <div class="receipt-header">
  <h1 class="receipt-title">{{ __('Withdrawal Receipt') }}</h1>
  <button type="button" data-action="print" class="btn btn-outline-secondary print-btn"><i class="fas fa-print"></i> {{ __('Print') }}</button>
  </div>
  <div class="receipt-meta mb-3">
    <div>{{ __('Reference') }}: <span class="ref-code">{{ $withdrawal->reference }}</span></div>
    <div>{{ __('Date') }}: {{ $withdrawal->created_at->format('Y-m-d H:i') }}</div>
    <div>{{ __('Status') }}: <span class="status-completed">{{ __('vendor.withdrawals.status_completed') }}</span></div>
  </div>
  <div class="amount mb-4">{{ number_format($withdrawal->amount,2) }} {{ $withdrawal->currency }}</div>
  <table class="table table-sm">
    <tbody>
      <tr><th>{{ __('Vendor') }}</th><td>{{ $user->name }}</td></tr>
      <tr><th>{{ __('Email') }}</th><td>{{ $user->email }}</td></tr>
      <tr><th>{{ __('Payment Method') }}</th><td>{{ ucfirst(str_replace('_',' ',$withdrawal->payment_method)) }}</td></tr>
      @if($withdrawal->notes)
      <tr><th>{{ __('Notes') }}</th><td>{{ $withdrawal->notes }}</td></tr>
      @endif
      @if($withdrawal->admin_note)
      <tr><th>{{ __('Admin Note') }}</th><td>{{ $withdrawal->admin_note }}</td></tr>
      @endif
    </tbody>
  </table>
  <p class="mt-4 small text-muted">{{ __('This receipt confirms the completion of your withdrawal. Keep it for your records.') }}</p>
</div>
@endsection
