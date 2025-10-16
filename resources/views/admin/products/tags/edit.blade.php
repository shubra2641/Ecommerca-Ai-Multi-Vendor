@extends('layouts.admin')

@section('title', __('Edit Tag'))

@section('content')
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-8">
        <h1 class="page-title mb-1">{{ __('Edit Tag') }}</h1>
        <p class="text-muted mb-0">{{ $productTag->name }}</p>
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
                <span class="d-none d-sm-inline">{{ __('Update Tag') }}</span>
                <span class="d-sm-none">{{ __('Update') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="card modern-card">
    <div class="card-header">
        <div>
            <h5 class="card-title mb-0">{{ __('Tag Information') }}</h5>
            <small class="text-muted">{{ __('Update the tag details below') }}</small>
        </div>
    </div>
    <div class="card-body">
        <form id="tag-form" method="POST" action="{{ route('admin.product-tags.update', $productTag) }}">
            @csrf
            @method('PUT')
            @include('admin.products.tags._form', ['model' => $productTag])
        </form>
    </div>
</div>
@endsection
