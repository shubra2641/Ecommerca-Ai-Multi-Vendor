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
                <i class="fas fa-file-invoice title-icon"></i>
                {{ __('Invoices / Payments') }}
              </h1>
              <p class="order-date-modern">
                <i class="fas fa-clipboard-list"></i>
                {{ __('Payment History & Invoices') }}
              </p>
            </div>
          </div>
        </div>

        @if(!$payments->count())
        <!-- Empty State -->
        <div class="modern-card">
          <div class="empty-state">
            <i class="fas fa-file-invoice empty-icon"></i>
            <p class="empty-text">{{ __('No payments yet.') }}</p>
          </div>
        </div>
        @else
        <!-- Payments List -->
        <div class="modern-card">
          <div class="card-header-modern">
            <h3 class="card-title-modern">
              <i class="fas fa-credit-card"></i>
              {{ __('Payment History') }}
            </h3>
            <span class="badge-count">{{ $payments->total() }} {{ __('payments') }}</span>
          </div>

          <div class="items-list-modern">
            @foreach($payments as $p)
            <div class="item-card-modern payment-item-modern">
              <div class="item-img-wrapper">
                <div class="item-img-placeholder">
                  <i class="fas fa-file-invoice"></i>
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
                    <i class="fas fa-credit-card"></i>
                    {{ __('Method') }}: <strong>{{ ucfirst($p->method) }}</strong>
                  </span>
                  <span class="payment-badge-modern status-{{ $p->status }}">
                    {{ ucfirst($p->status) }}
                  </span>
                </div>
              </div>
              <div class="item-total-modern">
                {{ number_format($p->display_amount ?? $p->amount, 2) }}<br>
                <small>{{ $currencySymbol ?? $p->currency }}</small>
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