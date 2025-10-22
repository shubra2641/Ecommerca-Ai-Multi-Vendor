<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <i class="fas fa-lock"></i>
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
                                <i class="fas fa-envelope"></i>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required autofocus :value="old('email', $request->email)" placeholder="{{ __('Enter your email address') }}">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                <i class="fas fa-lock"></i>
                                {{ __('Password') }}
                            </label>
                            <input id="password" name="password" type="password" class="admin-form-input" required placeholder="{{ __('Enter your new password') }}">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password_confirmation" class="admin-form-label">
                                <i class="fas fa-lock"></i>
                                {{ __('Confirm Password') }}
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="admin-form-input" required placeholder="{{ __('Confirm your new password') }}">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>