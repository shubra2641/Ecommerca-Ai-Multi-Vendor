@component('mail::message')

Your return/exchange request status has been updated.

**Product:** {{ $product }}

**New status:** {{ $status }}

@component('mail::button', ['url' => $url])
View Request
@endcomponent

Thanks,
{{ config('app.name') }}

@endcomponent
