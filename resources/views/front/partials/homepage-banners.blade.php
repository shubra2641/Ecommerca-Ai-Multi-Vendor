@if(($banners ?? collect())->count())
@foreach($banners as $placement => $group)
<section class="homepage-banners placement-{{ Str::slug($placement) }} animate-fade-in-up" aria-label="{{ __('Promotions') }}">
    <div class="container">
        <div class="banners-grid">
            @foreach($group as $bn)
            <a @if($bn->link_url) href="{{ $bn->link_url }}" @endif class="banner-item" aria-label="{{ $bn->alt_text ?? __('Banner') }}">
                <img loading="lazy" src="{{ $bn->image ? asset('storage/'.$bn->image) : asset('images/product-placeholder.svg') }}" alt="{{ $bn->alt_text ?? '' }}" class="banner-img">
            </a>
            @endforeach
        </div>
    </div>
</section>
@endforeach
@endif