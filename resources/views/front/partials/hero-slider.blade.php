@if(($slides ?? collect())->count())
<section class="hero-slider" aria-label="{{ __('Featured') }}">
    <div class="hero-slider-viewport">
        <div class="hero-slider-track" data-hero-slider-track>
            @foreach($slides as $sl)
            <div class="hero-slide" role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration.' / '.$slides->count() }}">
                <img class="hero-slide-img" src="{{ $sl->image ? \App\Helpers\GlobalHelper::storageImageUrl($sl->image) : asset('images/placeholder.svg') }}" alt="{{ $sl->title ?? $sl->subtitle ?? ('Slide '.$loop->iteration) }}">
                <div class="hero-slide-overlay">
                    @if(!empty($sl->title))<h2 class="hero-slide-title">{{ $sl->title }}</h2>@endif
                    @if(!empty($sl->subtitle))<p class="hero-slide-sub">{{ $sl->subtitle }}</p>@endif
                    @if(!empty($sl->button_text) && !empty($sl->link_url))
                    <a href="{{ $sl->link_url }}" class="btn btn-primary hero-slide-cta">{{ $sl->button_text }}</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" class="hero-nav prev" data-hero-prev aria-label="{{ __('Previous slide') }}" hidden>‹</button>
        <button type="button" class="hero-nav next" data-hero-next aria-label="{{ __('Next slide') }}" hidden>›</button>
        <div class="hero-dots" data-hero-dots aria-label="{{ __('Slide navigation') }}" hidden></div>
    </div>
    <noscript>
        <div class="hero-noscript-fallback">
            @if(($slides ?? collect())->first())
            <img src="{{ ($slides->first()->image ? \App\Helpers\GlobalHelper::storageImageUrl($slides->first()->image) : asset('images/placeholder.svg')) }}" alt="{{ $slides->first()->title ?? 'Slide' }}" class="hero-slide-img">
            @endif
        </div>
    </noscript>
</section>
@endif