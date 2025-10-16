@extends('front.layout')
@section('title', __('Orders').' - '.config('app.name'))
@section('content')

<section class="account-section">
 <div class="container account-grid">
  @include('front.account._sidebar')
  <main class="account-main">
   <div class="orders-page">
     <h1 class="page-title">{{ __('Orders') }}</h1>
     <div class="order-filters">
       <input type="text" placeholder="{{ __('Find items') }}" disabled>
       <select disabled><option>{{ now()->year }}</option></select>
     </div>
     @if(!$orders->count())
       <div class="alert alert-info small">{{ __('No orders yet.') }}</div>
     @else
       <div class="orders-list">
         @foreach($orders as $o)
         <div class="order-card">
           <div class="order-status-line">
             <span class="status-dot status-{{ $o->status }}"></span>
             <span class="status-text">{{ ucfirst($o->status) }} <small>{{ $o->created_at->format('l, j M, H:i A') }}</small></span>
             <a href="{{ route('user.orders.show',$o) }}" class="btn btn-primary btn-place">{{ __('View') }}</a>
           </div>
           <div class="order-summary">
              <div class="thumb-stack">
                @foreach($o->items->take(3) as $it)
                  <div class="thumb">{{ strtoupper(substr($it->name,0,1)) }}</div>
                @endforeach
              </div>
              <div class="details">
                <div class="title">{{ $ordersFirstSummaries[$o->id] ?? __('Order') }}</div>
                <div class="meta">{{ __('Items') }}: {{ $o->items->count() }} · {{ number_format($o->total,2) }} {{ $o->currency }}</div>
                <div class="meta">{{ __('Payment') }}: {{ ucfirst($o->payment_status) }}</div>
              </div>
              <div class="badges">
                 <span class="badge subtle">#{{ $o->id }}</span>
                 @if($o->shipping_price) <span class="badge yellow">{{ __('express') }}</span>@endif
              </div>
           </div>
         </div>
         @endforeach
       </div>
       <div class="pagination-wrap">{{ $orders->links() }}</div>
     @endif
   </div>
  </main>
 </div>
</section>
@endsection