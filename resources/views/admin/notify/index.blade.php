@extends('layouts.admin')
@section('title', __('Product Notifications'))
@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">{{ __('Product Notifications') }}</h1>
    @isset($breakdown)
        @include('admin.notify.summary',['breakdown'=>$breakdown])
    @endisset
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-2"><input name="email" value="{{ request('email') }}" class="form-control"
                placeholder="{{ __('Email') }}"></div>
        <div class="col-md-2"><input name="product" value="{{ request('product') }}" class="form-control"
                placeholder="{{ __('Product ID') }}"></div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">{{ __('Status') }}</option>
                @foreach(['pending','notified','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="type" class="form-select">
                <option value="">{{ __('Type') }}</option>
                @foreach(['stock','back_in_stock','price_drop'] as $t)
                <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">{{ __('Filter') }}</button></div>
    </form>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Created') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($interests as $interest)
                <tr>
                    <td>{{ $interest->id }}</td>
                    <td><a
                            href="{{ route('admin.products.show',$interest->product_id) }}">#{{ $interest->product_id }}</a>
                        {{ $interest->product?->name }}</td>
                    <td>{{ $interest->phone }}</td>
                    <td>{{ $interest->email }}</td>
                    <td>{{ $interest->type }}</td>
                    <td><span
                            class="badge bg-{{ $interest->status==='pending'?'warning':($interest->status==='notified'?'success':'secondary') }}">{{ $interest->status }}</span>
                    </td>
                    <td>{{ $interest->created_at->diffForHumans() }}</td>
                    <td class="d-flex gap-1">
                        @if($interest->status==='pending')
                        <form method="post" action="{{ route('admin.notify.mark',$interest) }}">@csrf @method('put')
                            <button class="btn btn-sm btn-outline-success">{{ __('Mark') }}</button>
                        </form>
                        @endif
                        <form method="post" action="{{ route('admin.notify.delete',$interest) }}" class="js-confirm" data-confirm="{{ __('Delete?') }}">@csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger">&times;</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">{{ __('No interests') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $interests->links() }}</div>
</div>
@endsection