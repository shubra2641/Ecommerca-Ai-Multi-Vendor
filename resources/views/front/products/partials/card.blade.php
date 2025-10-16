{{-- price resolved via model accessors / controller --}}
<div class="product-card-small">
    @if($product->isOnSale())<span class="badge bg-danger position-absolute badge-sale">Sale</span>@endif
    @if($product->main_image)
    <div class="product-image">
        <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}">
    </div>
    @endif
    <h6 class="fw-semibold mb-1"><a href="{{ route('products.show',$product->slug) }}"
            class="text-decoration-none">{{ $product->name }}</a></h6>
    <div class="small text-muted mb-2 small-desc">{{ Str::limit($product->short_description,60) }}</div>
    <div class="fw-semibold">
    @if($product->isOnSale())
    <span class="text-danger">{{ $currency_symbol ?? '$' }} {{ number_format($product->display_price ?? $product->effectivePrice(),2) }}</span>
    <span class="text-muted text-decoration-line-through small">{{ $currency_symbol ?? '$' }} {{ number_format($product->display_price ?? $product->price,2) }}</span>
    @else
    <span>{{ $currency_symbol ?? '$' }} {{ number_format($product->display_price ?? $product->effectivePrice(),2) }}</span>
    @endif
    </div>
</div>