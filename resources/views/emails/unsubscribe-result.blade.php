@if($status==='success')
<p>{{ __('You have been unsubscribed successfully.') }}</p>
@elseif($status==='already')
<p>{{ __('You are already unsubscribed.') }}</p>
@endif
