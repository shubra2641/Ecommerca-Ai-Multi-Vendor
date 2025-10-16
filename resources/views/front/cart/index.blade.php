@extends('front.layout')
@section('title', __('Cart').' - '.config('app.name'))
@section('content')
<section class="products-section cart-section">
    <div class="container cart-container">
        <section class="cart-inner">
            <div class="panel-card">
                @if(!count($items))
                <div class="empty-cart">
                    <p class="text-muted">{{ __('Your cart is empty.') }}</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">{{ __('Shop Now') }}</a>
                </div>
                @else
                <div class="checkout-row">
                    <div class="cart-items-col">
                        <h2 class="panel-title">Cart ({{ count($items) }} item{{ count($items)>1?'s':'' }})</h2>

                        @foreach(($cartItemsPrepared ?? $items) as $it)
                        <div class="cart-item">
                            <div class="cart-thumb">
                                <a href="{{ route('products.show',$it['product']->slug) }}">
                                    @if($it['product']->main_image)
                                    <img src="{{ asset($it['product']->main_image) }}" alt="{{ $it['product']->name }}">
                                    @else
                                    <div class="no-image">No Image</div>
                                    @endif
                                </a>
                            </div>
                            <div class="meta">
                                <a href="{{ route('products.show',$it['product']->slug) }}" class="name">{{ $it['product']->name }}</a>
                                @if(!empty($it['variant_label']))
                                <div class="small-muted">{{ $it['variant_label'] }}</div>
                                @endif
                                @if($it['product']->short_description)
                                <div class="desc">{{ Str::limit($it['product']->short_description,120) }}</div>
                                @endif

                                <div class="row">
                                    @if(method_exists($it['product'],'seller') && $it['product']->seller)
                                    <div class="seller-note">Sold by <strong>{{ $it['product']->seller->name }}</strong></div>
                                    @endif
                                    @if(($it['product']->stock_qty ?? null) !== null)
                                    <div class="stock">
                                        {{ ($it['product']->stock_qty ?? 0) > 0 ? ($it['product']->stock_qty.' in stock') : 'Out of stock' }}
                                    </div>
                                    @endif
                                </div>

                                <div class="cart-actions">
                                    <form action="{{ route('cart.update') }}" method="post" class="qty-form">@csrf
                                        <input type="hidden" name="lines[{{ $loop->index }}][cart_key]" value="{{ $it['cart_key'] }}">
                                        <label for="qty-input-{{ $loop->index }}" class="qty-label">Qty</label>
                                        <div class="qty-input-group">
                                            <button type="button" class="qty-btn qty-decrease" data-target="#qty-input-{{ $loop->index }}">−</button>
                                            <input id="qty-input-{{ $loop->index }}" name="lines[{{ $loop->index }}][qty]" type="number" min="1" @if(!is_null($it['available'])) max="{{ $it['available'] }}" @endif value="{{ $it['qty'] }}" class="qty-input" data-available="{{ $it['available'] ?? '' }}" />
                                            <button type="button" class="qty-btn qty-increase" data-target="#qty-input-{{ $loop->index }}">+</button>
                                        </div>
                                        <button type="submit" class="visually-hidden">Update</button>
                                        {{-- Remove line button (sets qty to 0 via JS or handled server-side if supported) --}}
                                        <button type="submit" name="lines[{{ $loop->index }}][remove]" value="1" class="circle-btn icon-btn" aria-label="Remove item">
                                            <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 3v1H4v2h16V4h-5V3H9zm1 5v9h2V8H10zm4 0v9h2V8h-2zM7 8v9h2V8H7z" fill="currentColor"/></svg>
                                        </button>
                                        <span class="line-price">{{ number_format($it['display_price'],2) }}</span>
                                    </form>
                                    @if(!empty($it['on_sale']))
                                        <div class="sale-flag">{{ $it['sale_percent'] }}% OFF</div>
                                    @endif
                                </div>
                            </div>

                            <div class="cart-price">
                                    <div data-cart-line-price>
                                        {{ $currency_symbol ?? '$' }}
                                        {{ number_format($it['display_price'] ?? ($price ?? 0),2) }}
                                    </div>
                                    @if(!empty($it['cart_on_sale']))
                                    <div class="original-price">
                                        {{ $currency_symbol ?? '$' }}
                                        {{ number_format($it['display_line_total'] ? ($it['product']->price) : ($it['product']->price),2) }}
                                    </div>
                                    <div class="sale-badge">
                                        {{ $it['cart_sale_percent'] }}% OFF
                                    </div>
                                    @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <aside class="checkout-right">
                        <div class="summary-box panel-card">
                            <h3>Order Summary</h3>
                            @if(isset($coupon) && $coupon)
                            <div class="coupon-applied">
                                <div class="row">
                                    <div>
                                        <strong>{{ $coupon->code }}</strong>
                                        <div class="applied-label">Applied</div>
                                    </div>
                                    <form action="{{ route('cart.removeCoupon') }}" method="post" class="m-0">@csrf
                                        <button class="btn btn-sm btn-outline-secondary">Remove</button>
                                    </form>
                                </div>
                            </div>
                            @else
                            <form action="{{ route('cart.applyCoupon') ?? '#' }}" method="post" data-coupon-form>
                                @csrf
                                <div class="coupon-form-row">
                                    <input type="text" name="coupon" placeholder="Coupon Code">
                                    <button class="btn btn-primary" type="submit">APPLY</button>
                                </div>
                            </form>
                            @endif

                            <div class="summary-break">
                                <div class="line subtotal">
                                    Subtotal ({{ count($items) }} item): <span
                                        class="subtotal-amount">{{ $currency_symbol ?? '$' }}
                                        {{ number_format($displayTotal ?? $total,2) }}</span>
                                </div>
                                <div class="line discount">
                                    Discount:
                                    <span class="discount-amount coupon-discount-value">
                                        @if($displayDiscount > 0)
                                        - {{ $currency_symbol ?? '$' }} {{ number_format($displayDiscount,2) }}
                                        @else
                                        {{ $currency_symbol ?? '$' }} {{ number_format(0,2) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="line shipping">Shipping Fee: <span>Calculated at checkout</span></div>
                                <div class="line total">Total: <span class="total-amount">{{ $currency_symbol ?? '$' }}
                                        {{ number_format(($discounted_total ?? false) ? (($displayTotal ?? $total ?? 0) - ($discount ?? 0)) : ($displayTotal ?? $total ?? 0),2) }}</span>
                                </div>
                            </div>

                            <a href="{{ route('checkout.form') }}" class="btn btn-primary w-100">CHECKOUT</a>
                        </div>
                    </aside>

                </div>
                @endif
            </div>
        </section>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('front/js/cart-qty.js') }}" defer></script>
@endpush

