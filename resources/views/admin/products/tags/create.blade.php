@extends('layouts.admin')

@section('title', __('Add Tag'))

@section('content')
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-8">
        <h1 class="page-title mb-1">{{ __('Add Tag') }}</h1>
        <p class="text-muted mb-0">{{ __('Create a new product tag') }}</p>
    </div>
    <div class="col-12 col-md-4 mt-3 mt-md-0">
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
            <a href="{{ route('admin.product-tags.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                <span class="d-none d-sm-inline">{{ __('Back') }}</span>
                <span class="d-sm-none">{{ __('Back') }}</span>
            </a>
            <button type="submit" form="tag-form" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                <span class="d-none d-sm-inline">{{ __('Save Tag') }}</span>
                <span class="d-sm-none">{{ __('Save') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="card modern-card">
    <div class="card-header">
        <div>
            <h5 class="card-title mb-0">{{ __('Tag Information') }}</h5>
            <small class="text-muted">{{ __('Fill in the tag details below') }}</small>
        </div>
    </div>
    <div class="card-body">
        <form id="tag-form" method="POST" action="{{ route('admin.product-tags.store') }}">
            @csrf
            @include('admin.products.tags._form')
        </form>
    </div>
</div>
@endsection
