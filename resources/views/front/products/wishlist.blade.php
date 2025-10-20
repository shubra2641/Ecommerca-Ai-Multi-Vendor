@extends('front.layout')
@section('title', __('My Wishlist'))
@section('content')
<section class="products-section">
  <div class="container container-wide">
    <x-breadcrumb :items="[
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('My Wishlist'), 'url' => '#'],
        ]" />
    <h1 class="results-title">{{ __('My Wishlist') }}</h1>
    @if(($items ?? collect())->isEmpty())
    @component('front.components.empty-state', [
    'title' => __('No wishlist items yet.'),
    'actionLabel' => __('Browse Products'),
    'actionUrl' => route('products.index')
    ])@endcomponent
    @else
    <div class="products-grid">
      @foreach($items as $product)
      @include('front.products.partials.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds ?? [], 'compareIds' => $compareIds ?? []])
      @endforeach
    </div>
    @endif
  </div>
</section>
@endsection