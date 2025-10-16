@extends('layouts.admin')

@section('title', __('Profile Settings'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('Profile Settings') }}</h1>
    <p class="page-description">{{ __('Manage your account information and security settings') }}</p>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-md-6">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Profile Information') }}</h3>
            </div>
            <form action="{{ route('admin.profile.update') }}" method="POST" class="card-body">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Full Name') }}</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone_number" class="form-label">{{ __('Phone Number') }}</label>
                    <input type="text" id="phone_number" name="phone_number"
                        class="form-control @error('phone_number') is-invalid @enderror"
                        value="{{ old('phone_number', $user->phone_number) }}">
                    @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Role') }}</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly disabled>
                    <small class="form-text text-muted">{{ __('Your role cannot be changed') }}</small>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Account Status') }}</label>
                    <div class="d-flex align-items-center">
                        @if($user->approved_at)
                        <span class="badge bg-success">{{ __('Approved') }}</span>
                        <small class="text-muted ms-2">{{ __('Approved on') }}
                            {{ \Carbon\Carbon::parse($user->approved_at)->format('Y-m-d H:i') }}</small>
                        @else
                        <span class="badge bg-warning">{{ __('Pending Approval') }}</span>
                        @endif
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        {{ __('Update Profile') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Change -->
    <div class="col-md-6">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Change Password') }}</h3>
            </div>
            <form action="{{ route('admin.profile.password') }}" method="POST" class="card-body">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                    <input type="password" id="current_password" name="current_password"
                        class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">{{ __('New Password') }}</label>
                    <input type="password" id="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">{{ __('Minimum 8 characters required') }}</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                        required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i>
                        {{ __('Change Password') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Information -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">{{ __('Account Information') }}</h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>{{ __('Member Since') }}</label>
                    <span>{{ $user->created_at->format('F j, Y') }}</span>
                </div>

                <div class="info-item">
                    <label>{{ __('Last Login') }}</label>
                    <span>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never') }}</span>
                </div>

                <div class="info-item">
                    <label>{{ __('User ID') }}</label>
                    <span>{{ $user->id }}</span>
                </div>

                @if($user->balance !== null)
                <div class="info-item">
                    <label>{{ __('Balance') }}</label>
                    <span class="badge bg-info">{{ number_format($user->balance, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection