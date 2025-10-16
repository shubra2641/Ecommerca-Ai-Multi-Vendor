<p>{{ __('Good news! The product you were watching is back in stock.') }}</p>
<p>{{ __('Product ID') }}: {{ $interest->product_id }}</p>
@if($interest->product)
<p><a href="{{ route('products.show',$interest->product->slug) }}">{{ $interest->product->name }}</a></p>
@endif
<p><a href="{{ route('notify.unsubscribe', $interest->unsubscribe_token) }}">{{ __('Unsubscribe') }}</a></p>
<p>{{ __('Thank you') }}</p>
