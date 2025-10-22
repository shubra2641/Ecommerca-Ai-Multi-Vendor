@component('mail::message')

**Product:** {{ $product }}

**Order:** #{{ $order_id }}

@component('mail::button', ['url' => $url])
View Request
@endcomponent

Thanks,
{{ config('app.name') }}

@endcomponent
