@component('mail::message')

تم تحديث حالة طلب الاسترجاع/الاستبدال الخاص بك.

**المنتج:** {{ $product }}

**الحالة الجديدة:** {{ $status }}

@component('mail::button', ['url' => $url])
عرض الطلب
@endcomponent

مع الشكر،
{{ config('app.name') }}

@endcomponent
