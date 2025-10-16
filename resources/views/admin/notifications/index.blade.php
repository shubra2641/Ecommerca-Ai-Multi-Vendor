@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Notifications') }}</h1>
        <p class="page-description">{{ __('Manage and send system notifications') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.notifications.send.create') }}" class="btn btn-primary">{{ __('Send notification') }}</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <h3 class="card-title mb-0">{{ __('Notifications') }}</h3>
            </div>
            <div class="card-body">
                @if($notifications->count())
                    <ul class="list-group list-group-flush">
                        @foreach($notifications as $n)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $n->data['title'] ?? ($n->data['type'] ?? __('Notification')) }}</strong>
                                    <div class="small text-muted">{{ $n->created_at->diffForHumans() }}</div>
                                    <div class="mt-1">{{ $n->data['message'] ?? '' }}</div>
                                </div>
                                <div class="text-end">
                                    @if(!$n->read_at)
                                        <form method="POST" action="{{ route('admin.notifications.read', $n->id) }}" class="admin-form">
                                            @csrf
                                            <button class="btn btn-sm btn-primary">{{ __('Mark read') }}</button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Read') }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-3">{{ $notifications->links() }}</div>
                @else
                    <div class="text-muted">{{ __('No notifications') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
