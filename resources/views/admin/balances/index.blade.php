@extends('layouts.admin')

@section('title', __('User Balances'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('User Balances') }}</h1>
        <p class="page-description">{{ __('View and export user balance information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.balances.export', ['format' => 'xlsx']) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            {{ __('Export XLSX') }}
        </a>
        <a href="{{ route('admin.balances.export', ['format' => 'pdf']) }}" class="btn btn-outline-secondary">
            <i class="fas fa-file-pdf"></i>
            {{ __('Export PDF') }}
        </a>
    </div>
</div>

<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('User Balances') }}</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{{ number_format($user->balance, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</div>
@endsection
