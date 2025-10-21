<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                </div>
                <h1 class="admin-login-title">{{ __('Forgot your password?') }}</h1>
                <p class="admin-login-subtitle">{{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}</p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <x-auth-session-status class="admin-form-group" :status="session('status')" />

                    <form action="{{ route('password.email') }}" method="POST" class="admin-login-form">
                        @csrf

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required autofocus :value="old('email')" placeholder="{{ __('Enter your email address') }}">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                {{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>