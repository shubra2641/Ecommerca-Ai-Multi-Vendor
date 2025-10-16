@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="card modern-card">

                <div class="page-header">
        <h1 class="mb-0">{{ __('Edit Brands') }}</h1>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-primary">{{ __('All Brand') }}</a>
    </div>

        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0">{{ __('Edit Brand') }}</h5>
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('admin.brands.update', $brand) }}" class="admin-form">
                @csrf
                @method('put')
                <div class="mb-3">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $brand->name) }}" />
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Slug') }}</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $brand->slug) }}" />
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="active" id="active" class="form-check-input" value="1" {{ old('active', $brand->active) ? 'checked' : '' }} />
                    <label class="form-check-label" for="active">{{ __('Active') }}</label>
                </div>
                <div>
                    <button class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
