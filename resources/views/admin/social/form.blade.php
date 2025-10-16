@extends('layouts.admin')

@section('title', $link->exists ? __('Edit Social Link') : __('Add Social Link'))

@section('content')
@include('admin.partials.page-header', ['title'=>$link->exists ? __('Edit Social Link') : __('Add Social Link'),'actions'=>'<a href="'.route('admin.social.index').'" class="btn btn-secondary">'.__('Back').'</a>'])

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ $link->exists ? route('admin.social.update', $link) : route('admin.social.store') }}" novalidate>
    @csrf
    @if($link->exists)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">@lang('Platform') <span class="text-danger">*</span></label>
            <input type="text" name="platform" class="form-control" value="{{ old('platform', $link->platform) }}" required maxlength="50">
        </div>
        <div class="col-md-4">
            <label class="form-label">@lang('Label')</label>
            <input type="text" name="label" class="form-control" value="{{ old('label', $link->label) }}" maxlength="100" placeholder="@lang('Optional display text')">
        </div>
        <div class="col-md-4">
            <label class="form-label">@lang('URL') <span class="text-danger">*</span></label>
            <input type="url" name="url" class="form-control" value="{{ old('url', $link->url) }}" required maxlength="255" placeholder="https://">
        </div>
        <div class="col-md-4">
            <label class="form-label">@lang('Icon')</label>
            <select name="icon" class="form-select">
                @foreach(($socialIcons ?? []) as $class => $label)
                    <option value="{{ $class }}" @selected(($socialCurrentIcon ?? '') === $class)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-text">@lang('Select an icon to display')</div>
        </div>
        <div class="col-md-2">
            <label class="form-label">@lang('Order')</label>
            <input type="number" name="order" class="form-control" value="{{ old('order', $link->order) }}" min="0" max="9999">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $link->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">@lang('Active')</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary">{{ $link->exists ? __('Update') : __('Create') }}</button>
    </div>
</form>
@endsection
