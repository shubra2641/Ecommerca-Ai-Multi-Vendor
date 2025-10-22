<div class="contact-phone">
 @php($locale = app()->getLocale())
 @php($numbers = $block->body['numbers'] ?? [])
 @if(empty($numbers) && !empty($setting?->contact_phone))
   @php(
     $numbers = collect(preg_split('/[\n,]+/',$setting->contact_phone))
        ->map(fn($v)=>trim($v))
        ->filter()
        ->values()
        ->all()
   )
 @endif
 <ul class="phones-list">
   @forelse($numbers as $n)
     <li><i class="fas fa-phone"></i> <a href="tel:{{ preg_replace('/\s+/','',$n) }}">{{ $n }}</a></li>
   @empty
     <li class="text-muted">{{ __('No phone numbers configured') }}</li>
   @endforelse
 </ul>
</div>
