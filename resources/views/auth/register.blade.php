<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="admin-login-title">{{ __('Create a new account') }}</h1>
                <p class="admin-login-subtitle">
                    {{ __('Or') }}
                    <a href="{{ route('login') }}" class="admin-login-link">{{ __('log in to your existing account') }}</a>
                </p>
            </div>

            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <form action="{{ route('register') }}" method="POST" novalidate class="admin-login-form">
                        @csrf

                        <div class="admin-form-group">
                            <label for="name" class="admin-form-label">
                                <i class="fas fa-user"></i>
                                {{ __('Name') }}
                            </label>
                            <input id="name" name="name" type="text" class="admin-form-input" required autofocus autocomplete="name" :value="old('name')" placeholder="{{ __('Your full name') }}">
                            <x-input-error :messages="$errors->get('name')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">
                                <i class="fas fa-envelope"></i>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required :value="old('email')" autocomplete="email" placeholder="name@example.com">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="phone_number" class="admin-form-label">
                                <i class="fas fa-phone" aria-hidden="true"></i>
                                {{ __('Phone Number') }}
                            </label>
                            <input id="phone_number" name="phone_number" type="text" class="admin-form-input" required :value="old('phone_number')" autocomplete="tel" placeholder="{{ __('e.g. +20123456789') }}">
                            <x-input-error :messages="$errors->get('phone_number')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="whatsapp_number" class="admin-form-label">
                                <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                {{ __('WhatsApp Number') }}
                            </label>
                            <input id="whatsapp_number" name="whatsapp_number" type="text" class="admin-form-input" :value="old('whatsapp_number')" placeholder="{{ __('Optional') }}">
                            <x-input-error :messages="$errors->get('whatsapp_number')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="role" class="admin-form-label">
                                <i class="fas fa-user-tag" aria-hidden="true"></i>
                                {{ __('Register as') }}
                            </label>
                            <select id="role" name="role" class="admin-form-input" aria-label="{{ __('Select account type') }}">
                                <option value="user">{{ __('User') }}</option>
                                <option value="vendor">{{ __('Vendor') }}</option>
                            </select>
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                {{ __('Password') }}
                            </label>
                            <input id="password" name="password" type="password" class="admin-form-input" required autocomplete="new-password" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password_confirmation" class="admin-form-label">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                {{ __('Confirm Password') }}
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="admin-form-input" required autocomplete="new-password" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="admin-form-error" />
                        </div>

                        <x-recaptcha />

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full" aria-label="{{ __('Register') }}">
                                <i class="fas fa-user-plus" aria-hidden="true"></i>
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>