@extends('front.layout')
@section('title', __('Orders').' - '.config('app.name'))
@section('content')

<section class="account-section">
  <div class="container account-grid">
    @include('front.account._sidebar')
    <main class="account-main">
      <div class="orders-page">

        <!-- Header -->
        <div class="order-title-card">
          <div class="title-row">
            <div class="title-content">
              <h1 class="modern-order-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="title-icon">
                  <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                {{ __('Orders') }}
              </h1>
              <p class="order-date-modern">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                  <path d="M9 22V12h6v10" />
                </svg>
                {{ __('Your Order History') }}
              </p>
            </div>
          </div>
        </div>

        @if(!$orders->count())
        <!-- Empty State -->
        <div class="modern-card">
          <div class="empty-state">
            <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="empty-icon">
              <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <p class="empty-text">{{ __('No orders yet.') }}</p>
          </div>
        </div>
        @else
        <!-- Orders List -->
        <div class="modern-card">
          <div class="card-header-modern">
            <h3 class="card-title-modern">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              {{ __('Order History') }}
            </h3>
            <span class="badge-count">{{ $orders->total() }} {{ __('orders') }}</span>
          </div>

          <div class="items-list-modern">
            @foreach($orders as $o)
            <div class="item-card-modern order-item-modern">
              <div class="item-img-wrapper">
                <div class="item-img-placeholder">
                  <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                </div>
              </div>
              <div class="item-info-modern">
                <h4 class="item-name-modern">{{ __('Order') }} #{{ $o->id }}</h4>
                <p class="item-variant-modern">
                  {{ $ordersFirstSummaries[$o->id] ?? __('Order') }}
                </p>
                <div class="item-meta-modern">
                  <span class="meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="10" />
                      <path d="M12 6v6l4 2" />
                    </svg>
                    {{ $o->created_at->format('M j, Y') }}
                  </span>
                  <span class="meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <rect x="1" y="3" width="15" height="13" />
                      <path d="M16 8h4v8h-4M1 12h12" />
                    </svg>
                    {{ __('Items') }}: <strong>{{ $o->items->count() }}</strong>
                  </span>
                  <span class="payment-badge-modern status-{{ $o->status }}">
                    <span class="pulse-dot"></span>
                    <span class="status-text">{{ ucfirst($o->status) }}</span>
                  </span>
                </div>
                <div class="item-meta-modern item-meta-payment">
                  <span class="payment-badge-modern status-{{ $o->payment_status }}">
                    {{ __('Payment') }}: {{ ucfirst($o->payment_status) }}
                  </span>
                </div>
              </div>
              <div class="item-total-modern">
                <div class="total-amount-wrapper">
                  {{ number_format($o->total, 2) }}<br>
                  <small>{{ $o->currency }}</small>
                </div>
                <a href="{{ route('user.orders.show',$o) }}" class="badge-count">
                  <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  {{ __('View') }}
                </a>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
          {{ $orders->links() }}
        </div>
        @endif

      </div>
    </main>
  </div>
</section>
@endsection