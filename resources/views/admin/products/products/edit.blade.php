@extends('layouts.admin')

@section('title', __('Edit Product'))

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Edit Product') }}</h1>
        <p class="page-description mb-0 text-muted">{{ __('Update product information') }}</p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span class="d-none d-sm-inline">{{ __('Back to Products') }}</span>
        </a>
        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-secondary">
            <i class="fas fa-eye"></i>
            <span class="d-none d-sm-inline">{{ __('View Product') }}</span>
        </a>
        <button type="submit" form="product-form" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <span class="d-none d-sm-inline">{{ __('Update') }}</span>
            <span class="d-sm-none">{{ __('Save') }}</span>
        </button>
    </div>
</div>

<div class="card modern-card content-card">
    <div class="content-card-header card-header">
        <div>
            <h3 class="content-title card-title mb-1">{{ __('Product Information') }}</h3>
            <p class="content-description text-muted mb-0">{{ __('Update the product details below') }}</p>
        </div>
    </div>
    <div class="content-card-body card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form id="product-form" method="POST" action="{{ route('admin.products.update', $product) }}"
            enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')
            @include('admin.products.products._form',['model'=>$product])
        </form>
    </div>
</div>
@endsection