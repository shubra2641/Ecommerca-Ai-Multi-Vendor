@extends('front.layout')
@section('title', __('Addresses').' - '.config('app.name'))
@section('content')

<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="addresses-page">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Add New Address') }}</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.addresses.store') }}">
                            @csrf
                            <div class="two-cols">


                                <div class="field">
                                    <label>{{ __('Title') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}"
                                        class="form-control @error('title') is-invalid @enderror">
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="field">
                                    <label>{{ __('Name') }}</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>


                                <div class="field">
                                    <label>{{ __('Country') }}</label>
                                    <select name="country_id" id="new_country"
                                        class="form-control @error('country_id') is-invalid @enderror"
                                        data-loading-text="{{ __('Loading...') }}">
                                        <option value="">{{ __('Select country') }}</option>
                                        @foreach($countries as $c)
                                        <option value="{{ $c->id }}" {{ old('country_id') == $c->id ? 'selected':'' }}>
                                            {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="two-cols">
                                <div class="field">
                                    <label>{{ __('Governorate') }}</label>
                                    <select name="governorate_id" id="new_governorate"
                                        class="form-control @error('governorate_id') is-invalid @enderror" disabled>
                                        <option value="">{{ __('Select governorate') }}</option>
                                    </select>
                                    @error('governorate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="field">
                                    <label>{{ __('City') }}</label>
                                    <select name="city_id" id="new_city"
                                        class="form-control @error('city_id') is-invalid @enderror" disabled>
                                        <option value="">{{ __('Select city') }}</option>
                                    </select>
                                    @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="field">
                                    <label>{{ __('Phone') }}</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="two-cols">
                                <div class="field">
                                    <label>{{ __('Address Line 1') }}</label>
                                    <input type="text" name="line1" value="{{ old('line1') }}"
                                        class="form-control @error('line1') is-invalid @enderror">
                                    @error('line1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="field">
                                    <label>{{ __('Address Line 2') }}</label>
                                    <input type="text" name="line2" value="{{ old('line2') }}"
                                        class="form-control @error('line2') is-invalid @enderror">
                                    @error('line2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="field">
                                    <label>{{ __('Postal Code') }}</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                        class="form-control @error('postal_code') is-invalid @enderror">
                                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="two-cols">

                                <div class="field">
                                    <label>&nbsp;</label>
                                    <div>
                                        <label><input type="checkbox" name="is_default" value="1"
                                                {{ old('is_default')? 'checked':'' }}>
                                            {{ __('Set as default') }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                </div>

                                <div class="actions mt-2">
                                    <button class="btn btn-primary" type="submit">{{ __('Save address') }}</button>
                                </div>
                            </div>


                        </form>

                        <div class="address-groups">
                            @if($addrDefault)
                            <div class="address-group">
                                @include('front.account.partials.address_edit_modal')
                                <h4>{{ __('Default address') }}</h4>
                                @include('front.account.partials.address_card',['ad'=>$addrDefault])
                            </div>
                            @endif
                            <div class="address-group">
                                <h4>{{ __('Other addresses') }}</h4>
                                <div class="address-cards">
                                    @forelse($addrOthers as $ad)
                                    @include('front.account.partials.address_card',['ad'=>$ad])
                                    @empty
                                    <div class="muted small">{{ __('No other addresses.') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
        </main>
    </div>
</section>
@endsection