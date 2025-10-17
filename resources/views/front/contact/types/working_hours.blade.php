<div class="contact-hours">
 @php($hours = $block->body['hours'] ?? []) {{-- hours now stored directly in block body via builder --}}
 <table class="table table-sm table-borderless working-hours-table">
   <tbody>
   @forelse($hours as $row)
     <tr>
       <th class="working-hours-day">{{ $row['day'] ?? '' }}</th>
       <td>{{ $row['from'] ?? '' }} - {{ $row['to'] ?? '' }}</td>
     </tr>
   @empty
     <tr><td colspan="2" class="text-muted small">{{ __('No hours configured') }}</td></tr>
   @endforelse
   </tbody>
 </table>
</div>
