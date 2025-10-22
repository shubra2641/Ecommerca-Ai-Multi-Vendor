<aside class="catalog-sidebar">
    <div class="sidebar-block">
        <h4>{{ __('Price') }}</h4>
        <form method="GET" action="{{ request()->url() }}" class="filter-form">
            @foreach(request()->except(['min_price', 'max_price']) as $key => $value)
                @if(is_array($value))
                    @foreach($value as $val)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <div class="price-range">
                <div class="value-row">
                    <span>{{ __('Min') }}: <strong>{{ request('min_price') ?: 0 }}</strong></span>
                    <span>{{ __('Max') }}: <strong>{{ request('max_price') ?: 1000 }}</strong></span>
                </div>
                <div class="price-inputs">
                    <input type="number" name="min_price" min="0" max="1000" step="10" 
                           value="{{ request('min_price') ?: 0 }}" placeholder="{{ __('Min') }}" class="price-input">
                    <span class="price-separator">-</span>
                    <input type="number" name="max_price" min="0" max="1000" step="10" 
                           value="{{ request('max_price') ?: 1000 }}" placeholder="{{ __('Max') }}" class="price-input">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm filter-apply-btn">{{ __('Apply') }}</button>
        </form>
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
        <form method="GET" action="{{ request()->url() }}" class="filter-form">
            @foreach(request()->except(['brand']) as $key => $value)
                @if(is_array($value))
                    @foreach($value as $val)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <div class="brand-search">
                <input type="search" name="brand_search" placeholder="{{ __('Search') }}" value="{{ request('brand_search') }}">
            </div>
            <div class="brand-list">
                @if(isset($brandList) && $brandList->count())
                @foreach($brandList as $b)
                <label class="filter-brand-item">
                    <input type="checkbox" name="brand[]" value="{{ $b->slug }}" {{ in_array($b->slug,$csSelectedBrands ?? [])?'checked':'' }}>
                    <span class="filter-brand-name">{{ $b->name }}</span>
                    <span class="filter-brand-count">{{ $b->products_count }}</span>
                </label>
                @endforeach
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-sm filter-apply-btn">{{ __('Apply') }}</button>
        </form>
    </div>
</aside>
