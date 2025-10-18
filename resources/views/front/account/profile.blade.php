@extends('front.layout')
@section('title', __('Profile').' - '.config('app.name'))
@section('content')

<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="profile-panels">
                <h1 class="page-title">{{ __('Profile') }}</h1>
                <p class="page-sub">{{ __('View & Update Your Personal and Contact Information') }}</p>
                <div class="profile-layout">
                    <div class="profile-main">
                        <form method="post" action="{{ route('user.profile.update') }}" class="profile-form">
                            @csrf
                            @method('PUT')
                            <div class="panel-row">
                                <div class="panel-block">
                                    <h4>{{ __('Contact Information') }}</h4>
                                    <div class="two-cols">
                                        <div class="field">
                                            <label>{{ __('Email') }}</label>
                                            <input type="email" name="email"
                                                value="{{ old('email', auth()->user()->email) }}">
                                            @error('email')<div class="err">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="field">
                                            <label>{{ __('Phone number') }}</label>
                                            <input type="text" name="phone_number"
                                                value="{{ old('phone_number', auth()->user()->phone_number) }}">
                                            @error('phone_number')<div class="err">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="two-cols mt-1">
                                        <div class="field">
                                            <label>{{ __('Balance') }}</label>
                                            <input type="text" value="{{ auth()->user()->formatted_balance }}" readonly>
                                        </div>
                                    </div>
                                    <div class="two-cols mt-1">
                                        <div class="field">
                                            <label>{{ __('WhatsApp number') }}</label>
                                            <input type="text" name="whatsapp_number"
                                                value="{{ old('whatsapp_number', auth()->user()->whatsapp_number) }}">
                                            @error('whatsapp_number')<div class="err">{{ $message }}</div>@enderror
                                        </div>

                                    </div>
                                </div>
                                <div class="panel-block">
                                    <h4>{{ __('Personal Information') }}</h4>
                                    <div class="two-cols">
                                        <div class="field">
                                            <label>{{ __('Name') }}</label>
                                            <input type="text" name="name"
                                                value="{{ old('name', auth()->user()->name) }}">
                                            @error('name')<div class="err">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="field">
                                            <label>{{ __('Password') }}</label>
                                            <input type="password" name="password" autocomplete="new-password">
                                            @error('password')<div class="err">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="two-cols mt-1">
                                        <div class="field">
                                            <label>{{ __('Confirm Password') }}</label>
                                            <input type="password" name="password_confirmation"
                                                autocomplete="new-password">
                                        </div>
                                        <div class="field">
                                            <label>&nbsp;</label>
                                            <div class="muted small">
                                                {{ __('Leave password fields empty to keep current password.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="actions">
                                <button class="btn btn-primary btn-sm" type="submit">{{ __('Update Profile') }}</button>
                            </div>
                        </form>
                    </div>
                    <aside class="profile-side">
                        <div class="user-card">
                            <div class="profile-header">
                                <div class="avatar">
                                    {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email,0,1)) }}</div>
                                <div class="user-meta">
                                    <div class="user-line">{{ auth()->user()->name ?? __('User') }}</div>
                                    <div class="muted">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <div class="quick-links">
                                <a href="{{ route('user.addresses') }}" class="quick-link">{{ __('Manage addresses') }}
                                    <span class="muted">›</span></a>
                                <a href="{{ route('user.orders') }}" class="quick-link">{{ __('My orders') }} <span
                                        class="muted">›</span></a>
                                <a href="{{ route('user.invoices') }}" class="quick-link">{{ __('Invoices') }} <span
                                        class="muted">›</span></a>
                            </div>
                        </div>
                            <div class="panel-block">
                            <h4>{{ __('Profile Completion') }}</h4>
                            <div class="progress-bar progress-track">
                                <span data-progress="{{ auth()->user()->profile_completion }}" class="progress-fill"></span>
                            </div>
                            <div class="progress-label">{{ __('Completion') }}
                                <strong>{{ auth()->user()->profile_completion }}%</strong>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>
</section>
@endsection