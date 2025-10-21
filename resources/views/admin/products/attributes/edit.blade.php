@extends('layouts.admin')

@section('title', __('Edit Attribute'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13M18.5 2.5C18.8978 2.10218 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10218 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Edit Attribute') }}</h1>
                        <p class="admin-order-subtitle">{{ $productAttribute->name }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-attributes.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Back') }}
                </a>
                <button type="submit" form="attribute-form" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17 21V13H7V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7 3V8H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Update Attribute') }}
                </button>
            </div>
        </div>

        <!-- Attribute Information -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Attribute Information') }}
                </h3>
                <p class="admin-card-subtitle">{{ __('Update the attribute details below') }}</p>
            </div>
            <div class="admin-card-body">
                <form id="attribute-form" method="POST" action="{{ route('admin.product-attributes.update',$productAttribute) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.products.attributes._form',['model'=>$productAttribute])
                </form>
            </div>
        </div>

        <!-- Attribute Values -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ __('Attribute Values') }}
                </h3>
                <p class="admin-card-subtitle">{{ __('Manage the values for this attribute') }}</p>
            </div>
            <div class="admin-card-body">
                <!-- Add New Value Form -->
                <div class="add-value-section mb-4">
                    <h6 class="mb-3">{{ __('Add New Value') }}</h6>
                    <form method="POST" action="{{ route('admin.product-attributes.values.store',$productAttribute) }}" class="admin-form-grid">
                        @csrf
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Value') }}</label>
                            <input name="value" class="admin-form-input" placeholder="{{ __('Enter value') }}" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Slug') }} <small class="admin-text-muted">({{ __('optional') }})</small></label>
                            <input name="slug" class="admin-form-input" placeholder="{{ __('Auto-generated if empty') }}" readonly>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label d-block">&nbsp;</label>
                            <button type="submit" class="admin-btn admin-btn-success w-100">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Add Value') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Values List -->
                @if($productAttribute->values->count() > 0)
                <div class="values-list">
                    <h6 class="mb-3">{{ __('Existing Values') }}</h6>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Value') }}</th>
                                    <th class="d-none d-md-table-cell">{{ __('Slug') }}</th>
                                    <th class="text-end w-200">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productAttribute->values as $val)
                                <tr>
                                    <td>
                                        <div class="value-display">
                                            <strong>{{ $val->value }}</strong>
                                            <div class="d-md-none admin-text-muted small mt-1">
                                                {{ __('Slug') }}: {{ $val->slug }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell admin-text-muted small">{{ $val->slug }}</td>
                                    <td class="text-end">
                                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                                            <!-- Edit Form -->
                                            <form method="POST" action="{{ route('admin.product-attributes.values.update',[$productAttribute,$val]) }}" class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                                                @csrf
                                                @method('PUT')
                                                <div class="d-flex gap-1">
                                                    <input name="value" value="{{ $val->value }}" class="admin-form-input admin-form-input-sm max-w-100" placeholder="{{ __('Value') }}">
                                                    <input name="slug" value="{{ $val->slug }}" class="admin-form-input admin-form-input-sm d-none d-lg-inline max-w-90" placeholder="{{ __('Slug') }}" readonly>
                                                </div>
                                                <button type="submit" class="admin-btn admin-btn-small admin-btn-outline" title="{{ __('Update') }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M17 21V13H7V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M7 3V8H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Delete Form -->
                                            <form method="POST" action="{{ route('admin.product-attributes.values.destroy',[$productAttribute,$val]) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this value?') }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-btn admin-btn-small admin-btn-danger" title="{{ __('Delete') }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="admin-empty-state text-center py-4">
                    <div class="admin-notification-icon mb-3">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h6>{{ __('No values added yet') }}</h6>
                    <p class="admin-text-muted mb-0">{{ __('Add values using the form above to define selectable options for this attribute.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection