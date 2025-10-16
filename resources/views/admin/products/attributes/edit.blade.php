@extends('layouts.admin')

@section('title', __('Edit Attribute'))

@section('content')
<!-- Page Header -->
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Edit Attribute') }}</h1>
        <p class="page-description mb-0 text-muted">{{ $productAttribute->name }}</p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <span class="d-none d-sm-inline ms-1">{{ __('Back') }}</span>
        </a>
        <button type="submit" form="attribute-form" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <span class="d-none d-sm-inline ms-1">{{ __('Update Attribute') }}</span>
        </button>
    </div>
</div>

<!-- Attribute Information -->
<div class="card modern-card mb-4">
    <div class="card-header">
        <div>
            <h5 class="card-title mb-0">{{ __('Attribute Information') }}</h5>
            <small class="text-muted">{{ __('Update the attribute details below') }}</small>
        </div>
    </div>
    <div class="card-body">
        <form id="attribute-form" method="POST" action="{{ route('admin.product-attributes.update',$productAttribute) }}">
            @csrf 
            @method('PUT')
            @include('admin.products.attributes._form',['model'=>$productAttribute])
        </form>
    </div>
</div>

<!-- Attribute Values -->
<div class="card modern-card">
    <div class="card-header">
        <div>
            <h5 class="card-title mb-0">{{ __('Attribute Values') }}</h5>
            <small class="text-muted">{{ __('Manage the values for this attribute') }}</small>
        </div>
    </div>
    <div class="card-body">
        <!-- Add New Value Form -->
        <div class="add-value-section mb-4">
            <h6 class="mb-3">{{ __('Add New Value') }}</h6>
            <form method="POST" action="{{ route('admin.product-attributes.values.store',$productAttribute) }}" class="row g-3">
                @csrf
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">{{ __('Value') }}</label>
                    <input name="value" class="form-control" placeholder="{{ __('Enter value') }}" required>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">{{ __('Slug') }} <small class="text-muted">({{ __('optional') }})</small></label>
                    <input name="slug" class="form-control" placeholder="{{ __('Auto-generated if empty') }}" readonly>
                </div>
                <div class="col-lg-4 col-md-12">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-plus"></i>
                        <span class="ms-1">{{ __('Add Value') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Values List -->
        @if($productAttribute->values->count() > 0)
            <div class="values-list">
                <h6 class="mb-3">{{ __('Existing Values') }}</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
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
                                            <div class="d-md-none small text-muted mt-1">
                                                {{ __('Slug') }}: {{ $val->slug }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell text-muted small">{{ $val->slug }}</td>
                                    <td class="text-end">
                                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                                            <!-- Edit Form -->
                                            <form method="POST" action="{{ route('admin.product-attributes.values.update',[$productAttribute,$val]) }}" class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                                                @csrf 
                                                @method('PUT')
                                                <div class="d-flex gap-1">
                                                    <input name="value" value="{{ $val->value }}" class="form-control form-control-sm max-w-100" placeholder="{{ __('Value') }}">
                                                    <input name="slug" value="{{ $val->slug }}" class="form-control form-control-sm d-none d-lg-inline max-w-90" placeholder="{{ __('Slug') }}" readonly>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ __('Update') }}">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Delete Form -->
                                            <form method="POST" action="{{ route('admin.product-attributes.values.destroy',[$productAttribute,$val]) }}" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this value?') }}">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
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
            <div class="empty-state text-center py-4">
                <div class="empty-icon mb-3">
                    <i class="fas fa-list fa-2x text-muted"></i>
                </div>
                <h6 class="empty-title">{{ __('No values added yet') }}</h6>
                <p class="empty-description text-muted mb-0">{{ __('Add values using the form above to define selectable options for this attribute.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
