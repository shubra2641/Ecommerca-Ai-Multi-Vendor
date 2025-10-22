@props([
'icon' => 'circle-off', // not used as we inline svg, kept for future
'title' => null,
'message' => null,
'actionLabel' => null,
'actionUrl' => null,
'secondaryLabel' => null,
'secondaryUrl' => null,
])
<div class="empty-state unified-empty">
    <div class="empty-icon empty-icon-sm">
        <i class="fas fa-inbox empty-img"></i>
    </div>
    @if($title)<h3 class="empty-title">{{ $title }}</h3>@endif
    @if($message)<p class="empty-message">{{ e($message) }}</p>@endif
    @if($actionLabel && $actionUrl)
    <div class="empty-actions">
        <a href="{{ $actionUrl }}" class="btn btn-primary">{{ $actionLabel }}</a>
        @if($secondaryLabel && $secondaryUrl)
        <a href="{{ $secondaryUrl }}" class="btn btn-outline">{{ $secondaryLabel }}</a>
        @endif
    </div>
    @endif
</div>