@component('mail::message')
<link rel="stylesheet" href="{{ asset('css/email-styles.css') }}">
<div class="email-container">
    <h2>New Return / Exchange Request</h2>
    <table class="email-table">
        <tr>
            <td><strong>Product</strong></td>
            <td>{{ $product }}</td>
        </tr>
        <tr>
            <td><strong>Order</strong></td>
            <td>#{{ $order_id }}</td>
        </tr>
    </table>

    @component('mail::button', ['url' => $url])
    View Request
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
</div>
@endcomponent
