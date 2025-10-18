@extends('front.layout')

@section('title', __('Order #') . $order->id . ' - ' . config('app.name'))

@section('content')
<section class="orders-section">
    <div class="container">
        <div class="panel-card">
            <h2 class="panel-title">{{ __('Order Details') }}</h2>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-item">
                        <strong>{{ __('Order Number') }}:</strong> #{{ $order->id }}
                    </div>
                    <div class="info-item">
                        <strong>{{ __('Order Date') }}:</strong> {{ $order->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <strong>{{ __('Status') }}:</strong> 
                        <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <strong>{{ __('Payment') }}:</strong> 
                        <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-outline" href="{{ route('user.orders.invoice.pdf', $order->id) }}">
                    📄 {{ __('Download Invoice (PDF)') }}
                </a>
                <a class="btn btn-outline" href="{{ route('user.orders') }}">
                    📋 {{ __('View All Orders') }}
                </a>
            </div>

            <hr />
            <h4>🚚 {{ __('Shipping & Billing Information') }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ __('Shipping Address') }}</h5>
                    <div class="address-card">
                        @if($order->shipping_address)
                        <div class="address-name">{{ $order->shipping_address['name'] ?? '' }}</div>
                        <div class="address-line">{{ $order->shipping_address['line1'] ?? '' }}</div>
                        @if(!empty($order->shipping_address['line2']))
                        <div class="address-line">{{ $order->shipping_address['line2'] }}</div>
                        @endif
                        <div class="address-line">
                            {{ $order->shipping_address['city'] ?? '' }}, 
                            {{ $order->shipping_address['state'] ?? '' }} 
                            {{ $order->shipping_address['postal_code'] ?? '' }}
                        </div>
                        @else
                        <div class="text-muted">{{ __('No shipping address provided') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>{{ __('Shipping Details') }}</h5>
                    <div class="shipping-info">
                        <div class="info-item">
                            <strong>{{ __('Shipping fee') }}:</strong>
                            <span class="price">
                                {{ $order->shipping_price !== null ? (($order->currency ?? '$') . number_format($order->shipping_price,2)) : __('TBD') }}
                            </span>
                        </div>
                        <div class="info-item">
                            <strong>{{ __('Estimated delivery') }}:</strong>
                            {{ $order->shipping_estimated_days ? $order->shipping_estimated_days . ' ' . __('days') : __('N/A') }}
                        </div>
                    </div>
                </div>
            </div>

            <hr />
            <h4>🛍️ {{ __('Order Items') }}</h4>
            <div class="items-container">
                @foreach($order->items as $it)
                <div class="order-item d-flex align-items-center justify-content-between">
                    <div class="item-details">
                        <div class="item-name"><strong>{{ $it->name }}</strong></div>
                        <div class="item-meta small-muted">
                            {{ __('Quantity') }}: {{ $it->qty }} × 
                            {{ $order->currency ?? '$' }}{{ number_format($it->price,2) }}
                        </div>
                        @if($it->description)
                        <div class="item-description small-muted">{{ Str::limit($it->description, 100) }}</div>
                        @endif
                    </div>
                    <div class="order-price">
                        {{ $order->currency ?? '$' }}{{ number_format($it->qty * $it->price,2) }}
                    </div>
                </div>
                @endforeach
                
                {{-- Order Summary --}}
                <div class="order-summary">
                    <div class="summary-row">
                        <span>{{ __('Subtotal') }}:</span>
                        <span>{{ $order->currency ?? '$' }}{{ number_format($order->items->sum(function($item) { return $item->qty * $item->price; }), 2) }}</span>
                    </div>
                    @if($order->shipping_price)
                    <div class="summary-row">
                        <span>{{ __('Shipping') }}:</span>
                        <span>{{ $order->currency ?? '$' }}{{ number_format($order->shipping_price, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-row total-row">
                        <span><strong>{{ __('Total') }}:</strong></span>
                        <span><strong>{{ $order->currency ?? '$' }}{{ number_format($order->total,2) }}</strong></span>
                    </div>
                </div>
            </div>

        @if(($ovbAttachments ?? collect())->count())
            <hr />
            <h4>📎 {{ __('Payment Attachments') }}</h4>
            <div class="attachments-grid">
            @foreach($ovbAttachments as $att)
                <div class="attachment-item">
                    <a href="{{ asset('storage/' . $att['path']) }}" target="_blank" class="attachment-link">
                        <div class="attachment-icon">📄</div>
                        <div class="attachment-info">
                            <div class="attachment-name">{{ __('Payment Document') }} #{{ $att['id'] ?? '' }}</div>
                            <div class="attachment-type small-muted">{{ strtoupper(pathinfo($att['path'], PATHINFO_EXTENSION)) }}</div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            <hr />
            <div class="action-section">
                <h4>{{ __('What\'s Next?') }}</h4>
                <div class="action-buttons-grid">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        🛒 {{ __('Continue Shopping') }}
                    </a>
                    <a href="{{ route('user.orders') }}" class="btn btn-outline">
                        📋 {{ __('View All Orders') }}
                    </a>
                    @if($order->payment_status !== 'paid')
                    <a href="#" class="btn btn-outline">
                        💳 {{ __('Complete Payment') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('front/css/order-success.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('front/js/order-success.js') }}" defer></script>
@endpush