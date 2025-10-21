<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <circle cx="12" cy="16" r="1" />
                        <path d="M7 11V7A5 5 0 0 1 17 7V11" />
                        <path d="M12 5V19M5 12H19" />
                    </svg>
                </div>
                <h1 class="admin-login-title">{{ __('Reset your password') }}</h1>
                <p class="admin-login-subtitle">{{ __('Enter your new password below') }}</p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <form action="{{ route('password.store') }}" method="POST" class="admin-login-form">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required autofocus :value="old('email', $request->email)" placeholder="{{ __('Enter your email address') }}">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <circle cx="12" cy="16" r="1" />
                                    <path d="M7 11V7A5 5 0 0 1 17 7V11" />
                                </svg>
                                {{ __('Password') }}
                            </label>
                            <input id="password" name="password" type="password" class="admin-form-input" required placeholder="{{ __('Enter your new password') }}">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password_confirmation" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <circle cx="12" cy="16" r="1" />
                                    <path d="M7 11V7A5 5 0 0 1 17 7V11" />
                                </svg>
                                {{ __('Confirm Password') }}
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="admin-form-input" required placeholder="{{ __('Confirm your new password') }}">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>