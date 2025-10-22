<p>Hi {{ $product->vendor->name ?? 'Vendor' }},</p>
<p>Your product <strong>{{ $product->name }}</strong> has been approved and is now visible on the marketplace.</p>
<p><a href="{{ route('product.show', $product->slug) }}">View product</a></p>
