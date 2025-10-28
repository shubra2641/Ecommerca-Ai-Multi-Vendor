<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <i class="fas fa-envelope"></i>
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
                                <i class="fas fa-envelope"></i>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required autofocus :value="old('email')" placeholder="{{ __('Enter your email address') }}">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <x-recaptcha />

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <i class="fas fa-envelope"></i>
                                {{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>