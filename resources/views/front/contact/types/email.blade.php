<div class="contact-email">
 @php($emails = $block->body['emails'] ?? [])
 @if(empty($emails) && !empty($setting?->contact_email))
   @php(
     $emails = collect(preg_split('/[\n,]+/',$setting->contact_email))
       ->map(fn($v)=>trim($v))
       ->filter(fn($v)=>filter_var($v, FILTER_VALIDATE_EMAIL))
       ->values()
       ->all()
   )
 @endif
 <ul class="email-list">
   @forelse($emails as $e)
     <li><i class="fas fa-envelope"></i> <a href="mailto:{{ $e }}">{{ $e }}</a></li>
   @empty
     <li class="text-muted">{{ __('No emails configured') }}</li>
   @endforelse
 </ul>
</div>
