<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <i class="fas fa-shield-alt icon-large" aria-hidden="true"></i>
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
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required autofocus placeholder="{{ __('Enter your email address') }}">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                <i class="fas fa-lock" aria-hidden="true"></i>
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
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                {{ __('Log in') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>