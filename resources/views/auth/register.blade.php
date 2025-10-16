<x-guest-layout>
    <div class="form-wrapper">
        <div class="form-header">
            <h2>{{ __('Create a new account') }}</h2>
            <p>
                {{ __('Or') }}
                <a href="{{ route('login') }}">{{ __('log in to your existing account') }}</a>
            </p>
        </div>

        <form action="{{ route('register') }}" method="POST" novalidate>
            @csrf
            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="form-control" required autofocus autocomplete="name"
                    :value="old('name')" placeholder="{{ __('Your full name') }}">
                <x-input-error :messages="$errors->get('name')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" class="form-control" required :value="old('email')"
                    autocomplete="email" placeholder="name@example.com">
                <x-input-error :messages="$errors->get('email')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="phone_number">{{ __('Phone Number') }}</label>
                <input id="phone_number" name="phone_number" type="text" class="form-control" required
                    :value="old('phone_number')" autocomplete="tel" placeholder="{{ __('e.g. +20123456789') }}">
                <x-input-error :messages="$errors->get('phone_number')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="whatsapp_number">{{ __('WhatsApp Number') }}</label>
                <input id="whatsapp_number" name="whatsapp_number" type="text" class="form-control"
                    :value="old('whatsapp_number')" placeholder="{{ __('Optional') }}">
                <x-input-error :messages="$errors->get('whatsapp_number')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="role">{{ __('Register as') }}</label>
                <select id="role" name="role" class="form-select" aria-label="{{ __('Select account type') }}">
                    <option value="user">{{ __('User') }}</option>
                    <option value="vendor">{{ __('Vendor') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="form-control" required
                    autocomplete="new-password" placeholder="••••••••">
                <x-input-error :messages="$errors->get('password')" class="input-error" />
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                    required autocomplete="new-password" placeholder="••••••••">
                <x-input-error :messages="$errors->get('password_confirmation')" class="input-error" />
            </div>

            <div class="form-group mt-6">
                <button type="submit" class="btn-primary mobile-btn mobile-full-width"
                    aria-label="{{ __('Register') }}">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>