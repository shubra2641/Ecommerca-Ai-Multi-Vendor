@extends('layouts.admin')

@section('title', isset($user) && $user->exists ? __('Edit User') : __('Create User'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-start flex-wrap">
    <div>
        <h1 class="page-title">
            @if(isset($user) && $user->exists)
                {{ __('Edit User') }}: {{ $user->name }}
            @else
                {{ __('Create New User') }}
            @endif
        </h1>
        <p class="page-description">
            @if(isset($user) && $user->exists)
                {{ __('Update user information and settings') }}
            @else
                {{ __('Add a new user to the system') }}
            @endif
        </p>
    </div>
    <div class="page-actions mt-2">
        @if(isset($user) && $user->exists)
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary me-2">
                <i class="fas fa-eye"></i>
                {{ __('View User') }}
            </a>
        @endif
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to Users') }}
        </a>
    </div>
</div>

<form action="{{ $userFormAction }}" method="POST">
    @csrf
    @if(isset($user) && $user->exists)
        @method('PUT')
    @endif

    <div class="card modern-card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-user text-primary"></i>
            <h3 class="card-title mb-0">{{ __('Basic Information') }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="form-label required">{{ __('Full Name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $user->name ?? '') }}" required
                            placeholder="{{ __('Enter full name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="form-label required">{{ __('Email Address') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $user->email ?? '') }}" required
                            placeholder="{{ __('Enter email address') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card modern-card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0">{{ __('Password Information') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password"
                            class="form-label {{ isset($user) && $user->exists ? '' : 'required' }}">{{ __('Password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="{{ __('Enter password') }}"
                            {{ isset($user) && $user->exists ? '' : 'required' }}>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($user) && $user->exists)
                            <small class="form-text text-muted">{{ __('Leave blank to keep current password') }}</small>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation"
                            class="form-label {{ isset($user) && $user->exists ? '' : 'required' }}">{{ __('Confirm Password') }}</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" name="password_confirmation"
                            placeholder="{{ __('Confirm password') }}"
                            {{ isset($user) && $user->exists ? '' : 'required' }}>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card modern-card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0">{{ __('Contact Information') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                            placeholder="{{ __('Enter phone number') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="whatsapp" class="form-label">{{ __('WhatsApp Number') }}</label>
                        <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp"
                            name="whatsapp" value="{{ old('whatsapp', $user->whatsapp ?? '') }}"
                            placeholder="{{ __('Enter WhatsApp number') }}">
                        @error('whatsapp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card modern-card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0">{{ __('Role & Permissions') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role" class="form-label required">{{ __('Role') }}</label>
                        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">{{ __('Select Role') }}</option>
                            <option value="user" {{ old('role', $user->role ?? '') === 'user' ? 'selected' : '' }}>
                                {{ __('User') }}
                            </option>
                            <option value="vendor" {{ old('role', $user->role ?? '') === 'vendor' ? 'selected' : '' }}>
                                {{ __('Vendor') }}
                            </option>
                            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>
                                {{ __('Admin') }}
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="balance" class="form-label">{{ __('Balance') }}</label>
                        <input type="number" step="0.01" class="form-control @error('balance') is-invalid @enderror"
                            id="balance" name="balance" value="{{ old('balance', $user->balance ?? 0) }}"
                            placeholder="{{ __('Enter balance') }}">
                        @error('balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="approved" name="approved" value="1"
                            {{ old('approved', (isset($user) && $user->exists && $user->approved_at) ? '1' : '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="approved">
                            {{ __('Approved User') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card modern-card">
                <div class="card-body">
                    <div class="form-actions d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            @if(isset($user) && $user->exists)
                                <i class="fas fa-save"></i>
                                {{ __('Update User') }}
                            @else
                                <i class="fas fa-plus"></i>
                                {{ __('Create User') }}
                            @endif
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

            <div class="col-md-4">
            @if(isset($user) && $user->exists)
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <h3 class="card-title mb-0">{{ __('User Summary') }}</h3>
                </div>
                <div class="card-body">
                    <div class="user-summary">
                        <div class="summary-item">
                            <label>{{ __('Created') }}:</label>
                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="summary-item">
                            <label>{{ __('Status') }}:</label>
                            <span>
                                @if($user->approved_at)
                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="summary-item">
                            <label>{{ __('Role') }}:</label>
                            <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                        </div>
                    </div>

                    @if(!$user->approved_at)
                    <div class="quick-actions">
                        <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i>
                                {{ __('Approve User') }}
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</form>

@endsection