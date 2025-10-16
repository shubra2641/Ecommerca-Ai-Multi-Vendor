<x-guest-layout>
    <div class="form-wrapper">
        <div class="form-header">
            <h2>{{ __('Log in to your account') }}</h2>
            <p>
                {{ __('Or') }}
                <a href="{{ route('register') }}">{{ __('create a new account') }}</a>
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

    <form action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            <div class="form-group">
                <label for="email">{{ __('Email address') }}</label>
        <input id="email" name="email" type="email" class="form-control" required autofocus autocomplete="email" :value="old('email')" placeholder="name@example.com">
                <x-input-error :messages="$errors->get('email')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password" placeholder="••••••••">
                <x-input-error :messages="$errors->get('password')" class="input-error" />
            </div>

            <div class="form-footer">
                <div class="form-check">
                    <input id="remember_me" name="remember" type="checkbox" class="form-check-input">
                    <label for="remember_me" class="form-check-label">{{ __('Remember me') }}</label>
                </div>

                <div class="text-sm">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                    @endif
                </div>
            </div>

            <div class="form-group mt-6">
                <button type="submit" class="btn-primary mobile-btn mobile-full-width" aria-label="{{ __('Log in') }}">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
