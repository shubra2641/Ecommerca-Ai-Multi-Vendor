@extends('layouts.admin')
@section('title', __('Add City'))
@section('content')
@include('admin.partials.page-header', ['title'=>__('Add City')])
<div class="card modern-card">
	<div class="card-header d-flex align-items-center gap-2">
		<h5 class="m-0">{{ __('Add City') }}</h5>
		<div class="ms-auto">
			<a href="{{ route('admin.cities.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back') }}</a>
		</div>
	</div>

	<form class="admin-form" method="POST" action="{{ route('admin.cities.store') }}">@csrf
		<div class="card-body">
			<div class="mb-3">
				<label class="form-label">{{ __('Governorate') }}</label>
				<select name="governorate_id" class="form-select" required aria-label="{{ __('Governorate') }}">
					@foreach($governorates as $g)
						<option value="{{ $g->id }}">{{ $g->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="mb-3">
				<label class="form-label">{{ __('Name') }}</label>
				<input name="name" class="form-control" required aria-label="{{ __('Name') }}">
			</div>

			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" name="active" id="active" checked>
				<label class="form-check-label" for="active">{{ __('Active') }}</label>
			</div>
		</div>

		<div class="card-footer text-end">
			<a href="{{ route('admin.cities.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
			<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
		</div>
	</form>
</div>
@endsection
