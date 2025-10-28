@extends('layouts.admin')

@section('title', __('System Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <i class="fas fa-server"></i>
                    {{ __('System Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('System health, performance and storage analysis') }}</p>
            </div>
        </div>

        <!-- System Health Overview -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">
                        @if(isset($systemData['health']['status']) && $systemData['health']['status'] === 'healthy')
                        {{ __('Healthy') }}
                        @else
                        {{ __('Warning') }}
                        @endif
                    </div>
                    <div class="admin-stat-label">{{ __('System Status') }}</div>
                    <div class="admin-stat-description">{{ __('System health') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('System health') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fab fa-php"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ PHP_VERSION }}">{{ PHP_VERSION }}</div>
                    <div class="admin-stat-label">{{ __('PHP Version') }}</div>
                    <div class="admin-stat-description">{{ __('Server version') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Server version') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fab fa-laravel"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ app()->version() }}">{{ app()->version() }}</div>
                    <div class="admin-stat-label">{{ __('Laravel Version') }}</div>
                    <div class="admin-stat-description">{{ __('Framework version') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Framework version') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">
                        @if(isset($systemData['health']['uptime']))
                        {{ $systemData['health']['uptime'] }}
                        @else
                        {{ __('N/A') }}
                        @endif
                    </div>
                    <div class="admin-stat-label">{{ __('Uptime') }}</div>
                    <div class="admin-stat-description">{{ __('System uptime') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('System uptime') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        @if(isset($systemData['performance']))
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fas fa-bolt"></i>
                    {{ __('Performance Metrics') }}
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <tbody>
                                    <tr>
                                        <td><strong>{{ __('Memory Usage') }}:</strong></td>
                                        <td>
                                            @if(isset($systemData['performance']['memory_usage']))
                                            {{ $systemData['performance']['memory_usage'] }}
                                            @else
                                            {{ number_format(memory_get_usage(true) / 1024 / 1024, 2) }} MB
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Peak Memory') }}:</strong></td>
                                        <td>
                                            @if(isset($systemData['performance']['peak_memory']))
                                            {{ $systemData['performance']['peak_memory'] }}
                                            @else
                                            {{ number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) }} MB
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Memory Limit') }}:</strong></td>
                                        <td>{{ ini_get('memory_limit') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Max Execution Time') }}:</strong></td>
                                        <td>{{ ini_get('max_execution_time') }}s</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <tbody>
                                    <tr>
                                        <td><strong>{{ __('Upload Max Size') }}:</strong></td>
                                        <td>{{ ini_get('upload_max_filesize') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Post Max Size') }}:</strong></td>
                                        <td>{{ ini_get('post_max_size') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Max Input Vars') }}:</strong></td>
                                        <td>{{ ini_get('max_input_vars') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Timezone') }}:</strong></td>
                                        <td>{{ config('app.timezone') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Storage Information -->
        @if(isset($systemData['storage']))
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fas fa-hdd"></i>
                    {{ __('Storage Information') }}
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    @if(isset($systemData['storage']['disk_usage']))
                    <div class="col-lg-6">
                        <h6 class="font-weight-bold">{{ __('Disk Usage') }}</h6>
                        <div class="progress mb-3">
                            <div class="progress-bar {{ $sysDiskClass ?? '' }}" role="progressbar" data-width="{{ $sysDiskPct ?? 0 }}" aria-valuenow="{{ $sysDiskPct ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $sysDiskPct ?? 0 }}%
                            </div>
                        </div>
                        <p class="text-muted small">
                            {{ $systemData['storage']['disk_usage']['used'] }} /
                            {{ $systemData['storage']['disk_usage']['total'] }}
                        </p>
                    </div>
                    @endif

                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <tbody>
                                    <tr>
                                        <td><strong>{{ __('Storage Path') }}:</strong></td>
                                        <td>{{ storage_path() }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Public Path') }}:</strong></td>
                                        <td>{{ public_path() }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Cache Path') }}:</strong></td>
                                        <td>{{ config('cache.default') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Session Driver') }}:</strong></td>
                                        <td>{{ config('session.driver') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Database Information -->
        @if(isset($systemData['database']))
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fas fa-database"></i>
                    {{ __('Database Information') }}
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <tbody>
                                    <tr>
                                        <td><strong>{{ __('Database Driver') }}:</strong></td>
                                        <td>{{ config('database.default') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Database Host') }}:</strong></td>
                                        <td>{{ config('database.connections.' . config('database.default') . '.host') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Database Name') }}:</strong></td>
                                        <td>{{ config('database.connections.' . config('database.default') . '.database') }}
                                        </td>
                                    </tr>
                                    @if(isset($systemData['database']['version']))
                                    <tr>
                                        <td><strong>{{ __('Database Version') }}:</strong></td>
                                        <td>{{ $systemData['database']['version'] }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        @if(isset($systemData['database']['tables_count']))
                        <div class="table-responsive">
                            <table class="admin-table">
                                <tbody>
                                    <tr>
                                        <td><strong>{{ __('Total Tables') }}:</strong></td>
                                        <td>{{ $systemData['database']['tables_count'] }}</td>
                                    </tr>
                                    @if(isset($systemData['database']['size']))
                                    <tr>
                                        <td><strong>{{ __('Database Size') }}:</strong></td>
                                        <td>{{ $systemData['database']['size'] }}</td>
                                    </tr>
                                    @endif
                                    @if(isset($systemData['database']['connection_status']))
                                    <tr>
                                        <td><strong>{{ __('Connection Status') }}:</strong></td>
                                        <td>
                                            <span
                                                class="admin-status-badge admin-status-badge-{{ $systemData['database']['connection_status'] === 'connected' ? 'completed' : 'warning' }}">
                                                {{ ucfirst($systemData['database']['connection_status']) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- System Environment -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fas fa-globe"></i>
                    {{ __('System Environment') }}
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <tbody>
                                    <tr>
                                        <td><strong>{{ __('Environment') }}:</strong></td>
                                        <td>
                                            <span
                                                class="admin-status-badge admin-status-badge-{{ app()->environment() === 'production' ? 'completed' : 'warning' }}">
                                                {{ ucfirst(app()->environment()) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Debug Mode') }}:</strong></td>
                                        <td>
                                            <span class="admin-status-badge admin-status-badge-{{ config('app.debug') ? 'warning' : 'completed' }}">
                                                {{ config('app.debug') ? __('Enabled') : __('Disabled') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Maintenance Mode') }}:</strong></td>
                                        <td>
                                            <span class="admin-status-badge admin-status-badge-completed">{{ __('Disabled') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Queue Driver') }}:</strong></td>
                                        <td>{{ config('queue.default') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <tbody>
                                    <tr>
                                        <td><strong>{{ __('Mail Driver') }}:</strong></td>
                                        <td>{{ config('mail.default') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Broadcast Driver') }}:</strong></td>
                                        <td>{{ config('broadcasting.default') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Filesystem Driver') }}:</strong></td>
                                        <td>{{ config('filesystems.default') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Log Channel') }}:</strong></td>
                                        <td>{{ config('logging.default') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection