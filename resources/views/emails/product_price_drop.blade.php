<p>{{ __('Good news! The price dropped for a product you are tracking.') }}</p>
<p>{{ __('Product ID') }}: {{ $interest->product_id }}</p>
@if($interest->product)
<p><a href="{{ route('products.show',$interest->product->slug) }}">{{ $interest->product->name }}</a></p>
@endif
<p>{{ __('Old price') }}: {{ number_format($oldPrice,2) }}<br>
{{ __('New price') }}: {{ number_format($newPrice,2) }}<br>
{{ __('Change') }}: -{{ number_format($percent,2) }}%</p>
<p><a href="{{ route('notify.unsubscribe', $interest->unsubscribe_token) }}">{{ __('Unsubscribe') }}</a></p>
<p>{{ __('Thank you') }}</p>
