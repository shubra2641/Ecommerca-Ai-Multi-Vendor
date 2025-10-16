@extends('layouts.admin')
@section('title', __('Cities'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>{{ __('Cities') }}</h3>
  <a href="{{ route('admin.cities.create') }}" class="btn btn-sm btn-primary">{{ __('Add City') }}</a>
</div>
<form method="GET" class="mb-3 js-auto-submit">
  <select name="governorate" class="form-select form-select-sm">
    <option value="">-- {{ __('All Governorates') }} --</option>
    @foreach($governorates as $g)
      <option value="{{ $g->id }}" {{ $govId==$g->id? 'selected':'' }}>{{ $g->name }}</option>
    @endforeach
  </select>
</form>
<table class="table table-striped">
  <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Governorate') }}</th><th>{{ __('Active') }}</th><th></th></tr></thead>
  <tbody>
    @foreach($cities as $c)
    <tr>
      <td>{{ $c->name }}</td>
      <td>{{ $c->governorate? $c->governorate->name : '' }}</td>
      <td>{{ $c->active?__('Yes'):__('No') }}</td>
      <td>
        <a href="{{ route('admin.cities.edit',$c) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
        <form action="{{ route('admin.cities.destroy',$c) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
{{ $cities->links() }}
@endsection
