@extends('front.layout')

@section('title', 'Products - ' . config('app.name'))

@section('meta')
<meta name="description"
    content="Browse our wide selection of high-quality products. Find exactly what you're looking for with our advanced filtering and search options.">
<meta name="keywords" content="products, shop, online store, e-commerce">
<meta property="og:title" content="Products - {{ config('app.name') }}">
<meta property="og:description" content="Browse our wide selection of high-quality products.">
<meta property="og:type" content="website">
@endsection

@section('content')
<section class="products-section">
    <div class="container container-wide">
        <x-breadcrumb :items="array_merge([
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Products'), 'url' => route('products.index')]
        ], request('category') ? [['title' => request('category'), 'url' => '#']] : [])" />
    <h1 class="results-title">{{ method_exists($products, 'total') ? $products->total() : $products->count() }} {{ __('Results') }}
            @if(request('q')) {{ __('for') }} "{{ request('q') }}" @endif</h1>
        <div class="catalog-layout">
            @include('front.products.partials.sidebar')
            <div class="catalog-main">
                <form method="GET" action="{{ route('products.index') }}" id="catalogFilters">
                    <div class="filters-row">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search') }}"
                            class="filter-input" />
                        <select name="category" class="filter-select">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category')==$cat->slug?'selected':'' }}>
                                {{ $cat->name }}</option>
                            @foreach($cat->children as $child)
                            <option value="{{ $child->slug }}" {{ request('category')==$child->slug?'selected':'' }}>—
                                {{ $child->name }}</option>
                            @endforeach
                            @endforeach
                        </select>
                        <select name="sort" class="filter-select">
                            <option value="">{{ __('Recommended') }}</option>
                            <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>
                                {{ __('Price: Low to High') }}</option>
                            <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>
                                {{ __('Price: High to Low') }}</option>
                        </select>
                        <label class="flag"><input type="checkbox" name="featured" value="1"
                                {{ request('featured')?'checked':'' }}> <span>{{ __('Featured') }}</span></label>
                        <label class="flag"><input type="checkbox" name="sale" value="1"
                                {{ request('sale')?'checked':'' }}> <span>{{ __('On Sale') }}</span></label>
                        <label class="flag"><input type="checkbox" name="best" value="1"
                                {{ request('best')?'checked':'' }}> <span>{{ __('Best') }}</span></label>
                        <button type="submit" class="btn btn-primary btn-compact">{{ __('Apply') }}</button>
                        @if(request()->query())
                        <a href="{{ route('products.index') }}"
                            class="btn btn-outline btn-compact">{{ __('Reset') }}</a>
                        @endif
                        <div class="results-badge">{{ method_exists($products, 'total') ? $products->total() : $products->count() }}</div>
                    </div>
                </form>
                <div class="chips-row">
                    @if(request('category'))
                    <div class="chip">{{ request('category') }} <a href="{{ route('products.index') }}">×</a></div>
                    @endif
                    @if(request('q'))
                    <div class="chip">{{ __('Search') }}: "{{ request('q') }}" <a
                            href="{{ route('products.index') }}">×</a></div>
                    @endif
                </div>
                <div class="products-grid">
                    @forelse($products as $product)
                    @include('front.products.partials.product-card', ['product' => $product, 'wishlistIds' =>
                    $wishlistIds ?? [], 'compareIds' => $compareIds ?? []])
                    @empty
                    @component('front.components.empty-state', [
                        'title' => __('No products found'),
                        'message' => __('Try adjusting your filters or search terms to find what you\'re looking for.'),
                        'actionLabel' => __('Clear All Filters'),
                        'actionUrl' => route('products.index')
                    ])@endcomponent
                    @endforelse
                </div>
                @if($products->hasPages())
                <div class="pagination-wrapper">{{ $products->appends(request()->query())->links() }}</div>
                @endif
            </div>
</section>
@endsection