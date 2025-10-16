<div class="pb-block text-image-block">
  <div class="row align-items-center py-3 flex-md-row {{ $pb_pos==='left'?'flex-row-reverse':'' }}">
    <div class="col-md-6 mb-3 mb-md-0">
      <div class="content">
        @if($pb_title)<h3 class="h4">{{ $pb_title }}</h3>@endif
  @if($pb_content)<div class="block-richtext">{{ nl2br(e($pb_content)) }}</div>@endif
      </div>
    </div>
    <div class="col-md-6">
      @if(!empty($pb_image))
        <img src="{{ $pb_image }}" alt="{{ $pb_image_alt }}" class="img-fluid rounded" />
      @endif
    </div>
  </div>
</div>
