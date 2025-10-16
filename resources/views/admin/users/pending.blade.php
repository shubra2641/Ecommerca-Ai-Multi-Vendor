@extends('layouts.admin')

@section('title', __('Pending Approval Users'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Users Pending Approval') }}</h1>
        <p class="page-description">{{ __('Review and approve pending user registrations') }}</p>
    </div>
</div>

<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Pending Users') }}</h3>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Registered') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $user->role === 'vendor' ? 'warning' : 'info' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->phone_number }}</td>
                                                <td>{{ $user->created_at->diffForHumans() }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary">
                                            {{ __('Review & Approve') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="mt-3">
                {{ $users->links() }}
            </div>
            @endif
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-check-circle fa-3x mb-3 text-muted"></i>
                <h5>{{ __('No Pending Users') }}</h5>
                <p>{{ __('All users have been approved!') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
