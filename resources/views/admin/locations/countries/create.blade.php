@extends('layouts.admin')

@section('title', __('Add Country'))

@section('content')
<section class="admin-order-details-modern">
	<div class="admin-order-wrapper">

		<!-- Header Section -->
		<div class="admin-order-header">
			<div class="header-left">
				<h1 class="admin-order-title">
					<i class="fas fa-plus"></i>
					{{ __('Add Country') }}
				</h1>
				<p class="admin-order-subtitle">{{ __('Create a new country') }}</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-secondary">
					<i class="fas fa-arrow-left"></i>
					{{ __('Back') }}
				</a>
			</div>
		</div>

		<form method="POST" action="{{ route('admin.countries.store') }}" class="admin-form">
			@csrf

			<!-- Country Details -->
			<div class="admin-modern-card">
				<div class="admin-card-header">
					<i class="fas fa-clipboard-list"></i>
					<h3 class="admin-card-title">{{ __('Country Details') }}</h3>
				</div>
				<div class="admin-card-body">
					<div class="admin-form-grid">
						<div class="admin-form-group admin-form-group-wide">
							<label class="admin-form-label">{{ __('Name') }}</label>
							<input name="name" class="admin-form-input" required aria-label="{{ __('Name') }}">
						</div>
						<div class="admin-form-group">
							<label class="admin-form-label">{{ __('ISO Code') }}</label>
							<input name="iso_code" class="admin-form-input" aria-label="{{ __('ISO Code') }}">
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
						<a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-secondary">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="admin-btn admin-btn-success admin-btn-large">
							<i class="fas fa-check"></i>
							{{ __('Save') }}
						</button>
					</div>
				</div>
			</div>

		</form>

	</div>
</section>
@endsection