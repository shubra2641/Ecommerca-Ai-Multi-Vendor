<aside class="catalog-sidebar">
    <div class="sidebar-block">
        <h4>{{ __('Price') }}</h4>
        <div class="price-range">
            <div class="value-row">
                <span>{{ __('Min') }}: <strong id="prMinVal">{{ request('min_price') ?: 0 }}</strong></span>
                <span>{{ __('Max') }}: <strong id="prMaxVal">{{ request('max_price') ?: 1000 }}</strong></span>
            </div>
            <div class="range-wrapper">
                <input type="range" min="0" max="1000" step="10" value="{{ request('min_price') ?: 0 }}" id="prMin">
                <input type="range" min="0" max="1000" step="10" value="{{ request('max_price') ?: 1000 }}" id="prMax">
            </div>
            <input type="hidden" form="catalogFilters" name="min_price" id="prMinHidden" value="{{ request('min_price') }}">
            <input type="hidden" form="catalogFilters" name="max_price" id="prMaxHidden" value="{{ request('max_price') }}">
        </div>
    </div>
    <div class="sidebar-block">
        <h4>{{ __('Category') }}</h4>
        <nav class="category-list">
            @foreach($categories as $cat)
            <div class="cat-item">
                <a href="{{ route('products.category',$cat->slug) }}">{{ $cat->name }}</a>
                @if($cat->children->count())
                <div class="cat-children">
                    @foreach($cat->children as $child)
                    <a href="{{ route('products.category',$child->slug) }}">{{ $child->name }}</a>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </nav>
    </div>
    <div class="sidebar-block">
        <h4>{{ __('Brand') }}</h4>
        <div class="brand-search"><input type="search" placeholder="{{ __('Search') }}" disabled></div>
        <div class="brand-list">
            @if(isset($brandList) && $brandList->count())
            @foreach($brandList as $b)
            <label class="brand-item">
                <input type="checkbox" form="catalogFilters" name="brand[]" value="{{ $b->slug }}" {{ in_array($b->slug,$csSelectedBrands ?? [])?'checked':'' }}>
                <span>{{ $b->name }}</span>
                <span class="count">{{ $b->products_count }}</span>
            </label>
            @endforeach
            @endif
        </div>
    </div>
</aside>
