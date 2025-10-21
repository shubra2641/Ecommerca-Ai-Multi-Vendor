<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22S8 18 8 12V5L12 3L16 5V12C16 18 12 22 12 22Z" />
                        <path d="M9 12L11 14L15 10" />
                    </svg>
                </div>
                <h1 class="admin-login-title">{{ __('Admin Login') }}</h1>
                <p class="admin-login-subtitle">{{ __('Sign in to access the admin panel') }}</p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <form action="{{ route('admin.login.store') }}" method="POST" class="admin-login-form">
                        @csrf

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required autofocus placeholder="{{ __('Enter your email address') }}">
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
                            <input id="password" name="password" type="password" class="admin-form-input" required placeholder="{{ __('Enter your password') }}">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <div class="admin-checkbox-wrapper">
                                <input id="remember_me" type="checkbox" class="admin-checkbox" name="remember">
                                <label for="remember_me" class="admin-checkbox-label">
                                    <span class="admin-checkbox-custom"></span>
                                    <span class="admin-checkbox-text">{{ __('Remember me') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M15 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H15" />
                                    <path d="M10 17L15 12L10 7" />
                                    <path d="M15 12H3" />
                                </svg>
                                {{ __('Log in') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>