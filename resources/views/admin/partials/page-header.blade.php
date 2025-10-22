@props(['title','actions'=>null,'subtitle'=>null])
<div class="modern-page-header d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title mb-1">{{ $title }}</h1>
    @if($subtitle)<p class="text-muted mb-0 small">{{ $subtitle }}</p>@endif
  </div>
  @if($actions)
  <div class="page-actions d-flex flex-wrap gap-2">{!! $actions !!}{{-- NOTE: $actions contains pre-rendered HTML for admin controls; ensure sources are trusted or refactor to build actions via components --}}</div>
  @endif
</div>
