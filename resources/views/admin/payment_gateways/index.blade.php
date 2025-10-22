@extends('layouts.admin')

@section('title', __('Payment Gateways'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Payment Gateways') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage payment processing gateways') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.payment-gateways.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Gateway') }}
                </a>
            </div>
        </div>

        <!-- Payment Gateways Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-credit-card"></i>
                    {{ __('Payment Gateways') }}
                </h2>
                <div class="admin-badge-count">{{ $gateways->count() }} {{ __('Gateways') }}</div>
            </div>
            <div class="admin-card-body">
                @if($gateways->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Driver') }}</th>
                                <th>{{ __('Enabled') }}</th>
                                <th>{{ __('Requires Image') }}</th>
                                <th width="200">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gateways as $gateway)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div class="user-name">{{ $gateway->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-badge">{{ $gateway->driver }}</span>
                                </td>
                                <td>
                                    @if($gateway->enabled)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('Yes') }}
                                    </span>
                                    @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle"></i>
                                        {{ __('No') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($gateway->requires_transfer_image)
                                    <span class="badge bg-info">
                                        <i class="fas fa-image"></i>
                                        {{ __('Yes') }}
                                    </span>
                                    @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle"></i>
                                        {{ __('No') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.payment-gateways.edit', $gateway->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                            {{ __('Edit') }}
                                        </a>

                                        <form action="{{ route('admin.payment-gateways.toggle', $gateway->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $gateway->enabled ? 'btn-success' : 'btn-outline-secondary' }}">
                                                <i class="fas fa-check"></i>
                                                {{ $gateway->enabled ? __('Enabled') : __('Enable') }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.payment-gateways.destroy', $gateway->id) }}" method="POST" class="d-inline js-confirm" data-confirm="{{ __('Are you sure?') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="admin-empty-state">
                    <i class="fas fa-credit-card admin-notification-icon"></i>
                    <h3>{{ __('No Payment Gateways') }}</h3>
                    <p>{{ __('No payment gateways configured yet. Click Add Gateway to create one.') }}</p>
                    <a href="{{ route('admin.payment-gateways.create') }}" class="admin-btn admin-btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('Add First Gateway') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection