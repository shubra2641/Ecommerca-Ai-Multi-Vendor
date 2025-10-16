@extends('vendor.layout')

@section('title', __('Notifications'))

@section('content')
<div class="container-fluid py-3">
	<h3 class="mb-3">{{ __('Notifications') }}</h3>
	@if($notifications->count())
		<div class="list-group mb-3">
			@foreach($notifications as $n)
				<div class="list-group-item d-flex justify-content-between align-items-start {{ $n->read_at ? 'text-muted' : '' }}">
					<div class="me-3">
						<div class="fw-semibold">{{ $n->data['title'] ?? ucfirst(str_replace('_',' ', $n->data['type'] ?? 'Notification')) }}</div>
						<div class="small">{{ $n->data['message'] ?? $n->data['text'] ?? '' }}</div>
						<div class="small text-muted mt-1">{{ $n->created_at->diffForHumans() }}</div>
					</div>
					@if(!$n->read_at)
					<form method="POST" action="{{ route('vendor.notifications.read', $n->id) }}" class="ms-auto">
						@csrf
						<button class="btn btn-sm btn-outline-primary">{{ __('Mark read') }}</button>
					</form>
					@endif
				</div>
			@endforeach
		</div>
		{{ $notifications->links() }}
	@else
		<div class="text-muted">{{ __('No notifications') }}</div>
	@endif
</div>
@endsection

