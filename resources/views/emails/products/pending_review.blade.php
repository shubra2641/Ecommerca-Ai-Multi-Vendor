<p>A vendor has submitted a new product for review.</p>
<p><strong>Vendor:</strong> {{ $product->vendor->name ?? 'N/A' }} (ID: {{ $product->vendor->id ?? 'N/A' }})</p>
<p><strong>Product:</strong> {{ $product->name }}</p>
<p><a href="{{ route('admin.products.edit', $product->id) }}">Open in admin panel</a></p>
