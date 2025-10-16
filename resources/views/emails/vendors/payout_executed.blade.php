<p>{{ __('Hello') }} {{ $payout->user?->name }},</p>
<p>{{ __('Your payout of :amount :currency has been executed.', ['amount' => number_format($payout->amount,2), 'currency' => $payout->currency]) }}</p>
@if($payout->admin_note)
    <p><strong>{{ __('Admin note:') }}</strong> {{ $payout->admin_note }}</p>
@endif
<p>{{ __('Thank you.') }}</p>
