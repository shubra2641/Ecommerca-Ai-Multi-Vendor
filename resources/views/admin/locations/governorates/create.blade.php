@extends('layouts.admin')

@section('title', __('Add Governorate'))

@section('content')
<section class="admin-order-details-modern">
	<div class="admin-order-wrapper">

		<!-- Header Section -->
		<div class="admin-order-header">
			<div class="header-left">
				<h1 class="admin-order-title">
					<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path d="M12 4v16m8-8H4" />
					</svg>
					{{ __('Add Governorate') }}
				</h1>
				<p class="admin-order-subtitle">{{ __('Create a new governorate') }}</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('admin.governorates.index') }}" class="admin-btn admin-btn-secondary">
					<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					{{ __('Back') }}
				</a>
			</div>
		</div>

		<form method="POST" action="{{ route('admin.governorates.store') }}" class="admin-form">
			@csrf

			<!-- Governorate Details -->
			<div class="admin-modern-card">
				<div class="admin-card-header">
					<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
					<h3 class="admin-card-title">{{ __('Governorate Details') }}</h3>
				</div>
				<div class="admin-card-body">
					<div class="admin-form-grid">
						<div class="admin-form-group">
							<label class="admin-form-label">{{ __('Country') }}</label>
							<select name="country_id" class="admin-form-select" required aria-label="{{ __('Country') }}">
								@foreach($countries as $c)
								<option value="{{ $c->id }}">{{ $c->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="admin-form-group">
							<label class="admin-form-label">{{ __('Name') }}</label>
							<input name="name" class="admin-form-input" required aria-label="{{ __('Name') }}">
						</div>
						<div class="admin-form-group admin-flex-end">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" name="active" id="active" checked>
								<label class="form-check-label" for="active">{{ __('Active') }}</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Actions -->
			<div class="admin-modern-card">
				<div class="admin-card-body">
					<div class="admin-form-actions">
						<a href="{{ route('admin.governorates.index') }}" class="admin-btn admin-btn-secondary">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="admin-btn admin-btn-success admin-btn-large">
							<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path d="M5 13l4 4L19 7" />
							</svg>
							{{ __('Save') }}
						</button>
					</div>
				</div>
			</div>

		</form>

	</div>
</section>
@endsection