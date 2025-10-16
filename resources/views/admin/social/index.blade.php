@extends('layouts.admin')

@section('title', __('Social Links'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title mb-1">@lang('Social Links')</h1>
        <p class="text-muted mb-0">@lang('Manage social media links and their display order')</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.social.create') }}" class="btn btn-primary">@lang('Add Link')</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card modern-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">@lang('Social Links')</h5>
        <a href="{{ route('admin.social.create') }}" class="btn btn-sm btn-primary">@lang('Add Link')</a>
    </div>
    <div class="card-body">
        @if(!$links->count())
            <div class="text-center text-muted py-4">
                <i class="fas fa-share-alt fa-3x mb-3 text-muted"></i>
                <p class="mb-3">@lang('No social links yet. Click Add Link to create one.')</p>
                <a href="{{ route('admin.social.create') }}" class="btn btn-primary">@lang('Add First Link')</a>
            </div>
        @else
        <form method="post" action="{{ route('admin.social.reorder') }}" id="reorder-form">
            @csrf
            <div class="table-responsive">
                <table class="table table-striped" id="social-links-table">
            <thead>
                <tr>
                    <th class="w-40"></th>
                    <th>@lang('Platform')</th>
                    <th>@lang('Label')</th>
                    <th>@lang('URL')</th>
                    <th>@lang('Icon')</th>
                    <th>@lang('Active')</th>
                    <th class="text-end w-160">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody id="sortable-body">
                @foreach($links as $link)
                <tr data-id="{{ $link->id }}">
                    <td class="text-muted cursor-move"><i class="fas fa-grip-vertical"></i></td>
                    <td>{{ $link->platform }}</td>
                    <td>{{ $link->label }}</td>
                    <td><a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->url }}</a></td>
                    <td>@if($link->icon)<i class="{{ $link->icon }}" title="{{ $link->platform }}"></i>@endif</td>
                    <td>
                        @if($link->is_active)
                            <span class="badge bg-success">@lang('Yes')</span>
                        @else
                            <span class="badge bg-secondary">@lang('No')</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.social.edit', $link) }}" class="btn btn-outline-secondary">@lang('Edit')</a>
                            <form action="{{ route('admin.social.destroy', $link) }}" method="post" class="d-inline-block js-confirm" data-confirm="@lang('Are you sure you want to delete this social link?')">
                            @csrf
                            @method('DELETE')
                                <button class="btn btn-outline-danger">@lang('Delete')</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-secondary" id="save-order" disabled>@lang('Save Order')</button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/js/social-links.js') }}" defer></script>
@endpush
