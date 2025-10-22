@extends('layouts.admin')

@section('title', isset($user) && $user->exists ? __('Edit User') : __('Create User'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    @if(isset($user) && $user->exists)
                    {{ __('Edit User') }}: {{ $user->name }}
                    @else
                    {{ __('Create New User') }}
                    @endif
                </h1>
                <p class="admin-order-subtitle">
                    @if(isset($user) && $user->exists)
                    {{ __('Update user information and settings') }}
                    @else
                    {{ __('Add a new user to the system') }}
                    @endif
                </p>
            </div>
            <div class="header-actions">
                @if(isset($user) && $user->exists)
                <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    {{ __('View User') }}
                </a>
                @endif
                <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12,19 5,12 12,5"></polyline>
                    </svg>
                    {{ __('Back to Users') }}
                </a>
            </div>
        </div>

        <form action="{{ $userFormAction }}" method="POST">
            @csrf
            @if(isset($user) && $user->exists)
            @method('PUT')
            @endif

            <!-- Basic Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        {{ __('Basic Information') }}
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="name" class="admin-form-label required">{{ __('Full Name') }}</label>
                            <input type="text" class="admin-form-input @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name ?? '') }}" required
                                placeholder="{{ __('Enter full name') }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label required">{{ __('Email Address') }}</label>
                            <input type="email" class="admin-form-input @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email ?? '') }}" required
                                placeholder="{{ __('Enter email address') }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <circle cx="12" cy="16" r="1"></circle>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        {{ __('Password Information') }}
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label {{ isset($user) && $user->exists ? '' : 'required' }}">{{ __('Password') }}</label>
                            <input type="password" class="admin-form-input @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="{{ __('Enter password') }}"
                                {{ isset($user) && $user->exists ? '' : 'required' }}>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($user) && $user->exists)
                            <small class="form-text text-muted">{{ __('Leave blank to keep current password') }}</small>
                            @endif
                        </div>

                        <div class="admin-form-group">
                            <label for="password_confirmation" class="admin-form-label {{ isset($user) && $user->exists ? '' : 'required' }}">{{ __('Confirm Password') }}</label>
                            <input type="password" class="admin-form-input @error('password_confirmation') is-invalid @enderror"
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

            <!-- Contact Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        {{ __('Contact Information') }}
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="phone" class="admin-form-label">{{ __('Phone Number') }}</label>
                            <input type="text" class="admin-form-input @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                placeholder="{{ __('Enter phone number') }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="whatsapp" class="admin-form-label">{{ __('WhatsApp Number') }}</label>
                            <input type="text" class="admin-form-input @error('whatsapp') is-invalid @enderror" id="whatsapp"
                                name="whatsapp" value="{{ old('whatsapp', $user->whatsapp ?? '') }}"
                                placeholder="{{ __('Enter WhatsApp number') }}">
                            @error('whatsapp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role & Permissions -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                        {{ __('Role & Permissions') }}
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="role" class="admin-form-label required">{{ __('Role') }}</label>
                            <select class="admin-form-select @error('role') is-invalid @enderror" id="role" name="role" required>
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

                        <div class="admin-form-group">
                            <label for="balance" class="admin-form-label">{{ __('Balance') }}</label>
                            <input type="number" step="0.01" class="admin-form-input @error('balance') is-invalid @enderror"
                                id="balance" name="balance" value="{{ old('balance', $user->balance ?? 0) }}"
                                placeholder="{{ __('Enter balance') }}">
                            @error('balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="admin-form-group">
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

            <!-- Form Actions -->
            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <div class="admin-flex-end">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            @if(isset($user) && $user->exists)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17,21 17,13 7,13 7,21"></polyline>
                                <polyline points="7,3 7,8 15,8"></polyline>
                            </svg>
                            {{ __('Update User') }}
                            @else
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            {{ __('Create User') }}
                            @endif
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>

            @if(isset($user) && $user->exists)
            <!-- User Summary -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        {{ __('User Summary') }}
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-row">
                            <div class="admin-info-label">{{ __('Created') }}:</div>
                            <div class="admin-info-value">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="admin-info-row">
                            <div class="admin-info-label">{{ __('Status') }}:</div>
                            <div class="admin-info-value">
                                @if($user->approved_at)
                                <span class="admin-status-badge admin-status-badge-success">{{ __('Approved') }}</span>
                                @else
                                <span class="admin-status-badge admin-status-badge-warning">{{ __('Pending') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="admin-info-row">
                            <div class="admin-info-label">{{ __('Role') }}:</div>
                            <div class="admin-info-value">
                                <span class="admin-status-badge admin-status-badge-primary">{{ ucfirst($user->role) }}</span>
                            </div>
                        </div>
                    </div>

                    @if(!$user->approved_at)
                    <div class="admin-mt-half">
                        <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-success">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12"></polyline>
                                </svg>
                                {{ __('Approve User') }}
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection