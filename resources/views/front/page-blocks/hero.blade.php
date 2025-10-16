<section class="pb-hero position-relative text-white" data-hero data-hero-bg="{{ $pb_hero_bg }}" data-hero-overlay="{{ $pb_hero_overlay }}">
  <div class="container position-relative py-5">
  @if($pb_hero_heading)<h1 class="display-6 mb-3">{{ $pb_hero_heading }}</h1>@endif
  @if($pb_hero_body)<div class="lead mb-0">{{ nl2br(e($pb_hero_body)) }}</div>@endif
  </div>
</section>
