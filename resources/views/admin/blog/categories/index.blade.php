@extends('layouts.admin')
@section('title', __('Categories'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('Categories') }}</li>
@endsection
@section('content')
<div class="card modern-card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('Categories') }}</h5>
    <a href="{{ route('admin.blog.categories.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> {{ __('Create') }}</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Parent') }}</th><th>{{ __('Updated') }}</th><th width="120">{{ __('Actions') }}</th></tr></thead>
      <tbody>
        @forelse($categories as $cat)
        <tr>
          <td><strong>{{ $cat->name }}</strong><br><small class="text-muted">/{{ $cat->slug }}</small></td>
          <td>{{ $cat->parent?->name ?? '-' }}</td>
          <td>{{ $cat->updated_at->diffForHumans() }}</td>
          <td class="text-end">
            <div class="btn-group btn-group-sm">
              <a href="{{ route('admin.blog.categories.edit',$cat) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-edit"></i></a>
              <form method="POST" action="{{ route('admin.blog.categories.destroy',$cat) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this category?') }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button></form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted py-3">{{ __('No categories found') }}</td></tr>
        @endforelse
      </tbody>
      </table>
    </div>
  </div>
  @if($categories->hasPages())
  <div class="card-footer">
    {{ $categories->links() }}
  </div>
  @endif
</div>
@endsection
