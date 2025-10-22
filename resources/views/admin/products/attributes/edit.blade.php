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
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Edit Attribute') }}</h1>
                        <p class="admin-order-subtitle">{{ $productAttribute->name }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-attributes.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
                <button type="submit" form="attribute-form" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('Update Attribute') }}
                </button>
            </div>
        </div>

        <!-- Attribute Information -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-list"></i>
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
                    <i class="fas fa-check"></i>
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
                                <i class="fas fa-plus"></i>
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
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>

                                            <!-- Delete Form -->
                                            <form method="POST" action="{{ route('admin.product-attributes.values.destroy',[$productAttribute,$val]) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this value?') }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-btn admin-btn-small admin-btn-danger" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
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
                        <i class="fas fa-list"></i>
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