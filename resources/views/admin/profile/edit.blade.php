@extends('layouts.admin')

@section('title', __('Profile Settings'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20.59 22C20.59 18.13 16.74 15 12 15C7.26 15 3.41 18.13 3.41 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Profile Settings') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage your account information and security settings') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-6">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ __('Profile Information') }}
                        </h3>
                    </div>
                    <form action="{{ route('admin.profile.update') }}" method="POST" class="admin-card-body">
                        @csrf
                        @method('PUT')

                        <div class="admin-form-group">
                            <label for="name" class="admin-form-label">{{ __('Full Name') }}</label>
                            <input type="text" id="name" name="name" class="admin-form-input @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">{{ __('Email Address') }}</label>
                            <input type="email" id="email" name="email"
                                class="admin-form-input @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="phone_number" class="admin-form-label">{{ __('Phone Number') }}</label>
                            <input type="text" id="phone_number" name="phone_number"
                                class="admin-form-input @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', $user->phone_number) }}">
                            @error('phone_number')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Role') }}</label>
                            <input type="text" class="admin-form-input" value="{{ ucfirst($user->role) }}" readonly disabled>
                            <div class="admin-text-muted">{{ __('Your role cannot be changed') }}</div>
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Account Status') }}</label>
                            <div class="d-flex align-items-center">
                                @if($user->approved_at)
                                <span class="admin-status-badge admin-status-badge-completed">{{ __('Approved') }}</span>
                                <div class="admin-text-muted ms-2">{{ __('Approved on') }}
                                    {{ \Carbon\Carbon::parse($user->approved_at)->format('Y-m-d H:i') }}</div>
                                @else
                                <span class="admin-status-badge admin-status-badge-warning">{{ __('Pending Approval') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="admin-card-footer">
                            <div class="admin-flex-end">
                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17 21V13H7V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7 3V8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ __('Update Profile') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change -->
            <div class="col-md-6">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="16" r="1" stroke="currentColor" stroke-width="2"/>
                                <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            {{ __('Change Password') }}
                        </h3>
                    </div>
                    <form action="{{ route('admin.profile.password') }}" method="POST" class="admin-card-body">
                        @csrf
                        @method('PUT')

                        <div class="admin-form-group">
                            <label for="current_password" class="admin-form-label">{{ __('Current Password') }}</label>
                            <input type="password" id="current_password" name="current_password"
                                class="admin-form-input @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">{{ __('New Password') }}</label>
                            <input type="password" id="password" name="password"
                                class="admin-form-input @error('password') is-invalid @enderror" required>
                            @error('password')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                            <div class="admin-text-muted">{{ __('Minimum 8 characters required') }}</div>
                        </div>

                        <div class="admin-form-group">
                            <label for="password_confirmation" class="admin-form-label">{{ __('Confirm New Password') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="admin-form-input"
                                required>
                        </div>

                        <div class="admin-card-footer">
                            <div class="admin-flex-end">
                                <button type="submit" class="admin-btn admin-btn-warning">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="12" cy="16" r="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    {{ __('Change Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Account Information -->
                <div class="admin-modern-card admin-mt-1-5">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ __('Account Information') }}
                        </h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-info-grid">
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                                        <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                                        <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    {{ __('Member Since') }}
                                </div>
                                <div class="admin-info-value">{{ $user->created_at->format('F j, Y') }}</div>
                            </div>

                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    {{ __('Last Login') }}
                                </div>
                                <div class="admin-info-value">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never') }}</div>
                            </div>

                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ __('User ID') }}
                                </div>
                                <div class="admin-info-value">{{ $user->id }}</div>
                            </div>

                            @if($user->balance !== null)
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
                                        <path d="M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6312 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 11.6312 16.9749 10.9749C17.6312 10.3185 18 9.42826 18 8.5C18 7.57174 17.6312 6.6815 16.9749 6.02513C16.3185 5.36875 15.4283 5 14.5 5H12" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    {{ __('Balance') }}
                                </div>
                                <div class="admin-info-value">
                                    <span class="admin-badge admin-badge-info">{{ number_format($user->balance, 2) }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection