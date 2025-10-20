@extends('front.layout')

@section('title', $product->seo_title ?: $product->name . ' - ' . config('app.name'))

@section('meta')
@if($product->seo_description)
<meta name="description" content="{{ $product->seo_description }}">
@else
<meta name="description" content="{{ $product->short_description ?: $product->name }}">
@endif
@if($product->seo_keywords)
<meta name="keywords" content="{{ $product->seo_keywords }}">
@endif
<meta property="og:title" content="{{ $product->name }}">
<meta property="og:description" content="{{ $product->short_description ?: $product->name }}">
<meta property="og:type" content="product">
@endsection

@section('content')
<section class="product-details-section">
    <div class="container">
        <div class="product-details-layout">
            <div class="product-gallery">
                {{-- Use prepared $gallery (first element main) --}}
                <div class="main-image" role="group" aria-label="Product images">
                    <img id="productMainImage" src="{{ $mainImage['url'] ?? asset('front/images/default-product.png') }}" alt="{{ $product->name }}" loading="lazy">
                </div>
                @if((count($gallery ?? []) > 1))
                <div class="thumbnail-gallery" aria-label="Product thumbnails">
                    @foreach($gallery as $index => $img)
                    <button type="button" class="thumbnail {{ $index === 0 ? 'active' : '' }}" data-image="{{ $img['url'] }}" aria-label="Show image {{ $index + 1 }}">
                        <img src="{{ $img['url'] }}" alt="{{ $product->name }} - Image {{ $index + 1 }}" loading="lazy">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="product-info product-info-card">
                <div class="pi-meta-row">
                    @if($product->brand)
                    <span class="pi-brand"><a href="{{ route('products.brand', $product->brand->slug) }}">{{ strtoupper($product->brand->name) }}</a></span>
                    <span class="sep">›</span>
                    @endif
                    <span class="pi-category"><a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a></span>
                    @auth
                    @if(!empty($inCart))<span class="badge-pill in-cart">{{ __('In Your Cart') }}</span>@endif
                    @endauth
                </div>
                <h1 class="product-title clamp-lines">{{ $product->name }}</h1>
                @if($product->short_description)
                <p class="product-subtitle clamp-2">{{ $product->short_description }}</p>
                @endif
                @php
                $rating = $product->reviews_avg_rating ?? 0;
                $stars = [];
                for ($i = 1; $i <= 5; $i++) {
                    $stars[]=['filled'=> $i <= round($rating)];
                        }
                        @endphp
                        <div class="rating-line" aria-label="Rating {{ number_format($rating,1) }} out of 5">
                        <span class="stars" aria-hidden="true">
                            @foreach($stars as $s)
                            <span class="star {{ $s['filled'] ? 'filled' : '' }}">{{ $s['filled'] ? '★' : '☆' }}</span>
                            @endforeach
                        </span>
                        <span class="rating-value">{{ number_format($product->reviews_avg_rating ?? 0,1) }}</span>
                        <a href="#reviews" class="rating-count">{{ $product->reviews_count ?? 0 }} {{ __('Ratings') }}</a>
            </div>
            <div class="price-line product-pricing" data-original-price="1">
                @if($product->type === 'variable')
                @php
                $variations = $product->variations;
                $prices = $variations->pluck('price')->filter()->values();
                $minP = $prices->min();
                $maxP = $prices->max();
                @endphp
                @if($minP !== null)
                @if($minP == $maxP)
                <span class="price-current" id="productPrice">{{ $currency_symbol ?? '$' }} {{ number_format($minP,2) }}</span>
                @else
                <span class="price-current" id="productPrice">{{ $currency_symbol ?? '$' }} {{ number_format($minP,2) }}</span>
                <span class="price-range-sep">-</span>
                <span class="price-current" id="productPriceMax">{{ $currency_symbol ?? '$' }} {{ number_format($maxP,2) }}</span>
                @endif
                @endif
                @else
                <span class="price-current" id="productPrice">{{ $currency_symbol ?? '$' }} {{ number_format($product->effectivePrice(),2) }}</span>
                @if($product->isOnSale())
                <span class="price-original">{{ $currency_symbol ?? '$' }} {{ number_format($product->price,2) }}</span>
                @php
                $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100);
                @endphp
                @if($discountPercent > 0)<span class="discount-badge">{{ $discountPercent }}% {{ __('Off') }}</span>@endif
                @endif
                @endif
            </div>
            <div class="interest-count mt-1" aria-label="Interest count">
                <small class="text-muted">{{ __('Interested users') }}: {{ $product->interests_count ?? 0 }}</small>
            </div>
            <div class="badges-row">
                @if($product->isOnSale())
                @php
                $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100);
                @endphp
                <span class="badge-soft badge-sale" id="globalSaleBadge">{{ $discountPercent }}% {{ __('Off') }}</span>
                @elseif($product->type === 'variable')
                <span class="badge-soft badge-sale" id="globalSaleBadge"></span>
                @endif
                @php
                $stock = $product->availableStock();
                $stockClass = 'badge-success';
                $levelLabel = __('In Stock');

                if ($stock === null) {
                $stockClass = 'badge-info';
                $levelLabel = __('Unlimited');
                } elseif ($stock <= 0) {
                    $stockClass='badge-danger' ;
                    $levelLabel=__('Out of Stock');
                    } elseif ($stock <=5) {
                    $stockClass='badge-warning' ;
                    $levelLabel=__('Low Stock');
                    }
                    @endphp
                    <span class="badge-soft badge-stock {{ $stockClass }}" id="topStockBadge">{{ $levelLabel }}</span>
            </div>
            <div class="divider-line"></div>

            @if($product->type === 'variable' && count($variationAttributes ?? []))
            <div class="variation-grid-card mt-4" id="variationGridCard" data-used='{{ e(json_encode($usedAttrs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'>
                <h3 class="variation-card-title">{{ __('Choose Options') }}</h3>
                <div class="variation-grid">
                    @foreach($variationAttributes as $attr)
                    <div class="variation-attr-block" data-attr="{{ $attr['name'] }}">
                        <div class="attr-label"><span class="attr-icon">{{ $attr['icon'] }}</span>{{ $attr['label'] }}</div>
                        <div class="attr-options">
                            @foreach($attr['values'] as $index => $v)
                            @if($attr['is_color'])
                            <div class="color-swatch-wrapper">
                                <input type="radio" name="attr_{{ $attr['name'] }}" value="{{ $v }}" id="attr_{{ $attr['name'] }}_{{ $index }}" class="attr-radio" {{ $index === 0 ? 'checked' : '' }}>
                                <label for="attr_{{ $attr['name'] }}_{{ $index }}" class="option-btn color attr-option-btn" aria-label="{{ $v }}" title="{{ $v }}" data-attr-value="{{ $v }}" data-swatch="{{ $v }}"></label>
                                <span class="swatch-label">{{ $v }}</span>
                            </div>
                            @else
                            <input type="radio" name="attr_{{ $attr['name'] }}" value="{{ $v }}" id="attr_{{ $attr['name'] }}_{{ $index }}" class="attr-radio" {{ $index === 0 ? 'checked' : '' }}>
                            <label for="attr_{{ $attr['name'] }}_{{ $index }}" class="option-btn attr-option-btn" data-attr-value="{{ $v }}">{{ $v }}</label>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="stock-status">
                @php
                $available = $product->availableStock();
                @endphp
                @if($available === 0)
                {{ __('Out of stock') }}
                @elseif(is_null($available))
                {{ __('In stock') }}
                @else
                {{ $available }} {{ __('in stock') }}
                @endif
            </div>

            <div class="product-meta mt-4" id="productMeta">
                @if($product->sku)
                <div class="meta-item sku-item">
                    <span class="meta-label">{{ __('SKU') }} :</span>
                    <div class="sku-wrapper">
                        <span class="meta-value" id="skuValue">{{ $product->sku }}</span>
                        <button type="button" class="btn-copy-sku" id="copySkuBtn" title="{{ __('Copy SKU') }}" aria-label="{{ __('Copy SKU') }}">📋</button>
                    </div>
                </div>
                @endif
                @if($product->tags && $product->tags->count() > 0)
                <div class="meta-item tags-item">
                    <span class="meta-label">{{ __('Tags') }} :</span>
                    <div class="tags-list" id="tagsListInline">
                        @php
                        $allTags = $product->tags;
                        $tagsFirst = $allTags->take(3);
                        $tagsMore = $allTags->skip(3);
                        @endphp
                        @foreach($tagsFirst as $tag)
                        <a href="{{ route('products.tag', $tag->slug) }}" class="tag-chip" title="{{ $tag->name }}">{{ $tag->name }}</a>
                        @endforeach
                        @if($tagsMore->count())
                        <button type="button" class="tag-more" id="toggleAllTags" data-more="{{ $tagsMore->count() }}" aria-expanded="false">+{{ $tagsMore->count() }} {{ __('more') }}</button>
                        <div class="tag-more-hidden" id="allTagsHidden">
                            @foreach($tagsMore as $tag)
                            <a href="{{ route('products.tag', $tag->slug) }}" class="tag-chip" title="{{ $tag->name }}">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                @if($product->weight)
                <div class="meta-item weight-item">
                    <span class="meta-label">{{ __('Weight') }}:</span>
                    <span class="meta-value">{{ $product->weight }} {{ __('kg') }}</span>
                </div>
                @endif
                @if($product->length || $product->width || $product->height)
                <div class="meta-item dimensions-item" title="{{ __('Dimensions') }}">
                    <span class="meta-label">{{ __('Dimensions') }}:</span>
                    <span class="meta-value">{{ $product->length ?? '-' }} × {{ $product->width ?? '-' }} × {{ $product->height ?? '-' }} {{ __('cm') }}</span>
                </div>
                @endif
                @if($product->refund_days)
                <div class="meta-item return-item">
                    <span class="meta-label">{{ __('Returns') }}:</span>
                    <span class="meta-value">{{ $product->refund_days }} {{ __('day') }} {{ __('return guarantee') }}</span>
                </div>
                @endif
            </div>
        </div>

        <aside class="purchase-box sticky">
            <div class="seller">
                <div class="avatar"></div>
                <div>
                    <div class="seller-name">{{ __('Sold by') }} {{ $product->seller ? $product->seller->name : 'Store' }}</div>
                    <div class="seller-score">{{ number_format($product->reviews_avg_rating ?? 0,1) }} • <small>{{ $product->reviews_count ?? 0 }} {{ __('ratings') }}</small></div>
                </div>
            </div>
            @if($onSale)
            <div class="sale-badge-inline">{{ __('On Sale') }}</div>
            @endif
            <ul class="seller-flags">
                <li>{{ __('No warranty') }}</li>
                <li>{{ __('Free delivery on lockers & pickup points') }}</li>
                <li>{{ __('Return eligible') }}: <strong>{{ $product->refund_days ?? 7 }}</strong> {{ __('days') }}</li>
                <li>{{ __('Secure payments') }}</li>
            </ul>
            @if($isOut)
            <div class="stock-out">
                <button type="button" class="front-button pill-out" disabled>{{ __('Out of stock') }}</button>
                <div class="notify-block">
                    <button type="button" class="front-button pill-notify notify-btn" id="notifyBtn" data-type="back_in_stock" data-product-id="{{ $product->id }}" @auth data-email="{{ auth()->user()->email }}" @if(auth()->user() && auth()->user()->phone) data-phone="{{ auth()->user()->phone }}" @endif @endauth>
                        <span class="notify-label">{{ __('Notify me when available') }}</span>
                        <span class="notify-subscribed d-none">{{ __('Subscribed') }}</span>
                    </button>
                </div>
            </div>
            @else
            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if($product->type === 'variable')
                <input type="hidden" name="variation_id" id="selectedVariationId" value="">
                @endif
                <input type="hidden" name="price" id="selectedPrice" value="{{ $product->effectivePrice() }}">
                <input type="hidden" name="buy_now" id="buyNowFlag" value="">
                <div class="quantity-selector">
                    <input id="qtyInputSide" type="number" name="qty" class="quantity-field" value="1" min="1" max="{{ $product->stock_quantity ?: 999 }}" style="display: none;">
                    <div class="qty-pill" role="group" aria-label="Quantity selector">
                        <button type="button" class="qty-action qty-trash" aria-label="Remove item"><span class="icon-trash">🗑</span></button>
                        <div class="qty-display" id="qtyDisplay">1</div>
                        <button type="button" class="qty-action qty-increase" aria-label="Increase quantity">+</button>
                    </div>
                </div>
                <button class="btn-buy front-button" type="submit" {{ ($product->stock_quantity ?? 1) == 0 ? 'disabled' : '' }}>{{ __('ADD TO CART') }}</button>
            </form>
            @endif
        </aside>
    </div>
    </div>
</section>

<!-- Product Details Tabs -->
<section class="product-tabs-section">
    <div class="container">
        <div class="product-tabs">
            <div class="tab-nav" role="tablist" aria-label="Product details tabs">
                <a href="#description" class="tab-btn active" role="tab" aria-controls="description"
                    id="tab-desc">{{ __('Description') }}</a>
                <a href="#specifications" class="tab-btn" role="tab" aria-controls="specifications"
                    id="tab-specs">{{ __('Specifications') }} <span
                        class="spec-count-badge">{{ $specCount }}</span></a>
                <a href="#reviews" class="tab-btn" role="tab" aria-controls="reviews"
                    id="tab-reviews">{{ __('Reviews') }} ({{ $reviewsCount }})</a>
            </div>

            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane active" id="description" role="tabpanel" aria-labelledby="tab-desc">
                    <div class="description-content">
                        @if($product->description)
                        {{-- Description is escaped and newlines converted to <br>; keep escaped to prevent XSS --}}
                        {!! nl2br(e($product->description)) !!}
                        @else
                        <p class="text-muted">{{ __('No detailed description available for this product.') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane" id="specifications" role="tabpanel" aria-labelledby="tab-specs">
                    <div class="specifications-content">
                        <div class="spec-grid">
                            @if($product->sku)
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">#</span>{{ __('SKU') }}</span><span
                                    class="spec-value">{{ $product->sku }}</span></div>
                            @endif
                            @if($product->weight)
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">⚖️</span>{{ __('Weight') }}</span><span
                                    class="spec-value">{{ $product->weight }} {{ __('kg') }}</span></div>
                            @endif
                            @if($product->length || $product->width || $product->height)
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">📦</span>{{ __('Dimensions') }}</span><span
                                    class="spec-value">{{ $product->length ?? '-' }} × {{ $product->width ?? '-' }} ×
                                    {{ $product->height ?? '-' }} {{ __('cm') }}</span></div>
                            @endif
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">🗂️</span>{{ __('Category') }}</span><span
                                    class="spec-value">{{ $product->category->name }}</span></div>
                            @if($product->refund_days)
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">↩️</span>{{ __('Returns') }}</span><span
                                    class="spec-value">{{ $product->refund_days }} {{ __('days') }}</span></div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane" id="reviews" role="tabpanel" aria-labelledby="tab-reviews" data-lazy="reviews"
                    data-loaded="1">
                    @include('front.products.partials.reviews', ['reviews' => $product->reviews()->where('approved',
                    true)->get(), 'product' => $product])
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('modals')
@include('front.partials.notify-modal')