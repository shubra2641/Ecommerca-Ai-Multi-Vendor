@extends('layouts.admin')
@section('title', __('Shipping Zones'))
@section('content')
@include('admin.partials.page-header', ['title'=>__('Shipping Zones'),'actions'=>'<a href="'.route('admin.shipping-zones.create').'" class="btn btn-primary">'.__('Create').'</a>'])
<div class="card modern-card">
   <div class="card-header d-flex align-items-center gap-2">
      <h3 class="card-title mb-0">{{ __('Shipping Zones') }}</h3>
   </div>
   <div class="card-body p-0">
      <div class="table-responsive">
         <table class="table table-sm mb-0">
            <thead><tr><th>{{ __('ID') }}</th><th>{{ __('Name') }}</th><th>{{ __('Code') }}</th><th>{{ __('Rules') }}</th><th>{{ __('Active') }}</th><th></th></tr></thead>
            <tbody>
            @foreach($zones as $z)
               <tr>
                  <td>{{ $z->id }}</td>
                  <td>{{ $z->name }}</td>
                  <td>{{ $z->code ?? '-' }}</td>
                  <td>{{ $z->rules_count }}</td>
                  <td>
                     @if($z->active)
                        <span class="badge bg-success">{{ __('Yes') }}</span>
                     @else
                        <span class="badge bg-secondary">{{ __('No') }}</span>
                     @endif
                  </td>
                  <td class="text-nowrap">
                     <a href="{{ route('admin.shipping-zones.edit',$z) }}" class="btn btn-sm btn-secondary">{{ __('Edit') }}</a>
                     <form method="POST" action="{{ route('admin.shipping-zones.destroy',$z) }}" class="d-inline admin-form js-confirm-delete" data-confirm="{{ __('Delete?') }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">{{ __('Delete') }}</button></form>
                  </td>
               </tr>
            @endforeach
            </tbody>
         </table>
      </div>
   </div>
</div>
{{ $zones->links() }}
@endsection

@section('scripts')
<script src="{{ asset('admin/js/confirm-delete.js') }}"></script>
@endsection
