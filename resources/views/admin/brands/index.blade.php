@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1 class="mb-0">{{ __('Brands') }}</h1>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">{{ __('Create Brand') }}</a>
    </div>
    <div class="card modern-card">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>{{ __('ID') }}</th><th>{{ __('Name') }}</th><th>{{ __('Active') }}</th><th>{{ __('Actions') }}</th></tr></thead>
                <tbody>
                    @foreach($brands as $brand)
                    <tr>
                        <td>{{ $brand->id }}</td>
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->active ? __('Active') : __('Inactive') }}</td>
                        <td>
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-secondary">{{ __('Edit') }}</a>
                            <form method="post" action="{{ route('admin.brands.destroy', $brand) }}" class="d-inline-block js-confirm-delete" data-confirm="{{ __('Delete?') }}">@csrf @method('delete')
                                <button class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $brands->links() }}
        </div>
    </div>
</div>
@endsection