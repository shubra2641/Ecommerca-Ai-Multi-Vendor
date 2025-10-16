@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h3>{{ __('Review') }} #{{ $review->id }}</h3>
    <div class="card modern-card">
        <div class="card-body">
            <h4>{{ __('Product') }}: {{ $review->product?->name }}</h4>
            <p><strong>{{ __('User') }}:</strong> {{ $review->user?->email ?? __('Guest') }} ({{ $review->user?->name ?? __('Guest') }})</p>
            <p><strong>{{ __('Rating') }}:</strong> {{ $review->rating }}</p>
            <p><strong>{{ __('Comment') }}:</strong></p>
            <div class="bg-light p-3 rounded">{{ $review->comment }}</div>
            @if($review->images && count($review->images) > 0)
                <h5 class="mt-3">{{ __('Images') }}</h5>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($review->images as $img)
                        <img src="{{ asset($img) }}" class="rounded obj-cover w-120 h-120" />
                    @endforeach
                </div>
            @endif
            <div class="mt-3">
                @if(!$review->approved)
                    <form method="post" action="{{ route('admin.reviews.approve',$review) }}" class="d-inline-block">@csrf<button class="btn btn-success">{{ __('Approve') }}</button></form>
                @else
                    <form method="post" action="{{ route('admin.reviews.unapprove',$review) }}" class="d-inline-block">@csrf<button class="btn btn-warning">{{ __('Unapprove') }}</button></form>
                @endif
                <form method="post" action="{{ route('admin.reviews.destroy',$review) }}" class="d-inline-block js-confirm" data-confirm="{{ __('Delete?') }}">@csrf @method('delete')<button class="btn btn-danger">{{ __('Delete') }}</button></form>
            </div>
        </div>
    </div>
</div>
@endsection
