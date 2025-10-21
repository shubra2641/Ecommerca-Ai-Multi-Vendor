<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <circle cx="12" cy="16" r="1" />
                        <path d="M7 11V7A5 5 0 0 1 17 7V11" />
                    </svg>
                </div>
                <h1 class="admin-login-title">{{ __('Confirm Password') }}</h1>
                <p class="admin-login-subtitle">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <form method="POST" action="{{ route('password.confirm') }}" class="admin-login-form">
                        @csrf

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <circle cx="12" cy="16" r="1" />
                                    <path d="M7 11V7A5 5 0 0 1 17 7V11" />
                                </svg>
                                {{ __('Password') }}
                            </label>
                            <input id="password" name="password" type="password" class="admin-form-input" required autocomplete="current-password" placeholder="{{ __('Enter your password') }}">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Confirm') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>