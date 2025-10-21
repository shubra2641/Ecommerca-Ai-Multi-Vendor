<div class="modern-card address-card {{ $ad->is_default ? 'is-default' : '' }}" data-address='{{ e(json_encode($ad, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'>
    <!-- Header -->
    <div class="card-header-modern address-card-head">
        <div class="head-main">
            <div class="address-icon-modern">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h3 class="address-title m-0">{{ $ad->title ?? $ad->label ?? __('Address') }}</h3>
                <p class="small-muted m-0">
                    @if($ad->city){{ $ad->city->name }}, @endif
                    @if($ad->governorate){{ $ad->governorate->name }}, @endif
                    @if($ad->country){{ $ad->country->name }}@endif
                </p>
            </div>
        </div>
        <div class="head-actions">
            @if($ad->is_default)
            <span class="badge-soft">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Default') }}
            </span>
            @endif
            <form method="post" action="{{ route('user.addresses.destroy',$ad) }}" class="inline-form delete-address-form" data-confirm="{{ __('Delete address?') }}">
                @csrf @method('DELETE')
                <button type="submit" class="link danger" title="{{ __('Delete') }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Body -->
    <div class="address-card-body">
        <div class="summary-lines-modern">
            <div class="summary-line-modern">
                <span>{{ __('Name') }}</span>
                <strong>{{ $ad->name }}</strong>
            </div>
            <div class="summary-line-modern">
                <span>{{ __('Address') }}</span>
                <strong>{{ $ad->line1 }} @if($ad->line2), {{ $ad->line2 }} @endif</strong>
            </div>
            <div class="summary-line-modern">
                <span>{{ __('Phone') }}</span>
                <strong>{{ $ad->phone }}</strong>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="address-card-footer">
        <div class="footer-left">
            @if(!$ad->is_default)
            <form method="post" action="{{ route('user.addresses.update',$ad) }}" class="inline-form">
                @csrf @method('PUT')
                <input type="hidden" name="is_default" value="1">
                <button class="btn-action-modern btn-primary" type="submit">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Make default') }}
                </button>
            </form>
            @endif
        </div>
        <div class="footer-right">
            <a href="{{ route('user.addresses.edit', $ad->id) }}" class="btn-action-modern btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('Edit') }}
            </a>
        </div>
    </div>
</div>