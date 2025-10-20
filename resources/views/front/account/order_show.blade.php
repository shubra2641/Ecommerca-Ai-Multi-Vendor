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
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 12H5M12 19l-7-7 7-7" />
                        </svg>
                        <span>{{ __('Back to Orders') }}</span>
                    </a>
                    <div class="header-actions">
                        <a href="{{ route('user.orders.invoice.pdf',$order->id) }}" class="btn-download" target="_blank">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" />
                            </svg>
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
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 6v6l4 2" />
                                </svg>
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
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Order Timeline') }}
                    </h3>
                    <div class="timeline-container">
                        @foreach($ovbShipmentStages['stages'] as $key=>$label)
                        <div class="timeline-item {{ in_array($key,$ovbShipmentStages['reached']) ? 'completed' : '' }} {{ $key === $ovbShipmentStages['current'] ? 'active' : '' }}">
                            <div class="timeline-marker">
                                @if(in_array($key,$ovbShipmentStages['reached']))
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>
                                @else
                                <span class="marker-dot"></span>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">{{ $label }}</h4>
                                @if($key === $ovbShipmentStages['current'])
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
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    {{ __('Order Items') }}
                                </h3>
                                <span class="badge-count">{{ $order->items->count() }} {{ __('items') }}</span>
                            </div>
                            <div class="items-list-modern">
                                @foreach($order->items as $it)
                                <div class="item-card-modern">
                                    <div class="item-img-wrapper">
                                        <div class="item-img-placeholder" style="background: linear-gradient(135deg, {{ ['#667eea','#764ba2','#f093fb','#f5576c','#4facfe','#00f2fe'][$loop->index % 6] }} 0%, {{ ['#764ba2','#667eea','#4facfe','#00f2fe','#667eea','#00f2fe'][($loop->index + 1) % 6] }} 100%);">
                                            {{ strtoupper(substr($it->name,0,2)) }}
                                        </div>
                                    </div>
                                    <div class="item-info-modern">
                                        <h4 class="item-name-modern">{{ $it->name }}</h4>
                                        @if($vl = ($ovbVariantLabels[$it->id] ?? null))
                                        <p class="item-variant-modern">{{ $vl }}</p>
                                        @endif
                                        <div class="item-meta-modern">
                                            <span class="meta-item">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <rect x="1" y="3" width="15" height="13" />
                                                    <path d="M16 8h4v8h-4M1 12h12" />
                                                </svg>
                                                {{ __('Qty') }}: <strong>{{ $it->qty }}</strong>
                                            </span>
                                            <span class="meta-item price-tag">
                                                {{ number_format($it->price,2) }} {{ $order->currency }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="item-total-modern">
                                        {{ number_format($it->price * $it->qty, 2) }}<br>
                                        <small>{{ $order->currency }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="modern-card address-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ __('Delivery Address') }}
                                </h3>
                            </div>
                            <div class="address-box-modern">
                                <div class="address-icon-modern">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path d="M9 22V12h6v10" />
                                    </svg>
                                </div>
                                <div class="address-text-modern">{!! nl2br(e($ovbAddressText)) !!}</div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column (Sidebar) -->
                    <div class="order-sidebar-col">

                        <!-- Order Summary -->
                        <div class="modern-card summary-card sticky-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    {{ __('Order Summary') }}
                                </h3>
                            </div>
                            <div class="summary-lines-modern">
                                <div class="summary-line-modern">
                                    <span>{{ __('Subtotal') }}</span>
                                    <strong>{{ number_format($order->subtotal ?? ($order->total - ($order->shipping_price??0)),2) }} {{ $order->currency }}</strong>
                                </div>
                                @if($order->shipping_price)
                                <div class="summary-line-modern">
                                    <span>{{ __('Shipping') }}</span>
                                    <strong>{{ number_format($order->shipping_price,2) }} {{ $order->currency }}</strong>
                                </div>
                                @endif
                                @if($order->tax_amount)
                                <div class="summary-line-modern">
                                    <span>{{ __('Tax') }}</span>
                                    <strong>{{ number_format($order->tax_amount,2) }} {{ $order->currency }}</strong>
                                </div>
                                @endif
                                <div class="summary-divider-modern"></div>
                                <div class="summary-line-modern total-line">
                                    <span>{{ __('Total Amount') }}</span>
                                    <strong class="total-amount">{{ number_format($order->total,2) }} {{ $order->currency }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="modern-card payment-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="1" y="4" width="22" height="16" rx="2" />
                                        <path d="M1 10h22" />
                                    </svg>
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
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
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