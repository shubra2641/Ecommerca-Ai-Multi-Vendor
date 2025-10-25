@props(['post'])
<div class="card h-100 shadow-sm" data-glow data-anim>
  @if($post->featured_image)
  <img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($post->featured_image) ?: asset('images/placeholder.png') }}" class="card-img-top" alt="{{ $post->title }}" loading="lazy" data-skel>
  @endif
  <div class="card-body d-flex flex-column">
    <h5 class="card-title mb-1"><a href="{{ route('blog.show',$post->slug) }}" class="text-decoration-none">{{ $post->title }}</a></h5>
    <div class="text-muted small mb-2">{{ $post->published_at?->format('Y-m-d') }} @if($post->category) • {{ $post->category->name }} @endif</div>
    <p class="card-text small flex-grow-1 mb-2">{{ $post->excerpt }}</p>
    <a href="{{ route('blog.show',$post->slug) }}" class="btn btn-sm btn-outline-primary mt-auto align-self-start">{{ __('Read More') }}</a>
  </div>
</div>