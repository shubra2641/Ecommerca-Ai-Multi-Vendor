{{-- Variables $siteName and $logoPath supplied by ApplicationLogoComposer --}}

@if($logoPath && file_exists(public_path('storage/' . $logoPath)))
<img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($logoPath) }}" alt="{{ $siteName }}" {{ $attributes->merge(['class' => 'block h-9 w-auto']) }}>
@else
<span {{ $attributes->merge(['class' => 'block h-9 flex items-center text-xl font-bold text-gray-800']) }}>{{ $siteName }}</span>
@endif