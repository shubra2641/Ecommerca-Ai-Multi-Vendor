@extends('layouts.admin')
@section('content')
<div class="container">
    <h3>{{ __('Serials for:') }} {{ $product->name }}</h3>
    <div class="mb-3">
        <form method="post" action="{{ route('admin.products.serials.import',$product) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
                <label class="form-label small">{{ __('Upload CSV / TXT (one serial per line)') }}</label>
                <input type="file" name="file" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label small">{{ __('Or paste serials (one per line)') }}</label>
                <textarea name="serials" class="form-control" rows="4"></textarea>
            </div>
            <button class="btn btn-primary">{{ __('Import') }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.products.serials.export',$product) }}">{{ __('Export CSV') }}</a>
        </form>
    </div>
    <table class="table table-striped">
        <thead><tr><th>{{ __('ID') }}</th><th>{{ __('Serial') }}</th><th>{{ __('Sold At') }}</th><th>{{ __('Actions') }}</th></tr></thead>
        <tbody>
            @foreach($serials as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->serial }}</td>
                <td>{{ $s->sold_at }}</td>
                <td>
                    @if(!$s->sold_at)
                    <form method="post" action="{{ route('admin.products.serials.markSold',[$product,$s]) }}" class="d-inline">@csrf<button class="btn btn-sm btn-success">{{ __('Mark Sold') }}</button></form>
                    @else
                    <span class="text-muted">{{ __('Sold') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $serials->links() }}
</div>
@endsection
