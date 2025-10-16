@extends('layouts.admin')
@section('title', __('Homepage Sections'))
@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Homepage Sections') }}</h1>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ route('admin.homepage.sections.update') }}" class="card shadow-sm p-3">
    @csrf
    <div class="table-responsive mb-3">
      <table class="table align-middle table-sm">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('Key') }}</th>
            <th>{{ __('Enabled') }}</th>
            <th>{{ __('Order') }}</th>
            <th>{{ __('Limit') }}</th>
            <th class="table-head-wide">{{ __('Titles') }}</th>
            <th class="table-head-wide">{{ __('Subtitles') }}</th>
            <th class="table-head-wide">{{ __('CTA Labels') }}</th>
            <th class="table-head-medium">{{ __('CTA Settings') }}</th>
          </tr>
        </thead>
        <tbody>
  @foreach($sections as $section)
          <tr>
            <td>{{ $loop->iteration }}<input type="hidden" name="sections[{{ $loop->index }}][id]" value="{{ $section->id }}"></td>
            <td><code>{{ $section->key }}</code></td>
            <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="sections[{{ $loop->index }}][enabled]" value="1" @checked($section->enabled)></div></td>
            <td><input type="number" class="form-control form-control-sm input-w-90" name="sections[{{ $loop->index }}][sort_order]" value="{{ $section->sort_order }}"></td>
            <td><input type="number" class="form-control form-control-sm input-w-90" name="sections[{{ $loop->index }}][item_limit]" value="{{ $section->item_limit }}" min="1" max="100"></td>
            <td>
              @foreach($activeLanguages as $lang)
                <div class="mb-1 d-flex align-items-center gap-1">
                  <span class="badge bg-secondary">{{ strtoupper($lang->code) }}</span>
                  <input type="text" class="form-control form-control-sm" name="sections[{{ $loop->parent->index }}][title][{{ $lang->code }}]" value="{{ $section->title_i18n[$lang->code] ?? '' }}" placeholder="{{ $lang->code }}">
                </div>
              @endforeach
            </td>
            <td>
              @foreach($activeLanguages as $lang)
                <div class="mb-1 d-flex align-items-center gap-1">
                  <span class="badge bg-secondary">{{ strtoupper($lang->code) }}</span>
                  <input type="text" class="form-control form-control-sm" name="sections[{{ $loop->parent->index }}][subtitle][{{ $lang->code }}]" value="{{ $section->subtitle_i18n[$lang->code] ?? '' }}" placeholder="{{ $lang->code }}">
                </div>
              @endforeach
            </td>
            <td>
              @foreach($activeLanguages as $lang)
                <div class="mb-1 d-flex align-items-center gap-1">
                  <span class="badge bg-info">{{ strtoupper($lang->code) }}</span>
                  <input type="text" class="form-control form-control-sm" name="sections[{{ $loop->parent->index }}][cta_label][{{ $lang->code }}]" value="{{ $section->cta_label_i18n[$lang->code] ?? '' }}" placeholder="{{ __('CTA') }} {{ $lang->code }}">
                </div>
              @endforeach
            </td>
            <td>
              <div class="form-check form-switch mb-1">
                <input class="form-check-input" type="checkbox" name="sections[{{ $loop->index }}][cta_enabled]" value="1" @checked($section->cta_enabled)>
                <label class="form-check-label small">{{ __('Enabled') }}</label>
              </div>
              <input type="text" class="form-control form-control-sm" name="sections[{{ $loop->index }}][cta_url]" value="{{ $section->cta_url }}" placeholder="/products">
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div>
      <button class="btn btn-primary">{{ __('Save Changes') }}</button>
    </div>
  </form>
</div>
@endsection
