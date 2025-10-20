@extends('layouts.admin')

@section('title', __('Edit Country'))

@section('content')
<section class="admin-order-details-modern">
	<div class="admin-order-wrapper">

		<!-- Header Section -->
		<div class="admin-order-header">
			<div class="header-left">
				<h1 class="admin-order-title">
					<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
					</svg>
					{{ __('Edit Country') }}
				</h1>
				<p class="admin-order-subtitle">{{ __('Update country information') }}</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-secondary">
					<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					{{ __('Back') }}
				</a>
			</div>
		</div>

		<form method="POST" action="{{ route('admin.countries.update',$country) }}" class="admin-form">
			@csrf @method('PUT')

			<!-- Country Details -->
			<div class="admin-modern-card">
				<div class="admin-card-header">
					<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
					<h3 class="admin-card-title">{{ __('Country Details') }}</h3>
				</div>
				<div class="admin-card-body">
					<div class="admin-form-grid">
						<div class="admin-form-group admin-form-group-wide">
							<label class="admin-form-label">{{ __('Name') }}</label>
							<input name="name" class="admin-form-input" value="{{ $country->name }}" required aria-label="{{ __('Name') }}">
						</div>
						<div class="admin-form-group">
							<label class="admin-form-label">{{ __('ISO Code') }}</label>
							<input name="iso_code" class="admin-form-input" value="{{ $country->iso_code }}" aria-label="{{ __('ISO Code') }}">
						</div>
						<div class="admin-form-group admin-flex-end">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" name="active" id="active" {{ $country->active ? 'checked' : '' }}>
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
						<a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-secondary">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="admin-btn admin-btn-success admin-btn-large">
							<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path d="M5 13l4 4L19 7" />
							</svg>
							{{ __('Save Changes') }}
						</button>
					</div>
				</div>
			</div>

		</form>

	</div>
</section>
@endsection