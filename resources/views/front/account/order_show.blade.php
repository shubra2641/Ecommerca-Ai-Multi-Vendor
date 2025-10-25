@extends('front.layout')
@section('title', __('Order').' #'.$order->id.' - '.config('app.name'))
@section('content')

<section class="account-section order-details-modern">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="order-detail-wrapper">

                <!-- Header with Back Button -->
                <div class="order-modern-header">
                    <a href="{{ route('user.orders') }}" class="btn-back-modern">
                        <i class="fas fa-arrow-left"></i>
                        <span>{{ __('Back to Orders') }}</span>
                    </a>
                    <div class="header-actions">
                        <a href="{{ route('user.orders.invoice.pdf',$order->id) }}" class="btn-download" target="_blank">
                            <i class="fas fa-download"></i>
                            <span>{{ __('Download PDF') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Order Title & Status -->
                <div class="order-title-card">
                    <div class="title-row">
                        <div class="title-content">
                            <h1 class="modern-order-title">{{ __('Order') }} <span class="order-number">#{{ $order->id }}</span></h1>
                            <p class="order-date-modern">
                                <i class="fas fa-clock"></i>
                                {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div class="status-badge-modern status-{{ $order->status }}">
                            <span class="pulse-dot"></span>
                            <span class="status-text">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="timeline-card-modern">
                    <h3 class="card-title-modern">
                        <i class="fas fa-stream"></i>
                        {{ __('Order Timeline') }}
                    </h3>
                    <div class="timeline-container">
                        @foreach($stages as $key=>$label)
                        <div class="timeline-item {{ in_array($key,$reached) ? 'completed' : '' }} {{ $key === $current ? 'active' : '' }}">
                            <div class="timeline-marker">
                                @if(in_array($key,$reached))
                                <i class="fas fa-check"></i>
                                @else
                                <span class="marker-dot"></span>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">{{ $label }}</h4>
                                @if($key === $current)
                                <p class="timeline-date">{{ $order->updated_at->format('M j, g:i A') }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="order-grid-modern">

                    <!-- Left Column -->
                    <div class="order-main-col">

                        <!-- Order Items -->
                        <div class="modern-card items-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-box-open"></i>
                                    {{ __('Order Items') }}
                                </h3>
                                <span class="badge-count">{{ $order->items->count() }} {{ __('items') }}</span>
                            </div>
                            <div class="items-list-modern">
                                @foreach($itemRows as $it)
                                <div class="item-card-modern">
                                    <div class="item-img-wrapper">
                                        <div class="item-img-placeholder">
                                            {{ strtoupper(substr($it['name'],0,2)) }}
                                        </div>
                                    </div>
                                    <div class="item-info-modern">
                                        <h4 class="item-name-modern">{{ $it['name'] }}</h4>
                                        @if($it['variant_label'])
                                        <p class="item-variant-modern">{{ $it['variant_label'] }}</p>
                                        @endif
                                        <div class="item-meta-modern">
                                            <span class="meta-item">
                                                <i class="fas fa-box"></i>
                                                {{ __('Qty') }}: <strong>{{ $it['qty'] }}</strong>
                                            </span>
                                            <span class="meta-item price-tag">
                                                {{ number_format($it['price'],2) }} {{ $currency_symbol }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="item-total-modern">
                                        {{ number_format($it['price'] * $it['qty'], 2) }}<br>
                                        <small>{{ $currency_symbol }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="modern-card address-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ __('Delivery Address') }}
                                </h3>
                            </div>
                            <div class="address-box-modern">
                                <div class="address-icon-modern">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="address-text-modern">{!! nl2br(e($addrText)) !!}</div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column (Sidebar) -->
                    <div class="order-sidebar-col">

                        <!-- Order Summary -->
                        <div class="modern-card summary-card sticky-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-receipt"></i>
                                    {{ __('Order Summary') }}
                                </h3>
                            </div>
                            <div class="summary-lines-modern">
                                <div class="summary-line-modern">
                                    <span>{{ __('Subtotal') }}</span>
                                    <strong>{{ number_format($subtotal,2) }} {{ $currency_symbol }}</strong>
                                </div>
                                @if($order->shipping_price)
                                <div class="summary-line-modern">
                                    <span>{{ __('Shipping') }}</span>
                                    <strong>{{ number_format($shipping_price,2) }} {{ $currency_symbol }}</strong>
                                </div>
                                @endif
                                @if($order->tax_amount)
                                <div class="summary-line-modern">
                                    <span>{{ __('Tax') }}</span>
                                    <strong>{{ number_format($tax_amount,2) }} {{ $currency_symbol }}</strong>
                                </div>
                                @endif
                                <div class="summary-divider-modern"></div>
                                <div class="summary-line-modern total-line">
                                    <span>{{ __('Total Amount') }}</span>
                                    <strong class="total-amount">{{ number_format($total,2) }} {{ $currency_symbol }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="modern-card payment-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-credit-card"></i>
                                    {{ __('Payment Info') }}
                                </h3>
                            </div>
                            <div class="payment-info-modern">
                                <div class="info-row-modern">
                                    <span class="info-label">{{ __('Method') }}</span>
                                    <span class="info-value method-badge">{{ ucfirst($order->payment_method) }}</span>
                                </div>
                                <div class="info-row-modern">
                                    <span class="info-label">{{ __('Status') }}</span>
                                    <span class="payment-badge-modern status-{{ $order->payment_status }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="actions-card-modern">
                            <a href="{{ route('user.invoices') }}" class="btn-action-modern btn-secondary">
                                <i class="fas fa-file-invoice"></i>
                                {{ __('View All Invoices') }}
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </main>
    </div>
</section>
@endsection