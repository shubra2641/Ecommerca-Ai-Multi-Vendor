@extends('front.layout')
@section('title', __('Order').' #'.$order->id.' - '.config('app.name'))
@section('content')

<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="order-detail-page">
                <div class="order-head">
                    <a href="{{ route('user.orders') }}" class="back-link">← {{ __('Orders') }}</a>
                    <h1 class="page-title">{{ __('Tracking Details') }}</h1>
                    <div class="order-meta">{{ __('Order Date') }}: {{ $order->created_at->format('jS M Y') }} · ID
                        #{{ $order->id }}</div>
                </div>
                <div class="tracking-block">
                    <div class="status-icon status-{{ $order->status }}"></div>
                    <div class="status-text-main">{{ ucfirst($order->status) }} <small>{{ __('on') }}
                            {{ $order->updated_at->format('l, jS M, H:i A') }}</small></div>
                    <div class="actions"><a href="#" class="btn btn-primary btn-place ghost"
                            disabled>{{ __('Review delivery') }}</a>
                    </div>
                </div>
                <div class="flex-cols">
                    <div class="col primary">
                        <div class="panel">
                            <h4>{{ __('Delivery address') }}</h4>
                            <div class="addr-text">{!! nl2br(e($ovbAddressText)) !!}</div>
                            <ul class="shipment-steps">
                                @foreach($ovbShipmentStages['stages'] as $key=>$label)
                                <li
                                    class="step {{ in_array($key,$ovbShipmentStages['reached'])?'done':'' }} {{ $key===$ovbShipmentStages['current']?'current':'' }}">
                                    <span class="step-label">{{ $label }}</span>
                                </li>
                                @endforeach
                            </ul>
                            {{-- Order shipment step styles integrated into envato-fixes.css --}}
                        </div>
                        <div class="panel">
                            <h4>{{ __('Item summary') }}</h4>
                            <div class="items-table">
                                @foreach($order->items as $it)
                                <div class="item-row">
                                    <div class="thumb">{{ strtoupper(substr($it->name,0,1)) }}</div>
                                    <div class="info">
                                        <div class="name">{{ $it->name }} @if($vl = ($ovbVariantLabels[$it->id] ??
                                            null)) <small class="small-muted">— {{ $vl }}</small>@endif</div>
                                        <div class="small">{{ __('Qty') }}: {{ $it->qty }}</div>
                                    </div>
                                    <div class="price">{{ number_format($it->price,2) }} {{ $order->currency }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col side">
                        <div class="panel sticky">
                            <h4>{{ __('Order / Invoice summary') }}</h4>
                            <div class="summary-line">
                                <span>{{ __('Subtotal') }}</span><strong>{{ number_format($order->subtotal ?? ($order->total - ($order->shipping_price??0)),2) }}
                                    {{ $order->currency }}</strong>
                            </div>
                            @if($order->shipping_price)
                            <div class="summary-line">
                                <span>{{ __('Shipping') }}</span><strong>{{ number_format($order->shipping_price,2) }}
                                    {{ $order->currency }}</strong>
                            </div>
                            @endif
                            @if($order->tax_amount)
                            <div class="summary-line">
                                <span>{{ __('Tax') }}</span><strong>{{ number_format($order->tax_amount,2) }}
                                    {{ $order->currency }}</strong>
                            </div>
                            @endif
                            <div class="summary-line total">
                                <span>{{ __('Payment') }}</span><strong>{{ ucfirst($order->payment_status) }}
                                    ({{ $order->payment_method }})</strong>
                            </div>
                            <div class="summary-actions">
                                <a href="{{ route('user.invoices') }}"
                                    class="btn btn-primary btn-place">{{ __('Find invoice') }}</a>
                                <a href="{{ route('user.orders.invoice.pdf',$order->id) }}"
                                    class="btn btn-primary btn-place outline" target="_blank">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>
@endsection