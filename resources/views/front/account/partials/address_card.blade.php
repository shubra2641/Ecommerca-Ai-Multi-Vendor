<div class="modern-card address-card {{ $ad->is_default ? 'is-default' : '' }}" data-address='{{ e(json_encode($ad, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'>
    <!-- Header -->
    <div class="card-header-modern address-card-head">
        <div class="head-main">
            <div class="address-icon-modern">
                <i class="fas fa-map-marker-alt"></i>
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
                <i class="fas fa-check-circle"></i>
                {{ __('Default') }}
            </span>
            @endif
            <form method="post" action="{{ route('user.addresses.destroy',$ad) }}" class="inline-form delete-address-form" data-confirm="{{ __('Delete address?') }}">
                @csrf @method('DELETE')
                <button type="submit" class="link danger" title="{{ __('Delete') }}">
                    <i class="fas fa-trash"></i>
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
                    <i class="fas fa-check-circle"></i>
                    {{ __('Make default') }}
                </button>
            </form>
            @endif
        </div>
        <div class="footer-right">
            <a href="{{ route('user.addresses.edit', $ad->id) }}" class="btn-action-modern btn-secondary">
                <i class="fas fa-edit"></i>
                {{ __('Edit') }}
            </a>
        </div>
    </div>
</div>