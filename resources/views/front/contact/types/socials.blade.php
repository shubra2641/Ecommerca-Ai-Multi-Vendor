<div class="contact-socials">
 @php($links = App\Models\SocialLink::orderBy('order')->get())
 <div class="socials-flex">
 @forelse($links as $s)
   <a href="{{ $s->url }}" target="_blank" rel="noopener" class="social-pill">
     <i class="fab fa-{{ $s->icon }}"></i> <span>{{ $s->title ?? $s->platform }}</span>
   </a>
 @empty
   <div class="text-muted small">{{ __('No socials configured') }}</div>
 @endforelse
 </div>
</div>
