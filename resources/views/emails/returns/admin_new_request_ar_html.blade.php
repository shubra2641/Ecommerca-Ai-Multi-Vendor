@component('mail::message')
<link rel="stylesheet" href="{{ asset('css/email-styles.css') }}">
<div class="email-container-rtl">
    <h2>طلب استرجاع / استبدال جديد</h2>
    <table class="email-table">
        <tr>
            <td><strong>المنتج</strong></td>
            <td>{{ $product }}</td>
        </tr>
        <tr>
            <td><strong>الطلب</strong></td>
            <td>#{{ $order_id }}</td>
        </tr>
    </table>

    @component('mail::button', ['url' => $url])
    عرض الطلب
    @endcomponent

    مع الشكر،<br>
    {{ config('app.name') }}
</div>
@endcomponent
