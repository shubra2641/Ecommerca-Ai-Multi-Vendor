@extends('front.layout')
@section('title', __('Profile').' - '.config('app.name'))
@section('content')

<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="dashboard-page">

                <!-- Header -->
                <div class="order-title-card">
                    <div class="title-row">
                        <div class="title-content">
                            <h1 class="modern-order-title">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="title-icon">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Profile') }}
                            </h1>
                            <p class="order-date-modern">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('View & Update Your Personal and Contact Information') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-page">
                    <!-- Main Profile Form -->
                    <div class="order-main-col">
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('Contact Information') }}
                                </h3>
                            </div>
                            <div class="card-body-padding">
                                <form method="post" action="{{ route('user.profile.update') }}" class="profile-form">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="field-label">{{ __('Email') }}</label>
                                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-input">
                                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="field-label">{{ __('Phone number') }}</label>
                                            <input type="text" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" class="form-input">
                                            @error('phone_number')<div class="field-error">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label">{{ __('WhatsApp number') }}</label>
                                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', auth()->user()->whatsapp_number) }}" class="form-input">
                                            @error('whatsapp_number')<div class="field-error">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="field-label">{{ __('Name') }}</label>
                                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-input">
                                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label">{{ __('Password') }}</label>
                                            <input type="password" name="password" autocomplete="new-password" class="form-input">
                                            @error('password')<div class="field-error">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="field-label">{{ __('Confirm Password') }}</label>
                                            <input type="password" name="password_confirmation" autocomplete="new-password" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <div class="info-box info-box-primary">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="info-icon">
                                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="info-text">{{ __('Leave password fields empty to keep current password.') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <button class="btn-action-modern btn-primary btn-full" type="submit">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('Update Profile') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
    </div>
    </div>
    </div>
    </main>
    </div>
</section>
@endsection