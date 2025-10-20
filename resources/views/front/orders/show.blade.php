@extends('front.layout')

@section('title', __('Order #') . $order->id . ' - ' . config('app.name'))

@section('content')
<section class="order-details-modern">
    <div class="container">
        <div class="order-detail-wrapper">
            <x-breadcrumb :items="[
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' =>  __('Order #') . $order->id . ' - ' . config('app.name')],
        ]" />
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
                        <span>{{ __('Download Invoice') }}</span>
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
                                    @if($it->meta && isset($it->meta['variant']))
                                    <p class="item-variant-modern">{{ $it->meta['variant']['name'] }}
                                        @if(isset($it->meta['variant']['sku']) && $it->meta['variant']['sku'])
                                        (SKU: {{ $it->meta['variant']['sku'] }})
                                        @endif
                                    </p>
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
                                            {{ $order->currency ?? '$' }}{{ number_format($it->price,2) }}
                                        </span>
                                    </div>
                                    @if($it->description)
                                    <p class="item-description-modern">{{ Str::limit($it->description, 100) }}</p>
                                    @endif
                                </div>
                                <div class="item-total-modern">
                                    {{ $order->currency ?? '$' }}{{ number_format($it->qty * $it->price,2) }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h3 class="card-title-modern">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ __('Shipping Address') }}
                            </h3>
                        </div>
                        @if($order->shipping_address)
                        <div class="address-box-modern">
                            <div class="address-icon-modern">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    <path d="M9 22V12h6v10" />
                                </svg>
                            </div>
                            <div class="address-text-modern">
                                @if(!empty($order->shipping_address['customer_name']))
                                <div class="address-name-bold">{{ $order->shipping_address['customer_name'] }}</div>
                                @endif
                                @if(!empty($order->shipping_address['customer_address']))
                                <div>{{ $order->shipping_address['customer_address'] }}</div>
                                @endif
                                @if($order->shipping_address['city_id'])
                                @php
                                $city = \App\Models\City::find($order->shipping_address['city_id']);
                                $governorate = $city ? \App\Models\Governorate::find($city->governorate_id) : null;
                                $country = $governorate ? \App\Models\Country::find($governorate->country_id) : null;
                                @endphp
                                <div>
                                    {{ $city ? $city->name : '' }}
                                    @if($governorate), {{ $governorate->name }}@endif
                                    @if($country), {{ $country->name }}@endif
                                </div>
                                @endif
                                @if(!empty($order->shipping_address['customer_phone']))
                                <div><strong>{{ __('Phone') }}:</strong> {{ $order->shipping_address['customer_phone'] }}</div>
                                @endif
                                @if(!empty($order->shipping_address['customer_email']))
                                <div><strong>{{ __('Email') }}:</strong> {{ $order->shipping_address['customer_email'] }}</div>
                                @endif
                                @if(!empty($order->shipping_address['notes']))
                                <div class="mt-2"><strong>{{ __('Notes') }}:</strong> {{ $order->shipping_address['notes'] }}</div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="text-muted-modern">{{ __('No shipping address provided') }}</div>
                        @endif
                    </div>

                    <!-- Payment Attachments -->
                    @if(($ovbAttachments ?? collect())->count())
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h3 class="card-title-modern">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                {{ __('Payment Attachments') }}
                            </h3>
                        </div>
                        <div class="attachments-list-modern">
                            @foreach($ovbAttachments as $att)
                            <a href="{{ asset('storage/' . $att['path']) }}" target="_blank" class="attachment-card-modern">
                                <div class="attachment-icon-modern">📄</div>
                                <div class="attachment-info-modern">
                                    <div class="attachment-name-modern">{{ __('Payment Document') }} #{{ $att['id'] ?? '' }}</div>
                                    <div class="attachment-type-modern">{{ strtoupper(pathinfo($att['path'], PATHINFO_EXTENSION)) }}</div>
                                </div>
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                                <strong>{{ $order->currency ?? '$' }}{{ number_format($order->items->sum(function($item) { return $item->qty * $item->price; }), 2) }}</strong>
                            </div>
                            @if($order->shipping_price)
                            <div class="summary-line-modern">
                                <span>{{ __('Shipping') }}</span>
                                <strong>{{ $order->currency ?? '$' }}{{ number_format($order->shipping_price, 2) }}</strong>
                            </div>
                            @endif
                            <div class="summary-divider-modern"></div>
                            <div class="summary-line-modern total-line">
                                <span>{{ __('Total Amount') }}</span>
                                <strong class="total-amount">{{ $order->currency ?? '$' }}{{ number_format($order->total,2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Details -->
                    @if($order->shipping_zone_id)
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h3 class="card-title-modern">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                                {{ __('Shipping Details') }}
                            </h3>
                        </div>
                        <div class="payment-info-modern">
                            @php
                            $shippingZone = \App\Models\ShippingZone::find($order->shipping_zone_id);
                            @endphp
                            <div class="info-row-modern">
                                <span class="info-label">{{ __('Company') }}</span>
                                <span class="info-value">{{ $shippingZone ? $shippingZone->name : __('Unknown') }}</span>
                            </div>
                            @if($order->shipping_estimated_days)
                            <div class="info-row-modern">
                                <span class="info-label">{{ __('Delivery') }}</span>
                                <span class="info-value">{{ $order->shipping_estimated_days }} {{ __('days') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

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
                                <span class="info-label">{{ __('Status') }}</span>
                                <span class="payment-badge-modern status-{{ $order->payment_status }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions-card-modern">
                        <a href="{{ route('products.index') }}" class="btn-action-modern btn-primary-action">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1" />
                                <circle cx="20" cy="21" r="1" />
                                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />
                            </svg>
                            {{ __('Continue Shopping') }}
                        </a>
                        @if($order->payment_status !== 'paid')
                        <a href="#" class="btn-action-modern btn-secondary">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="1" y="4" width="22" height="16" rx="2" />
                                <path d="M1 10h22" />
                            </svg>
                            {{ __('Complete Payment') }}
                        </a>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</section>
@endsection