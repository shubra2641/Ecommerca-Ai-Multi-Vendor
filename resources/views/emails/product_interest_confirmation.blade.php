<p>{{ __('Thank you for subscribing to product notifications.') }}</p>
<p>{{ __('Type') }}: {{ $interest->type }}</p>
<p>{{ __('You will be notified when we have an update.') }}</p>
<p><a href="{{ route('notify.unsubscribe', $interest->unsubscribe_token) }}">{{ __('Unsubscribe') }}</a></p>
