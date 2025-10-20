@extends('layouts.admin')

@section('title', __('System Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('System Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('System health, performance and storage analysis') }}</p>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary js-refresh-page" data-action="refresh">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 4v6h-6M1 20v-6h6m15-4a9 9 0 11-18 0 9 9 0 0118 0zM1 10a9 9 0 0118 0" />
                    </svg>
                    {{ __('Refresh') }}
                </button>
                <div class="dropdown">
                    <button class="admin-btn admin-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="system">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Excel') }}
                            </a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="system">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10,9 9,9 8,9" />
                                </svg>
                                {{ __('PDF') }}
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- System Health Overview -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('System health') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M10 20l4-16m-8 0l4 16M2 8h20M4 12h16" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ PHP_VERSION }}">{{ PHP_VERSION }}</div>
                    <div class="admin-stat-label">{{ __('PHP Version') }}</div>
                    <div class="admin-stat-description">{{ __('Server version') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('Server version') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ app()->version() }}">{{ app()->version() }}</div>
                    <div class="admin-stat-label">{{ __('Laravel Version') }}</div>
                    <div class="admin-stat-description">{{ __('Framework version') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('Framework version') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
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
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                    </svg>
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
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg>
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
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <ellipse cx="12" cy="5" rx="9" ry="3" />
                        <path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5" />
                        <path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3" />
                    </svg>
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
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set progress bar width dynamically
            const progressBars = document.querySelectorAll('.progress-bar[data-width]');
            progressBars.forEach(bar => {
                const width = bar.getAttribute('data-width');
                bar.style.width = width + '%';
            });
        });
    </script>
    @endpush
    @endsection