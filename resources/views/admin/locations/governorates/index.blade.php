@extends('layouts.admin')
@section('title', __('Governorates'))
@section('content')
<div class="page-header">
  <h3>{{ __('Governorates') }}</h3>
  <a href="{{ route('admin.governorates.create') }}" class="btn btn-sm btn-primary">{{ __('Add Governorate') }}</a>
</div>
<form method="GET" class="mb-3 js-auto-submit">
  <select name="country" class="form-select form-select-sm">
    <option value="">-- {{ __('All Countries') }} --</option>
    @foreach($countries as $c)
      <option value="{{ $c->id }}" {{ $countryId==$c->id? 'selected':'' }}>{{ $c->name }}</option>
    @endforeach
  </select>
</form>
<table class="table table-striped">
  <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Country') }}</th><th>{{ __('Active') }}</th><th></th></tr></thead>
  <tbody>
    @foreach($governorates as $g)
    <tr>
      <td>{{ $g->name }}</td>
      <td>{{ $g->country? $g->country->name : '' }}</td>
      <td>{{ $g->active?__('Yes'):__('No') }}</td>
      <td>
        <a href="{{ route('admin.governorates.edit',$g) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
        <form action="{{ route('admin.governorates.destroy',$g) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
{{ $governorates->links() }}
@endsection
