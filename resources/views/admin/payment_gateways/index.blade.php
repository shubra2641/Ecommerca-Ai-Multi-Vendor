@extends('layouts.admin')

@section('title', __('Payment Gateways'))

@section('content')
<div class="page-header">
    <h1>{{ __('Payment Gateways') }}</h1>
    <a href="{{ route('admin.payment-gateways.create') }}" class="btn btn-primary">{{ __('Add Gateway') }}</a>
</div>



<table class="table">
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Driver') }}</th>
            <th>{{ __('Enabled') }}</th>
            <th>{{ __('Requires Image') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($gateways as $g)
        <tr>
            <td>{{ $g->name }}</td>
            <td>{{ $g->driver }}</td>
            <td>{{ $g->enabled ? __('Yes') : __('No') }}</td>
            <td>{{ $g->requires_transfer_image ? __('Yes') : __('No') }}</td>
            <td>
                <a href="{{ route('admin.payment-gateways.edit', $g->id) }}"
                    class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                <form action="{{ route('admin.payment-gateways.toggle', $g->id) }}" method="POST"
                    class="d-inline-block">
                    @csrf
                    <button
                        class="btn btn-sm {{ $g->enabled ? 'btn-success' : 'btn-outline-secondary' }}">{{ $g->enabled ? __('Enabled') : __('Enable') }}</button>
                </form>
                <form action="{{ route('admin.payment-gateways.destroy', $g->id) }}" method="POST"
                    class="d-inline-block js-confirm-delete" data-confirm="{{ __('Are you sure?') }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
