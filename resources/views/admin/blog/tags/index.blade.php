@extends('layouts.admin')
@section('title', __('Tags'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('Tags') }}</li>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Tags') }}</h1>
        <p class="text-muted mb-0">{{ __('Manage blog tags and create new ones') }}</p>
    </div>
</div>

<div class="row">
  <div class="col-md-5">
    <div class="card card-body">
      <h6 class="fw-bold mb-3">{{ __('Create Tag') }}</h6>
      <form method="POST" action="{{ route('admin.blog.tags.store') }}">@csrf
      <div class="mb-2">
        <label class="form-label small">{{ __('Name') }}</label>
        <input name="name" class="form-control form-control-sm" required>
      </div>
      <div class="mb-2">
        <label class="form-label small">{{ __('Slug') }}</label>
      <input name="slug" class="form-control form-control-sm" placeholder="auto" readonly>
      <div class="form-text small">{{ __('Slug will be generated from the tag name.') }}</div>
      </div>
        <button class="btn btn-primary">{{ __('Save') }}</button>
      </form>
    </div>
  </div>
  <div class="col-md-7">
  <div class="card modern-card">
      <div class="card-header">
        <h5 class="mb-0">{{ __('Tags List') }}</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Updated') }}</th><th width="300">{{ __('Actions') }}</th></tr></thead>
          <tbody>
            @forelse($tags as $tag)
            <tr>
              <td><strong>{{ $tag->name }}</strong><br><small class="text-muted">/{{ $tag->slug }}</small></td>
              <td>{{ $tag->updated_at->diffForHumans() }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('admin.blog.tags.update',$tag) }}" class="d-inline-flex align-items-center gap-1">@csrf @method('PUT')
                  <input name="name" value="{{ $tag->name }}" class="form-control form-control-sm w-120">
                  <input name="slug" value="{{ $tag->slug }}" class="form-control form-control-sm w-120" readonly>
                  <button class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                </form>
                <form method="POST" action="{{ route('admin.blog.tags.destroy',$tag) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this tag?') }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
              </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted py-3">{{ __('No tags found') }}</td></tr>
            @endforelse
          </tbody>
          </table>
        </div>
      </div>
      @if($tags->hasPages())
      <div class="card-footer">
        {{ $tags->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
