@component('mail::message')
<link rel="stylesheet" href="{{ asset('css/email-styles.css') }}">
<div class="email-container">
    <h2>Return / Exchange Status Updated</h2>
    <p>The status for your request has changed.</p>
    <table class="email-table">
        <tr>
            <td><strong>Product</strong></td>
            <td>{{ $product }}</td>
        </tr>
        <tr>
            <td><strong>New status</strong></td>
            <td>{{ $status }}</td>
        </tr>
    </table>

    @component('mail::button', ['url' => $url])
    View Request
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
</div>
@endcomponent
