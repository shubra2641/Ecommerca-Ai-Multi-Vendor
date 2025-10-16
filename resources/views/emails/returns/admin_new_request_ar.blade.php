@component('mail::message')

**المنتج:** {{ $product }}

**الطلب:** #{{ $order_id }}

@component('mail::button', ['url' => $url])
عرض الطلب
@endcomponent

مع الشكر،
{{ config('app.name') }}

@endcomponent
