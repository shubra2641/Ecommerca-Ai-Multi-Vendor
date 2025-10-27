{{-- Logic extracted to ProductCardComposer: card* variables available --}}
<div class="product-card" role="group" aria-label="Product card">
    <div class="product-image {{ empty($product->main_image) ? 'is-empty' : '' }}">
        <a href="{{ route('products.show',$product->slug) }}" class="product-image-link" aria-label="View {{ $product->name }}">
            @php
            $imageUrl = \App\Helpers\GlobalHelper::storageImageUrl($product->main_image) ?: asset('images/placeholder.png');
            @endphp
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" decoding="async" class="product-image-media {{ empty($product->main_image)?'product-placeholder-img':'' }}">
            {{-- Debug: {{ $imageUrl }} --}}
        </a>

        <div class="badges-inline" aria-hidden="true">
            @if($cardOnSale)
            <span class="product-badge sale-badge" title="Sale">-{{ $cardDiscountPercent }}%</span>
            @endif
            @if($product->is_featured)
            <span class="product-badge featured-badge" title="Featured">Best Seller</span>
            @endif
            @if($cardAvailable === 0)
            <span class="product-badge out-badge" title="Out of stock">{{ __('Out of stock') }}</span>
            @endif
        </div>

        {{-- right-top controls (wishlist, compare, quick cart) --}}
        <div class="top-controls" aria-hidden="false">
            <div class="top-controls-row">
                {{-- Wishlist Button Form --}}
                <form action="{{ route('wishlist.toggle') }}" method="POST" class="wishlist-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="circle-btn icon-btn fav-btn {{ $cardWishActive ? 'active' : '' }}"
                        aria-pressed="{{ $cardWishActive ? 'true' : 'false' }}" aria-label="Toggle wishlist" type="submit">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                    </button>
                </form>

                {{-- Compare Button Form --}}
                <form action="{{ route('compare.toggle') }}" method="POST" class="compare-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="circle-btn icon-btn compare-btn {{ $cardCmpActive ? 'is-active' : '' }}" title="Compare"
                        aria-label="Compare product" type="submit">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
            <div class="top-controls-col">
                <button class="circle-btn icon-btn cart-quick"
                    title="{{ $cardAvailable === 0 ? __('Out of stock') : __('Add to cart') }}"
                    aria-label="{{ $cardAvailable === 0 ? __('Out of stock') : __('Add to cart') }}" data-product="{{ $product->id }}" {{ $cardAvailable === 0 ? 'disabled' : '' }}>
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="product-content product-card-body">
        <div class="product-category"><a href="{{ route('products.category',$product->category->slug) }}">{{ $product->category->name }}</a></div>
        <h3 class="product-title"><a href="{{ route('products.show',$product->slug) }}">{{ $product->name }}</a></h3>
        <div class="product-rating">
            <span class="stars">
                @for($i=1;$i<=5;$i++) <span class="star {{ $i <= $cardFullStars ? 'filled' : '' }}">{{ $i <= $cardFullStars ? '★' : '☆' }}</span>
            @endfor
            </span>
            <span>{{ number_format($cardRating,1) }} • {{ $cardReviewsCount }}</span>
        </div>
        @if(!empty($cardSnippet))
        <div class="product-desc">{{ $cardSnippet }}</div>
        @endif
        <div class="product-price">
            @if($cardOnSale && $cardDisplaySalePrice)
            <span class="price-sale">{{ $currency_symbol ?? '$' }} {{ number_format($cardDisplaySalePrice,0) }}</span>
            <span class="price-original">{{ $currency_symbol ?? '$' }} {{ number_format($cardDisplayPrice,0) }}</span>
            <span class="price-badge">{{ $cardDiscountPercent }}% OFF</span>
            @else
            <span class="price-current">{{ $currency_symbol ?? '$' }} {{ number_format($cardDisplayPrice,0) }}</span>
            @endif
        </div>

        @if($product->type === 'variable')
        <a href="{{ route('products.show',$product->slug) }}"
            class="btn btn-primary btn-sm">{{ __('Select options') }}</a>
        @else
        @if($cardAvailable === 0)
        <button class="btn btn-outline btn-sm notify-btn" data-product="{{ $product->id }}"
            data-type="back_in_stock" @auth data-email="{{ auth()->user()->email }}" @endauth>
            <span class="notify-label">{{ __('Notify Me') }}</span>
            <span class="notify-subscribed d-none">{{ __('Subscribed') }}</span>
        </button>
        @else
        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button class="btn btn-primary btn-sm" type="submit">{{ __('Add to Cart') }}</button>
        </form>
        @endif
        @endif
    </div>
</div>