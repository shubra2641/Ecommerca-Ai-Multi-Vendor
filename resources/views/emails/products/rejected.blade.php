<p>Hi {{ $product->vendor->name ?? 'Vendor' }},</p>
<p>We're sorry but your product <strong>{{ $product->name }}</strong> was rejected during review.</p>
@if(!empty($reason))
    <p><strong>Reason:</strong> {{ $reason }}</p>
@endif
<p>If you have questions, please contact support.</p>
