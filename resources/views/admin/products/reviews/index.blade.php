@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h3>{{ __('Product Reviews') }}</h3>
    <div class="card modern-card">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-striped mb-0">
        <thead><tr><th>{{ __('ID') }}</th><th>{{ __('Product') }}</th><th>{{ __('User') }}</th><th>{{ __('Rating') }}</th><th>{{ __('Title') }}</th><th>{{ __('Images') }}</th><th>{{ __('Created') }}</th><th>{{ __('Approved') }}</th><th>{{ __('Actions') }}</th></tr></thead>
        <tbody>
        @foreach($reviews as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->product?->name }}</td>
                <td>{{ $r->user?->email ?? __('Guest') }}</td>
                <td>{{ $r->rating }}</td>
                <td>{{ Str::limit($r->title,40) }}</td>
                <td>
                    @if($r->images && count($r->images)>0)
                        @foreach($r->images as $img)
                            <img src="{{ asset($img) }}" class="me-1 rounded obj-cover w-48 h-48" />
                        @endforeach
                    @endif
                </td>
                <td>{{ $r->created_at->format('Y-m-d') }}</td>
                <td>{{ $r->approved ? __('Yes') : __('No') }}</td>
                <td>
                    <a href="{{ route('admin.reviews.show',$r) }}" class="btn btn-sm btn-secondary">{{ __('View') }}</a>
                    @if($r->approved)
                        <form method="post" action="{{ route('admin.reviews.unapprove',$r) }}" class="d-inline">@csrf<button class="btn btn-sm btn-warning">{{ __('Unapprove') }}</button></form>
                    @else
                        <form method="post" action="{{ route('admin.reviews.approve',$r) }}" class="d-inline-block">@csrf<button class="btn btn-sm btn-success">{{ __('Approve') }}</button></form>
                    @endif
-                    <form method="post" action="{{ route('admin.reviews.destroy',$r) }}" class="d-inline">@csrf @method('delete')<button class="btn btn-sm btn-danger">{{ __('Delete') }}</button></form>
+                    <form method="post" action="{{ route('admin.reviews.destroy',$r) }}" class="d-inline-block js-confirm-delete" data-confirm="{{ __('Delete?') }}">@csrf @method('delete')<button class="btn btn-sm btn-danger">{{ __('Delete') }}</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $reviews->links() }}
        </div>
    </div>

{{-- Confirmation behavior provided centrally in admin.js; no per-page script imports allowed. --}}
</div>
@endsection
