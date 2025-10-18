@extends('layouts.admin')
@section('title', __('Homepage Slides'))
@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Homepage Slides') }}</h1>
  <div class="row g-4">
    <div class="col-lg-8 order-2 order-lg-1">
      <div class="card shadow-sm">
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th><th>{{ __('Image') }}</th><th>{{ __('Title') }}</th><th>{{ __('Enabled') }}</th><th>{{ __('Order') }}</th><th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($slides as $slide)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="td-w-110">@if($slide->image)<img src="{{ asset('storage/'.$slide->image) }}" class="img-fluid rounded img-thumb-70" alt="slide">@endif</td>
                <td>{{ $slide->title }}</td>
                <td>
                  @if($slide->enabled)
                    <span class="badge bg-success">{{ __('On') }}</span>
                  @else
                    <span class="badge bg-secondary">{{ __('Off') }}</span>
                  @endif
                </td>
                <td>{{ $slide->sort_order }}</td>
                <td class="text-nowrap">
                  <form method="POST" action="{{ route('admin.homepage.slides.update',$slide) }}" class="d-inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="sort_order" value="{{ $slide->sort_order }}">
                    <input type="hidden" name="enabled" value="{{ $slide->enabled?0:1 }}">
                    <button class="btn btn-sm btn-outline-secondary">{{ $slide->enabled?__('Disable'):__('Enable') }}</button>
                  </form>
                  <form method="POST" action="{{ route('admin.homepage.slides.destroy',$slide) }}" class="d-inline js-confirm-delete" data-confirm="{{ __('Delete slide?') }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center text-muted small">{{ __('No slides yet.') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-4 order-1 order-lg-2">
      <form method="POST" action="{{ route('admin.homepage.slides.store') }}" enctype="multipart/form-data" class="card shadow-sm p-3">
        @csrf
        <h5 class="mb-3">{{ __('Add Slide') }}</h5>
        <div class="mb-3">
          <label class="form-label">{{ __('Image') }}</label>
          <input name="image" type="file" accept="image/*" required class="form-control">
        </div>
        <div class="accordion mb-3" id="slideLangAcc">
          @foreach($activeLanguages as $lang)
            <div class="accordion-item">
              <h2 class="accordion-header" id="sl-head-{{ $lang->code }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sl-body-{{ $lang->code }}">{{ strtoupper($lang->code) }}</button>
              </h2>
              <div id="sl-body-{{ $lang->code }}" class="accordion-collapse collapse" data-bs-parent="#slideLangAcc">
                <div class="accordion-body">
                  <div class="mb-2"><label class="form-label small">{{ __('Title') }}</label><input name="title_i18n[{{ $lang->code }}]" class="form-control form-control-sm" maxlength="120"></div>
                  <div class="mb-2"><label class="form-label small">{{ __('Subtitle') }}</label><input name="subtitle_i18n[{{ $lang->code }}]" class="form-control form-control-sm" maxlength="180"></div>
                  <div class="mb-2"><label class="form-label small">{{ __('Button Text') }}</label><input name="button_text_i18n[{{ $lang->code }}]" class="form-control form-control-sm" maxlength="40"></div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="mb-3"><label class="form-label">{{ __('Link URL') }}</label><input name="link_url" type="url" class="form-control"></div>
        <div class="mb-3"><label class="form-label">{{ __('Sort Order') }}</label><input name="sort_order" type="number" value="100" class="form-control"></div>
        <div class="form-check form-switch mb-3"><input type="checkbox" class="form-check-input" name="enabled" value="1" checked id="slide_enabled"><label for="slide_enabled" class="form-check-label">{{ __('Enabled') }}</label></div>
        <button class="btn btn-primary w-100">{{ __('Create Slide') }}</button>
      </form>
    </div>
  </div>
</div>
@endsection
