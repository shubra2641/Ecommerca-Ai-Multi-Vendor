@extends('layouts.admin')
@section('title', __('Edit Country'))
@section('content')
@include('admin.partials.page-header', ['title'=>__('Edit Country')])
<div class="card modern-card">
	<div class="card-header d-flex align-items-center gap-2">
		<h5 class="m-0">{{ __('Edit Country') }}</h5>
		<div class="ms-auto">
			<a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back') }}</a>
		</div>
	</div>

	<form class="admin-form" method="POST" action="{{ route('admin.countries.update',$country) }}">@csrf @method('PUT')
		<div class="card-body">
			<div class="mb-3">
				<label class="form-label">{{ __('Name') }}</label>
				<input name="name" class="form-control" value="{{ $country->name }}" required aria-label="{{ __('Name') }}">
			</div>

			<div class="mb-3">
				<label class="form-label">{{ __('ISO Code') }}</label>
				<input name="iso_code" class="form-control" value="{{ $country->iso_code }}" aria-label="{{ __('ISO Code') }}">
			</div>

			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" name="active" id="active" {{ $country->active? 'checked':'' }}>
				<label class="form-check-label" for="active">{{ __('Active') }}</label>
			</div>
		</div>

		<div class="card-footer text-end">
			<a href="{{ route('admin.countries.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
			<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
		</div>
	</form>
</div>
@endsection
