@extends('layouts.admin')
@section('title', __('Shipping'))
@section('content')
@include('admin.partials.page-header', ['title'=>__('Shipping Groups'),'actions'=>'<a href="'.route('admin.shipping.create').'" class="btn btn-primary">'.__('Create').'</a>'])
<div class="card modern-card">
    <div class="card-header d-flex align-items-center gap-2">
        <h3 class="card-title mb-0">{{ __('Shipping Groups') }}</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Default Price') }}</th>
            <th>{{ __('Days') }}</th>
            <th>{{ __('Active') }}</th>
            <th>{{ __('Locations Sample') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $g)
            <tr>
                <td>{{ $g->id }}</td>
                <td>{{ $g->name }}</td>
                <td>{{ $g->default_price ?? '-' }}</td>
                <td>{{ $g->estimated_days ?? '-' }}</td>
                <td><span class="badge bg-{{ $g->active? 'success':'secondary' }}">{{ $g->active? __('Yes'):__('No') }}</span></td>
                <td>
                    @foreach(($shippingLocationSamples[$g->id] ?? collect()) as $loc)
                        <div>{{ $loc->country?->name ?? '-' }} - {{ $loc->governorate?->name ?? '-' }} - {{ $loc->city?->name ?? '-' }}</div>
                    @endforeach
                </td>
                <td class="text-end">
                    <a href="{{ route('admin.shipping.edit', $g) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                </td>
            </tr>
        @endforeach
    </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            {{-- pagination could go here --}} 
        </div>
    </div>
</div>
@endsection
