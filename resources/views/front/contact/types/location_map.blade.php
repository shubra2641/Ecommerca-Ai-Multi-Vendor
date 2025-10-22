<div class="contact-location">
 @php($lat = $block->body['lat'] ?? null)
 @php($lng = $block->body['lng'] ?? null)
 @php($embed = $block->body['embed'] ?? null)
 @if($embed)
  <div class="map-embed">
     @clean($embed)
   </div>
 @elseif($lat && $lng)
  <iframe class="map-embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q={{ $lat }},{{ $lng }}&hl={{ app()->getLocale() }}&z=14&output=embed"></iframe>
 @else
   <div class="text-muted small">{{ __('No location configured') }}</div>
 @endif
</div>
