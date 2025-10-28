<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h1 class="admin-login-title">{{ __('Log in to your account') }}</h1>
                <p class="admin-login-subtitle">
                    {{ __('Or') }}
                    <a href="{{ route('register') }}" class="admin-login-link">{{ __('create a new account') }}</a>
                </p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <x-auth-session-status class="admin-form-group" :status="session('status')" />

                    <form action="{{ route('login') }}" method="POST" novalidate class="admin-login-form">
                        @csrf

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">
                                <i class="fas fa-envelope"></i>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required autofocus autocomplete="email" :value="old('email')" placeholder="name@example.com">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                <i class="fas fa-lock"></i>
                                {{ __('Password') }}
                            </label>
                            <input id="password" name="password" type="password" class="admin-form-input" required autocomplete="current-password" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <div class="admin-form-footer">
                                <div class="admin-checkbox-wrapper">
                                    <input id="remember_me" name="remember" type="checkbox" class="admin-checkbox">
                                    <label for="remember_me" class="admin-checkbox-label">
                                        <span class="admin-checkbox-custom"></span>
                                        <span class="admin-checkbox-text">{{ __('Remember me') }}</span>
                                    </label>
                                </div>

                                <div class="admin-form-links">
                                    @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="admin-form-link">{{ __('Forgot your password?') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <x-recaptcha />

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full" aria-label="{{ __('Log in') }}">
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