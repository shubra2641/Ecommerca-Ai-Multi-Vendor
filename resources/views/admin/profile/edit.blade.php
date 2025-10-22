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
                        <i class="fas fa-user"></i>
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
                            <i class="fas fa-user"></i>
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
                                    {{ \Carbon\Carbon::parse($user->approved_at)->format('Y-m-d H:i') }}
                                </div>
                                @else
                                <span class="admin-status-badge admin-status-badge-warning">{{ __('Pending Approval') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="admin-card-footer">
                            <div class="admin-flex-end">
                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <i class="fas fa-save"></i>
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
                            <i class="fas fa-lock"></i>
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
                                    <i class="fas fa-lock"></i>
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
                            <i class="fas fa-bolt"></i>
                            {{ __('Account Information') }}
                        </h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-info-grid">
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ __('Member Since') }}
                                </div>
                                <div class="admin-info-value">{{ $user->created_at->format('F j, Y') }}</div>
                            </div>

                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <i class="fas fa-clock"></i>
                                    {{ __('Last Login') }}
                                </div>
                                <div class="admin-info-value">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never') }}</div>
                            </div>

                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <i class="fas fa-user"></i>
                                    {{ __('User ID') }}
                                </div>
                                <div class="admin-info-value">{{ $user->id }}</div>
                            </div>

                            @if($user->balance !== null)
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <i class="fas fa-dollar-sign"></i>
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