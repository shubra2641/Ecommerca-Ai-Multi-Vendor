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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true" width="40" height="40"
            class="empty-img">
            <circle cx="12" cy="12" r="9" stroke-opacity="0.45" />
            <path d="M9 10h6" />
            <path d="M10 14h4" stroke-opacity="0.6" />
        </svg>
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