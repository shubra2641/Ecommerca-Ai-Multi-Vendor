@extends('front.layout')
@section('title', __('Compare Products'))
@section('content')
<section class="products-section">
   <div class="container container-wide">
      <x-breadcrumb :items="[
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Compare'), 'url' => '#'],
        ]" />
      <h1 class="results-title">{{ __('Compare') }}</h1>
      @if(($items ?? collect())->isEmpty())
      @component('front.components.empty-state', [
      'title' => __('No products in compare list.'),
      'actionLabel' => __('Browse Products'),
      'actionUrl' => route('products.index')
      ])@endcomponent
      @else
      <div class="table-scroll">
         <table class="compare-table">
            <thead>
               <tr>
                  <th class="compare-th">{{ __('Feature') }}</th>
                  @foreach($items as $p)
                  <th class="compare-th">
                     <a href="{{ route('products.show',$p->slug) }}" class="compare-link">{{ $p->name }}</a>
                     <form action="{{ route('compare.toggle') }}" method="POST" class="compare-remove-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $p->id }}">
                        <button class="btn-remove">×</button>
                     </form>
                  </th>
                  @endforeach
               </tr>
            </thead>
            <tbody class="compare-tbody">
               <tr>
                  <td class="compare-td feature-td">{{ __('Image') }}</td>
                  @foreach($items as $p)
                  <td class="compare-td">
                     @if($p->main_image)
                     <img src="{{ asset($p->main_image) }}" alt="{{ $p->name }}" class="compare-img">
                     @else
                     <span class="compare-no-image">No Image</span>
                     @endif
                  </td>
                  @endforeach
               </tr>
               <tr>
                  <td class="compare-td feature-td">{{ __('Category') }}</td>
                  @foreach($items as $p)<td class="compare-td">{{ $p->category->name ?? '-' }}</td>@endforeach
               </tr>
               <tr>
                  <td class="compare-td feature-td">{{ __('Brand') }}</td>
                  @foreach($items as $p)<td class="compare-td">{{ $p->brand->name ?? '-' }}</td>@endforeach
               </tr>
               <tr>
                  <td class="compare-td feature-td">{{ __('Price') }}</td>
                  @foreach($items as $p)<td class="compare-td">{{ $currency_symbol ?? '$' }} {{ number_format($p->display_price ?? $p->effectivePrice(),0) }}</td>@endforeach
               </tr>
               <tr>
                  <td class="compare-td feature-td">SKU</td>
                  @foreach($items as $p)<td class="compare-td">{{ $p->sku ?? '-' }}</td>@endforeach
               </tr>
               <tr>
                  <td class="compare-td feature-td">{{ __('Stock') }}</td>
                  @foreach($items as $p)<td class="compare-td">{{ $p->availableStock() ?? '∞' }}</td>@endforeach
               </tr>
               <tr>
                  <td class="compare-td feature-td">{{ __('Weight') }}</td>
                  @foreach($items as $p)<td class="compare-td">{{ $p->weight ?? '-' }}</td>@endforeach
               </tr>
               <tr>
                  <td class="compare-td feature-td">{{ __('Featured') }}</td>
                  @foreach($items as $p)<td class="compare-td">{{ $p->is_featured? __('Yes'):__('No') }}</td>@endforeach
               </tr>
            </tbody>
         </table>
      </div>
      @endif
   </div>
</section>
@endsection