@extends('layouts.admin')

@section('title', __('Add Attribute'))

@section('content')
<!-- Page Header -->
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Add Attribute') }}</h1>
        <p class="page-description mb-0 text-muted">{{ __('Define a new attribute for product variations') }}</p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <span class="d-none d-sm-inline ms-1">{{ __('Back') }}</span>
        </a>
        <button type="submit" form="attribute-form" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <span class="d-none d-sm-inline ms-1">{{ __('Save Attribute') }}</span>
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="card modern-card">
    <div class="card-header">
        <div>
            <h5 class="card-title mb-0">{{ __('Attribute Information') }}</h5>
            <small class="text-muted">{{ __('Fill in the attribute details below') }}</small>
        </div>
    </div>
    <div class="card-body">
        <form id="attribute-form" method="POST" action="{{ route('admin.product-attributes.store') }}">
            @csrf
            @include('admin.products.attributes._form')
        </form>
    </div>
</div>
@endsection