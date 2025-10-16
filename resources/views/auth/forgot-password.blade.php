<x-guest-layout>
    <div class="form-wrapper">
        <div class="form-header">
            <h2>{{ __('Forgot your password?') }}</h2>
            <p>{{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" class="form-control" required autofocus :value="old('email')">
                <x-input-error :messages="$errors->get('email')" class="input-error" />
            </div>

            <div class="form-group mt-6">
                <button type="submit" class="btn-primary">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
