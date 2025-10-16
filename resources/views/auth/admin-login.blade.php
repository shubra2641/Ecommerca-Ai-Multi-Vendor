<x-guest-layout>
    <div class="form-wrapper">
        <div class="form-header">
            <h2>{{ __('Admin Login') }}</h2>
        </div>

        <form action="{{ route('admin.login.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" class="form-control" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="form-control" required>
                <x-input-error :messages="$errors->get('password')" class="input-error" />
            </div>

            <div class="form-group flex items-center justify-between mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="form-group mt-6">
                <button type="submit" class="btn-primary">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
