<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                        <polyline points="22,6 12,13 2,6" />
                        <path d="M9 12l2 2 4-4" />
                    </svg>
                </div>
                <h1 class="admin-login-title">{{ __('Verify Your Email') }}</h1>
                <p class="admin-login-subtitle">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    @if (session('status') == 'verification-link-sent')
                    <div class="admin-form-group">
                        <div class="admin-success-message">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    </div>
                    @endif

                    <div class="admin-form-actions">
                        <form method="POST" action="{{ route('verification.send') }}" class="admin-form-inline">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}" class="admin-form-inline">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-secondary admin-btn-full">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 21H5A2 2 0 0 1 3 19V5A2 2 0 0 1 5 3H19A2 2 0 0 1 21 5V19A2 2 0 0 1 19 21H15" />
                                    <path d="M9 9L15 15M15 9L9 15" />
                                </svg>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>