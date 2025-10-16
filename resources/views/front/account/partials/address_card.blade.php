<div class="address-card {{ $ad->is_default? 'is-default':'' }}" data-address='{{ e(json_encode($ad, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'>
    <div class="address-card-head">
        <div>
            <strong class="label">{{ $ad->title ?? $ad->label ?? __('Address') }}</strong>
            <div class="line meta small muted">@if($ad->city){{ $ad->city->name }}, @endif
                @if($ad->governorate){{ $ad->governorate->name }}, @endif
                @if($ad->country){{ $ad->country->name }}@endif</div>

        </div>
        <div class="head-actions">
            <form method="post" action="{{ route('user.addresses.destroy',$ad) }}"
                class="inline-form delete-address-form" data-confirm="{{ __('Delete address?') }}">
                @csrf @method('DELETE')
                <button type="submit" class="link danger">{{ __('Delete') }}</button>
            </form>
        </div>
    </div>

    <div class="body">
        <div class="line name">{{ $ad->name }}</div>
        <div class="line addr">{{ $ad->line1 }} @if($ad->line2), {{ $ad->line2 }} @endif</div>
        <div class="line phone">{{ $ad->phone }}</div>
    </div>

    <div class="card-footer actions">
        <div class="footer-left">
            @if($ad->is_default)
            <span class="badge muted">{{ __('Default') }}</span>
            @endif
        </div>
        <div class="footer-right">
            @if(!$ad->is_default)
            <form method="post" action="{{ route('user.addresses.update',$ad) }}" class="inline-form">
                @csrf @method('PUT')
                <input type="hidden" name="is_default" value="1">
                <button class="link" type="submit">{{ __('Make default') }}</button>
            </form>
            @endif
            <button class="link" data-action="edit-address" data-address-id="{{ $ad->id }}">{{ __('Edit') }}</button>
        </div>
    </div>
</div>