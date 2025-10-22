<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <i class="fas fa-envelope-open"></i>
                </div>
                <h1 class="admin-login-title">{{ __('Verify Your Email') }}</h1>
                <p class="admin-login-subtitle">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    @if (session('status') == 'verification-link-sent')
                    <div class="admin-form-group">
                        <div class="admin-success-message">
                            <i class="fas fa-check-circle"></i>
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    </div>
                    @endif

                    <div class="admin-form-actions">
                        <form method="POST" action="{{ route('verification.send') }}" class="admin-form-inline">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <i class="fas fa-envelope"></i>
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}" class="admin-form-inline">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-secondary admin-btn-full">
                                <i class="fas fa-sign-out-alt"></i>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>