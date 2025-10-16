@extends('front.layout')
@section('title', $tag->name.' - Tag')
@section('content')
<section class="products-section">

    <div class="container container-wide">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}" class="breadcrumb-item">{{ __('Home') }}</a>
            <span class="breadcrumb-separator">/</span>
            <a href="{{ route('products.index') }}" class="breadcrumb-item">{{ __('Products') }}</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active">#{{ $tag->name }}</span>
        </nav>
        <h1 class="results-title">{{ method_exists($products, 'total') ? $products->total() : $products->count() }} {{ __('Results') }} -
            #{{ $tag->name }}</h1>
        <div class="catalog-layout">
            @include('front.products.partials.sidebar')
            <div class="catalog-main">
                <form method="GET" action="{{ route('products.tag',$tag->slug) }}" id="catalogFilters">
                    <div class="filters-row">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search') }}"
                            class="filter-input" />
                        <select name="sort" class="filter-select">
                            <option value="">{{ __('Recommended') }}</option>
                            <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>
                                {{ __('Price: Low to High') }}</option>
                            <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>
                                {{ __('Price: High to Low') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-compact">{{ __('Apply') }}</button>
                        @if(request()->query())
                        <a href="{{ route('products.tag',$tag->slug) }}"
                            class="btn btn-outline btn-compact">{{ __('Reset') }}</a>
                        @endif
                        <div class="results-badge">{{ method_exists($products, 'total') ? $products->total() : $products->count() }}</div>
                    </div>
                </form>
                <div class="products-grid">
                    @forelse($products as $product)
                        @include('front.products.partials.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds ?? [], 'compareIds' => $compareIds ?? []])
                        @empty
                        @component('front.components.empty-state', [
                            'title' => __('No products found for this tag'),
                            'actionLabel' => __('All Products'),
                            'actionUrl' => route('products.index')
                        ])@endcomponent
                    @endforelse
                </div>
                @if($products->hasPages())<div class="pagination-wrapper">
                    {{ $products->appends(request()->query())->links() }}</div>@endif
            </div>
        </div>
    </div>
</section>
@endsection