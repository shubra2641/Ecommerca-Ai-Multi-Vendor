@extends('layouts.admin')
@section('title', __('Top Interested Products'))
@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">{{ __('Top Interested Products') }}</h1>
    <form class="row g-2 mb-3">
        <div class="col-auto"><label class="col-form-label">Limit</label></div>
        <div class="col-auto"><input type="number" name="limit" value="{{ $limit }}" class="form-control" min="5" max="200"></div>
        <div class="col-auto"><button class="btn btn-primary">{{ __('Apply') }}</button></div>
    </form>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>#</th><th>{{ __('Product') }}</th><th>{{ __('Interests') }}</th><th>{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @foreach($rows as $i=>$row)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>@if(($ntpResolvedProducts[$i] ?? null))<a href="{{ route('admin.products.show',$ntpResolvedProducts[$i]->id) }}">{{ $ntpResolvedProducts[$i]->name }}</a>@else #{{ $row->product_id }} @endif</td>
                    <td>{{ $row->total }}</td>
                    <td>@if($p)<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.notify.priceChart',$p) }}">{{ __('Price Chart') }}</a>@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection