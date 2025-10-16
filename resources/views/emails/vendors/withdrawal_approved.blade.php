<p>{{ __('Hello') }} {{ $withdrawal->user?->name }},</p>
<p>{{ __('Your withdrawal request of :amount :currency has been approved.', ['amount' => number_format($withdrawal->amount,2), 'currency' => $withdrawal->currency]) }}</p>
@if($withdrawal->admin_note)
    <p><strong>{{ __('Admin note:') }}</strong> {{ $withdrawal->admin_note }}</p>
@endif
<p>{{ __('Thank you.') }}</p>
