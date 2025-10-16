@extends('layouts.admin')
@section('title', __('Countries'))
@section('content')
<div class="page-header">
  <h3>{{ __('Countries') }}</h3>
  <a href="{{ route('admin.countries.create') }}" class="btn btn-sm btn-primary">{{ __('Add Country') }}</a>
</div>
<table class="table table-striped">
  <thead><tr><th>{{ __('Name') }}</th><th>{{ __('ISO') }}</th><th>{{ __('Active') }}</th><th></th></tr></thead>
  <tbody>
    @foreach($countries as $c)
    <tr>
      <td>{{ $c->name }}</td>
      <td>{{ $c->iso_code }}</td>
      <td>{{ $c->active?__('Yes'):__('No') }}</td>
      <td>
        <a href="{{ route('admin.countries.edit',$c) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
        <form action="{{ route('admin.countries.destroy',$c) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
{{ $countries->links() }}
@endsection
