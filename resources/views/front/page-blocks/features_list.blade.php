<div class="pb-block pb-features-list">
  <div class="row g-3">
  @foreach($pb_features as $feat)
      <div class="col-md-4 col-sm-6">
        <div class="h-100 p-3 border rounded">
      @if(!empty($feat['icon']))<div class="mb-2"><i class="fas fa-{{ e($feat['icon']) }} fa-lg text-primary"></i></div>@endif
      @if(!empty($feat['title']))<h6 class="fw-semibold mb-1">{{ $feat['title'] }}</h6>@endif
      @if(!empty($feat['desc']))<p class="small mb-0 text-muted">{{ $feat['desc'] }}</p>@endif
        </div>
      </div>
    @endforeach
  </div>
</div>
