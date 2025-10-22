<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <i class="fas fa-shield-alt"></i>
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
                                <i class="fas fa-lock"></i>
                                {{ __('Password') }}
                            </label>
                            <input id="password" name="password" type="password" class="admin-form-input" required autocomplete="current-password" placeholder="{{ __('Enter your password') }}">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                {{ __('Confirm') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>