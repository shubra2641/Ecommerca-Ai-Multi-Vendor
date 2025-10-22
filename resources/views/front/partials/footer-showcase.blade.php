{{-- Footer showcase partial expects: $showcaseSections, $showcaseSectionsActiveCount --}}
@if(($showcaseSectionsActiveCount ?? 0) > 0)
<section class="footer-showcase" aria-labelledby="showcase-heading">
    <div class="container">
        <div class="showcase-grid cols-{{ $showcaseSectionsActiveCount }}" role="list">
            @foreach($showcaseSections as $sec)
            @if($sec['type'] !== 'brands')
            <div class="showcase-col" role="listitem">
                @if($sec['title'])<h3 class="showcase-title">{{ $sec['title'] }}</h3>@endif
                <ul class="product-mini-list" role="list">
                    @foreach($sec['items'] as $p)
                    <li class="mini-item {{ $p->mini_flags }}">
                        <a href="{{ route('products.show',$p->slug) }}" class="mini-thumb" aria-label="{{ $p->name }}">
                            <img loading="lazy" src="{{ $p->mini_image_url }}" alt="{{ $p->name }}" class="{{ $p->mini_image_is_placeholder ? 'is-placeholder' : '' }}">
                        </a>
                        <div class="mini-meta">
                            <a href="{{ route('products.show',$p->slug) }}" class="mini-name">{{ $p->mini_trunc_name }}</a>
                            <div class="mini-price">
                                <strong>{{ $p->mini_price_html }}</strong>
                                @clean($p->mini_extra_html)
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @endforeach
        </div>

        {{-- Brand row (brandSec provided by controller) --}}
        @if(isset($brandSec) && $brandSec)
        <div class="brands-row" aria-labelledby="brands-heading">
            <h3 id="brands-heading" class="brands-title">{{ $brandSec['title'] ?? __('Brands') }}</h3>
            <div class="brands-slider" role="list">
                @forelse($brandSec['items'] as $b)
                <a href="{{ route('products.index', ['brand'=>$b->slug]) }}" class="brand-item" role="listitem" aria-label="{{ $b->name }} @if(isset($b->products_count)) ({{ $b->products_count }}) @endif">
                    <span class="brand-name">{{ $b->name }}</span>
                </a>
                @empty
                <div class="brand-empty">{{ __('No brands') }}</div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</section>
@endif