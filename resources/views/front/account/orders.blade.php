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
                <i class="fas fa-shopping-bag title-icon"></i>
                {{ __('Orders') }}
              </h1>
              <p class="order-date-modern">
                <i class="fas fa-home"></i>
                {{ __('Your Order History') }}
              </p>
            </div>
          </div>
        </div>

        @if(!$orders->count())
        <!-- Empty State -->
        <div class="modern-card">
          <div class="empty-state">
            <i class="fas fa-shopping-bag empty-icon"></i>
            <p class="empty-text">{{ __('No orders yet.') }}</p>
          </div>
        </div>
        @else
        <!-- Orders List -->
        <div class="modern-card">
          <div class="card-header-modern">
            <h3 class="card-title-modern">
              <i class="fas fa-clipboard-list"></i>
              {{ __('Order History') }}
            </h3>
            <span class="badge-count">{{ $orders->total() }} {{ __('orders') }}</span>
          </div>

          <div class="items-list-modern">
            @foreach($orders as $o)
            <div class="item-card-modern order-item-modern">
              <div class="item-img-wrapper">
                <div class="item-img-placeholder">
                  <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                </div>
              </div>
              <div class="item-info-modern">
                <h4 class="item-name-modern">{{ __('Order') }} #{{ $o->id }}</h4>
                <p class="item-variant-modern">
                  {{ $ordersFirstSummaries[$o->id] ?? __('Order') }}
                </p>
                <div class="item-meta-modern">
                  <span class="meta-item">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                    {{ $o->created_at->format('M j, Y') }}
                  </span>
                  <span class="meta-item">
                    <i class="fas fa-box" aria-hidden="true"></i>
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
                  {{ number_format($o->display_total, 2) }}<br>
                  <small>{{ $currencySymbol }}</small>
                </div>
                <a href="{{ route('user.orders.show',$o) }}" class="badge-count">
                  <i class="fas fa-eye" aria-hidden="true"></i>
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