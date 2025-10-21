@extends('vendor.layout')

@section('title', __('vendor.withdrawals.title') . ' - ' . ($withdrawal->reference ?? ''))

@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">
    <!-- Header -->
    <div class="admin-order-header">
      <div class="header-left">
        <div class="admin-header-content">
          <div class="admin-header-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <div class="admin-header-text">
            <h1 class="admin-order-title">{{ __('Withdrawal Receipt') }}</h1>
            <p class="admin-order-subtitle">{{ __('Reference') }}: {{ $withdrawal->reference }}</p>
          </div>
        </div>
      </div>
      <div class="header-actions">
        <button type="button" data-action="print" class="admin-btn admin-btn-secondary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 9H4A2 2 0 0 0 2 11V17A2 2 0 0 0 4 19H6M6 9V5A2 2 0 0 1 8 3H16A2 2 0 0 1 18 5V9M6 9H18M18 9V11A2 2 0 0 1 16 13H8A2 2 0 0 1 6 11V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          {{ __('Print') }}
        </button>
      </div>
    </div>

    <!-- Receipt Card -->
    <div class="admin-modern-card">
      <div class="admin-card-header">
        <h3 class="admin-card-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          {{ __('Receipt Details') }}
        </h3>
      </div>
      <div class="admin-card-body">
        <!-- Receipt Meta -->
        <div class="admin-receipt-meta mb-4">
          <div class="row">
            <div class="col-md-4">
              <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Reference') }}</label>
                <div class="admin-badge admin-badge-secondary">{{ $withdrawal->reference }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Date') }}</label>
                <div class="admin-fw-semibold">{{ $withdrawal->created_at->format('Y-m-d H:i') }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Status') }}</label>
                <span class="admin-badge admin-badge-success">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  {{ __('vendor.withdrawals.status_completed') }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Amount -->
        <div class="admin-amount-display mb-4">
          <div class="admin-stat-value">{{ number_format($withdrawal->amount,2) }} {{ $withdrawal->currency }}</div>
          <div class="admin-stat-label">{{ __('Withdrawal Amount') }}</div>
        </div>

        <!-- Details Table -->
        <div class="admin-table-responsive">
          <table class="admin-table">
            <tbody>
              <tr>
                <th class="admin-fw-semibold">{{ __('Vendor') }}</th>
                <td>{{ $user->name }}</td>
              </tr>
              <tr>
                <th class="admin-fw-semibold">{{ __('Email') }}</th>
                <td>{{ $user->email }}</td>
              </tr>
              <tr>
                <th class="admin-fw-semibold">{{ __('Payment Method') }}</th>
                <td>
                  <div class="admin-text-muted">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ ucfirst(str_replace('_',' ',$withdrawal->payment_method)) }}
                  </div>
                </td>
              </tr>
              @if($withdrawal->notes)
              <tr>
                <th class="admin-fw-semibold">{{ __('Notes') }}</th>
                <td class="admin-text-muted">{{ $withdrawal->notes }}</td>
              </tr>
              @endif
              @if($withdrawal->admin_note)
              <tr>
                <th class="admin-fw-semibold">{{ __('Admin Note') }}</th>
                <td class="admin-text-muted">{{ $withdrawal->admin_note }}</td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>

        <!-- Footer Note -->
        <div class="admin-receipt-footer mt-4">
          <div class="admin-alert admin-alert-info">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ __('This receipt confirms the completion of your withdrawal. Keep it for your records.') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection