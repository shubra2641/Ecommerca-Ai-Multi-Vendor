@extends('front.layout')
@section('title', __('Invoices').' - '.config('app.name'))
@section('content')

<section class="account-section">
  <div class="container account-grid">
    @include('front.account._sidebar')
    <main class="account-main">
      <div class="invoices-wrapper">

        <!-- Header -->
        <div class="order-title-card">
          <div class="title-row">
            <div class="title-content">
              <h1 class="modern-order-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="title-icon">
                  <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Invoices / Payments') }}
              </h1>
              <p class="order-date-modern">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                {{ __('Payment History & Invoices') }}
              </p>
            </div>
          </div>
        </div>

        @if(!$payments->count())
        <!-- Empty State -->
        <div class="modern-card">
          <div class="empty-state">
            <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="empty-icon">
              <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="empty-text">{{ __('No payments yet.') }}</p>
          </div>
        </div>
        @else
        <!-- Payments List -->
        <div class="modern-card">
          <div class="card-header-modern">
            <h3 class="card-title-modern">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="4" width="22" height="16" rx="2" />
                <path d="M1 10h22" />
              </svg>
              {{ __('Payment History') }}
            </h3>
            <span class="badge-count">{{ $payments->total() }} {{ __('payments') }}</span>
          </div>

          <div class="items-list-modern">
            @foreach($payments as $p)
            <div class="item-card-modern payment-item-modern">
              <div class="item-img-wrapper">
                <div class="item-img-placeholder">
                  <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
              </div>
              <div class="item-info-modern">
                <h4 class="item-name-modern">{{ __('Invoice') }} #{{ $p->id }}</h4>
                <p class="item-variant-modern">
                  {{ __('Order') }}:
                  <a href="{{ route('user.orders.show',$p->order_id) }}" class="order-link">
                    #{{ $p->order_id }}
                  </a>
                </p>
                <div class="item-meta-modern">
                  <span class="meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <rect x="1" y="4" width="22" height="16" rx="2" />
                      <path d="M1 10h22" />
                    </svg>
                    {{ __('Method') }}: <strong>{{ ucfirst($p->method) }}</strong>
                  </span>
                  <span class="payment-badge-modern status-{{ $p->status }}">
                    {{ ucfirst($p->status) }}
                  </span>
                </div>
              </div>
              <div class="item-total-modern">
                {{ number_format($p->amount, 2) }}<br>
                <small>{{ $p->currency }}</small>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
          {{ $payments->links() }}
        </div>
        @endif

      </div>
    </main>
  </div>
</section>
@endsection