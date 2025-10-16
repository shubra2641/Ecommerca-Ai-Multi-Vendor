@component('mail::message')
<link rel="stylesheet" href="{{ asset('css/email-styles.css') }}">
<div class="email-container-rtl">
    <h2>تم تحديث حالة الاسترجاع / الاستبدال</h2>
    <p>تم تغيير حالة طلبك.</p>
    <table class="email-table">
        <tr>
            <td><strong>المنتج</strong></td>
            <td>{{ $product }}</td>
        </tr>
        <tr>
            <td><strong>الحالة الجديدة</strong></td>
            <td>{{ $status }}</td>
        </tr>
    </table>

    @component('mail::button', ['url' => $url])
    عرض الطلب
    @endcomponent

    مع الشكر،<br>
    {{ config('app.name') }}
</div>
@endcomponent
