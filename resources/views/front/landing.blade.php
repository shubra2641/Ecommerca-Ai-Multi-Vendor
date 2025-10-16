@extends('front.layout')
@section('title', config('app.name'))
@section('content')
<main role="main">
    @include('front.partials.hero-slider')

@if(($flashSaleProducts ?? collect())->count())
    <section class="flash-sale-section animate-fade-in-up" aria-labelledby="flash-sale-title">
        <div class="container">
            <div class="section-header">
                <h2 id="flash-sale-title" class="section-title">{{ __('Flash Sale') }}</h2>
                <p class="section-sub">{{ __('Limited time deals – don\'t miss out!') }}</p>
                @if(!empty($flashSaleEndsAt))
                <div class="flash-countdown" data-flash-countdown data-end="{{ $flashSaleEndsAt->utc()->format('Y-m-d\TH:i:s\Z') }}" aria-live="polite">
                    <span class="cd-label">{{ __('Ends in') }}:</span>
                    <span class="cd-part" data-d>00</span><span class="cd-sep">:</span>
                    <span class="cd-part" data-h>00</span><span class="cd-sep">:</span>
                    <span class="cd-part" data-m>00</span><span class="cd-sep">:</span>
                    <span class="cd-part" data-s>00</span>
                </div>
                @endif
            </div>
            <div class="products-grid flash-sale-grid">
                @foreach(($flashSaleProducts ?? collect()) as $product)
                    @include('front.products.partials.product-card', ['product'=>$product, 'wishlistIds'=>$wishlistIds, 'compareIds'=>$compareIds])
                @endforeach
            </div>
            <div class="section-footer">
                <a href="{{ route('products.index', ['filter'=>'on-sale']) }}" class="btn btn-outline btn-lg">{{ __('View All Deals') }}</a>
            </div>
        </div>
    </section>
@endif

<!-- Categories Section (Circular) -->
    <section class="shop-categories animate-fade-in-up" aria-labelledby="categories-title">
        <div class="container">
            <div class="section-header">
                <h2 id="categories-title" class="section-title">{{ __('Shop by Category') }}</h2>
                <p class="section-sub">{{ __('Browse our main categories') }}</p>
            </div>
            <ul class="cat-main-list" role="list" aria-label="{{ __('Main categories') }}">
        @foreach(($landingCategories ?? collect()) as $mc)
                    <li class="cat-main-item">
            <a href="{{ route('products.category', $mc->slug) }}" class="cat-pill" aria-label="{{ __('Browse :name products', ['name'=>$mc->name]) }}">
                            <span class="icon-ring">
                @if($mc->has_image)
                    <img loading="lazy" src="{{ $mc->image_url }}" alt="{{ $mc->name }}">
                                @else
                                    <svg class="placeholder-icon" viewBox="0 0 24 24" aria-hidden="true"><path stroke="currentColor" stroke-width="1.6" d="M3 18V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path stroke="currentColor" stroke-width="1.6" d="m3 15 4.5-4.5 5.5 5.5M14 14l2-2 5 5"/><circle cx="10" cy="9" r="2" stroke="currentColor" stroke-width="1.6" fill="none"/></svg>
                                @endif
                            </span>
                            <span class="cat-label">{{ __($mc->name) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="section-footer">
                <a href="{{ route('products.index') }}" class="btn btn-outline btn-lg">{{ __('View All Products') }}</a>
            </div>
        </div>
    </section>

    @if(($latestProducts ?? collect())->count())
            <section class="latest-products-section animate-fade-in-up" aria-labelledby="latest-products-title">
                <div class="container">
                    <div class="section-header">
                        <h2 id="latest-products-title" class="section-title">{{ __('Latest Products') }}</h2>
                        <p class="section-sub">{{ __('Fresh arrivals just added to our catalog') }}</p>
                    </div>
                    <div class="products-grid latest-products-grid">
                        @foreach(($latestProducts ?? collect()) as $product)
                            @include('front.products.partials.product-card', [ 'product'=>$product, 'wishlistIds'=>$wishlistIds, 'compareIds'=>$compareIds ])
                        @endforeach
                    </div>
                    <div class="section-footer">
                        <a href="{{ route('products.index', ['sort'=>'newest']) }}" class="btn btn-outline btn-lg">{{ __('View All New Arrivals') }}</a>
                    </div>
                </div>
            </section>
        @endif

        @if(($latestPosts ?? collect())->count())
            <section class="latest-articles-section animate-fade-in-up" aria-labelledby="latest-articles-title" data-placeholder="{{ asset('images/product-placeholder.svg') }}">
                <div class="container">
                    <div class="section-header">
                        <h2 id="latest-articles-title" class="section-title">{{ __('Latest Articles') }}</h2>
                        <p class="section-sub">{{ __('Insights & updates from our blog') }}</p>
                    </div>
                    <div class="latest-articles-grid">
                        @foreach($latestPosts as $post)
                            <article class="blog-card">
                                <a href="{{ route('blog.show',$post->slug) }}" class="thumb-wrapper" aria-label="{{ $post->title }}">
                                    @if(!empty($post->featured_image))
                                        <img loading="lazy" src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" />
                                    @else
                                        <img loading="lazy" src="{{ $post->featured_image_url }}" class="is-placeholder" alt="{{ $post->title }}" />
                                    @endif
                                </a>
                                <div class="card-body">
                                    <div class="meta-row">
                                        @if($post->published_at)
                                            <span class="date">{{ $post->published_at->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                    <h3 class="post-title"><a href="{{ route('blog.show',$post->slug) }}">{{ $post->title }}</a></h3>
                                    @if($post->prepared_excerpt)
                                        <p class="excerpt">{{ $post->prepared_excerpt }}</p>
                                    @endif
                                    <a class="read-more" href="{{ route('blog.show',$post->slug) }}">{{ __('Read more') }} →</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="section-footer">
                        <a href="{{ route('blog.index') }}" class="btn btn-outline btn-lg">{{ __('View All Articles') }}</a>
                    </div>
                </div>
            </section>
        @endif

    @include('front.partials.homepage-banners')

    @include('front.partials.footer-showcase')

</main>
@endsection