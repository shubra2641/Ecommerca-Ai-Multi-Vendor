@extends('layouts.admin')
@section('content')
@include('admin.partials.page-header', ['title'=>__('Coupons'),'actions'=>'<a href="'.route('admin.coupons.create').'" class="btn btn-primary">'.__('Create Coupon').'</a>'])
<div class="card modern-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table mt-3">
    <thead>
        <tr>
            <th>{{ __('Code') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Value') }}</th>
            <th>{{ __('Uses') }}</th>
            <th>{{ __('Expires') }}</th>
            <th>{{ __('Active') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($coupons as $coupon)
        <tr>
            <td>{{ $coupon->code }}</td>
            <td>{{ ucfirst($coupon->type) }}</td>
            <td>{{ $coupon->value }}</td>
            <td>{{ $coupon->uses }}</td>
            <td>{{ $coupon->ends_at }}</td>
            <td>{{ $coupon->active ? __('Active') : __('Inactive') }}</td>
            <td>
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-secondary">{{ __('Edit') }}</a>
                <form method="post" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline-block js-confirm-delete" data-confirm="{{ __('Delete?') }}">@csrf @method('delete')
                    <button class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
            </table>
        </div>
        {{ $coupons->links() }}
    </div>
</div>
@section('scripts')
<script src="{{ asset('admin/js/confirm-delete.js') }}"></script>
@endsection
@endsection