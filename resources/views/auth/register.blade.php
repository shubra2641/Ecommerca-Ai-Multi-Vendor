<x-guest-layout>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <div class="admin-login-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7Z" />
                        <path d="M12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" />
                        <path d="M12 5V19M5 12H19" />
                    </svg>
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
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M20 21V19A4 4 0 0 0 16 15H8A4 4 0 0 0 4 19V21" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                {{ __('Name') }}
                            </label>
                            <input id="name" name="name" type="text" class="admin-form-input" required autofocus autocomplete="name" :value="old('name')" placeholder="{{ __('Your full name') }}">
                            <x-input-error :messages="$errors->get('name')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                {{ __('Email address') }}
                            </label>
                            <input id="email" name="email" type="email" class="admin-form-input" required :value="old('email')" autocomplete="email" placeholder="name@example.com">
                            <x-input-error :messages="$errors->get('email')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="phone_number" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0964 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.12 18.85C8.74773 17.3147 6.72533 15.2923 5.18999 12.92C3.49997 10.252 2.44824 7.29413 2.11999 4.22C2.095 3.94352 2.12887 3.66291 2.21931 3.39876C2.30975 3.13461 2.45471 2.89226 2.64476 2.68699C2.83481 2.48173 3.06556 2.31818 3.32242 2.20666C3.57928 2.09513 3.85675 2.03826 4.13699 2.04H7.13699C7.59599 2.00195 8.04341 2.18092 8.36699 2.54C8.94599 3.2 9.36699 4.57 9.36699 4.57C9.36699 4.57 9.69699 5.61 9.86699 6.24C10.037 6.87 10.407 7.27 10.407 7.27L12.407 9.27C12.407 9.27 12.777 9.64 13.037 10.27C13.297 10.9 13.627 11.94 13.627 11.94C13.627 11.94 14.047 13.31 14.627 13.97C14.9506 14.3291 15.1296 14.7765 15.0916 15.2355V18.2355C15.0916 18.2355 14.9506 18.3291 14.627 18.97C14.047 19.63 13.627 21 13.627 21C13.627 21 13.297 20.9 12.777 20.27C12.257 19.64 11.887 19.24 11.887 19.24L9.887 17.24C9.887 17.24 9.517 16.87 8.887 16.61C8.257 16.35 7.217 16.02 7.217 16.02C7.217 16.02 5.847 15.6 5.187 15.02C4.82799 14.6964 4.64902 14.2489 4.68699 13.79V10.79C4.68699 10.79 4.64902 10.6964 5.187 10.02C5.847 9.4 7.217 8.98 7.217 8.98C7.217 8.98 8.257 8.65 8.887 8.39C9.517 8.13 9.887 7.76 9.887 7.76L11.887 5.76C11.887 5.76 12.257 5.39 12.777 4.76C13.297 4.13 13.627 4.03 13.627 4.03C13.627 4.03 14.047 2.66 14.627 2C14.9506 1.64092 15.1296 1.19348 15.0916 0.73448V0.73448Z" />
                                </svg>
                                {{ __('Phone Number') }}
                            </label>
                            <input id="phone_number" name="phone_number" type="text" class="admin-form-input" required :value="old('phone_number')" autocomplete="tel" placeholder="{{ __('e.g. +20123456789') }}">
                            <x-input-error :messages="$errors->get('phone_number')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="whatsapp_number" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0964 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.12 18.85C8.74773 17.3147 6.72533 15.2923 5.18999 12.92C3.49997 10.252 2.44824 7.29413 2.11999 4.22C2.095 3.94352 2.12887 3.66291 2.21931 3.39876C2.30975 3.13461 2.45471 2.89226 2.64476 2.68699C2.83481 2.48173 3.06556 2.31818 3.32242 2.20666C3.57928 2.09513 3.85675 2.03826 4.13699 2.04H7.13699C7.59599 2.00195 8.04341 2.18092 8.36699 2.54C8.94599 3.2 9.36699 4.57 9.36699 4.57C9.36699 4.57 9.69699 5.61 9.86699 6.24C10.037 6.87 10.407 7.27 10.407 7.27L12.407 9.27C12.407 9.27 12.777 9.64 13.037 10.27C13.297 10.9 13.627 11.94 13.627 11.94C13.627 11.94 14.047 13.31 14.627 13.97C14.9506 14.3291 15.1296 14.7765 15.0916 15.2355V18.2355C15.0916 18.2355 14.9506 18.3291 14.627 18.97C14.047 19.63 13.627 21 13.627 21C13.627 21 13.297 20.9 12.777 20.27C12.257 19.64 11.887 19.24 11.887 19.24L9.887 17.24C9.887 17.24 9.517 16.87 8.887 16.61C8.257 16.35 7.217 16.02 7.217 16.02C7.217 16.02 5.847 15.6 5.187 15.02C4.82799 14.6964 4.64902 14.2489 4.68699 13.79V10.79C4.68699 10.79 4.64902 10.6964 5.187 10.02C5.847 9.4 7.217 8.98 7.217 8.98C7.217 8.98 8.257 8.65 8.887 8.39C9.517 8.13 9.887 7.76 9.887 7.76L11.887 5.76C11.887 5.76 12.257 5.39 12.777 4.76C13.297 4.13 13.627 4.03 13.627 4.03C13.627 4.03 14.047 2.66 14.627 2C14.9506 1.64092 15.1296 1.19348 15.0916 0.73448V0.73448Z" />
                                </svg>
                                {{ __('WhatsApp Number') }}
                            </label>
                            <input id="whatsapp_number" name="whatsapp_number" type="text" class="admin-form-input" :value="old('whatsapp_number')" placeholder="{{ __('Optional') }}">
                            <x-input-error :messages="$errors->get('whatsapp_number')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="role" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                                    <path d="M2 17L12 22L22 17" />
                                    <path d="M2 12L12 17L22 12" />
                                </svg>
                                {{ __('Register as') }}
                            </label>
                            <select id="role" name="role" class="admin-form-input" aria-label="{{ __('Select account type') }}">
                                <option value="user">{{ __('User') }}</option>
                                <option value="vendor">{{ __('Vendor') }}</option>
                            </select>
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <circle cx="12" cy="16" r="1" />
                                    <path d="M7 11V7A5 5 0 0 1 17 7V11" />
                                </svg>
                                {{ __('Password') }}
                            </label>
                            <input id="password" name="password" type="password" class="admin-form-input" required autocomplete="new-password" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-group">
                            <label for="password_confirmation" class="admin-form-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <circle cx="12" cy="16" r="1" />
                                    <path d="M7 11V7A5 5 0 0 1 17 7V11" />
                                </svg>
                                {{ __('Confirm Password') }}
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="admin-form-input" required autocomplete="new-password" placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="admin-form-error" />
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full" aria-label="{{ __('Register') }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7Z" />
                                    <path d="M12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" />
                                    <path d="M12 5V19M5 12H19" />
                                </svg>
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>