<x-guest-layout>
    <div class="form-wrapper">
        <div class="form-header">
            <h2>{{ __('Reset your password') }}</h2>
        </div>

        <form action="{{ route('password.store') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label for="email">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" class="form-control" required autofocus :value="old('email', $request->email)">
                <x-input-error :messages="$errors->get('email')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="form-control" required>
                <x-input-error :messages="$errors->get('password')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                <x-input-error :messages="$errors->get('password_confirmation')" class="input-error" />
            </div>

            <div class="form-group mt-6">
                <button type="submit" class="btn-primary">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
