@extends('vendor.layout')

@section('title', __('Add Product'))

@section('content')
    @include('admin.partials.page-header', [
        'title' => __('Add Product'),
        'subtitle' => __('Create a new product for your store'),
        'actions' => '<a href="'.route('vendor.products.index').'" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">'.e(__('Back to Products')).'</span></a> <button type="submit" form="product-form" class="btn btn-primary"><i class="fas fa-save"></i> '.e(__('Save Product')).'</button>'
    ])

    <div class="card modern-card content-card">
        <div class="content-card-header card-header">
            <div>
                <h3 class="content-title card-title mb-1">{{ __('Product Information') }}</h3>
                <p class="content-description text-muted mb-0">{{ __('Fill in the product details below') }}</p>
            </div>
        </div>
        <div class="content-card-body card-body">
            <form id="product-form" method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @include('admin.products.products._form', [
                    'model' => null,
                    'categories' => $categories ?? \App\Models\ProductCategory::all(),
                    'tags' => $tags ?? \App\Models\Tag::all(),
                    'attributes' => $attributes ?? \App\Models\ProductAttribute::with('values')->get(),
                    'isVendorForm' => true,
                ])
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    @include('admin.products.products._script')
@endsection


