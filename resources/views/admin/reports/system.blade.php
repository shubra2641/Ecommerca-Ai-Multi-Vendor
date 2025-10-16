@extends('layouts.admin')

@section('title', __('System Report'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1">{{ __('System Report') }}</h1>
            <p class="text-muted mb-0">{{ __('System health, performance and storage analysis') }}</p>
        </div>
        <div class="page-actions">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary js-refresh-page" data-action="refresh">
                    <i class="fas fa-sync-alt"></i> {{ __('Refresh') }}
                </button>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download"></i> {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="system">{{ __('Excel') }}</a>
                        </li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="system">{{ __('PDF') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number">
                            @if(isset($systemData['health']['status']) && $systemData['health']['status'] === 'healthy')
                                {{ __('Healthy') }}
                            @else
                                {{ __('Warning') }}
                            @endif
                        </div>
                        <div class="stats-label">{{ __('System Status') }}</div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-heartbeat"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number">{{ PHP_VERSION }}</div>
                        <div class="stats-label">{{ __('PHP Version') }}</div>
                    </div>
                    <div class="stats-icon"><i class="fab fa-php"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number">{{ app()->version() }}</div>
                        <div class="stats-label">{{ __('Laravel Version') }}</div>
                    </div>
                    <div class="stats-icon"><i class="fab fa-laravel"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-primary h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number">
                            @if(isset($systemData['health']['uptime']))
                                {{ $systemData['health']['uptime'] }}
                            @else
                                {{ __('N/A') }}
                            @endif
                        </div>
                        <div class="stats-label">{{ __('Uptime') }}</div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    @if(isset($systemData['performance']))
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Performance Metrics') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                        <table class="table table-borderless">
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
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Storage Information') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @if(isset($systemData['storage']['disk_usage']))
                <div class="col-lg-6">
                    <h6 class="font-weight-bold">{{ __('Disk Usage') }}</h6>
                    <div class="progress mb-3">
                        <div class="progress-bar {{ $sysDiskClass ?? '' }}" role="progressbar" style="{{ 'width: '.($sysDiskPct ?? 0).'%;' }}" aria-valuenow="{{ $sysDiskPct ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
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
                        <table class="table table-striped">
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
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Database Information') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                        <table class="table table-striped">
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
                                            class="badge badge-{{ $systemData['database']['connection_status'] === 'connected' ? 'success' : 'danger' }}">
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
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('System Environment') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>{{ __('Environment') }}:</strong></td>
                                    <td>
                                        <span
                                            class="badge badge-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                            {{ ucfirst(app()->environment()) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Debug Mode') }}:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ config('app.debug') ? 'warning' : 'success' }}">
                                            {{ config('app.debug') ? __('Enabled') : __('Disabled') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Maintenance Mode') }}:</strong></td>
                                    <td>
                                        <span class="badge bg-success">{{ __('Disabled') }}</span>
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
                        <table class="table table-striped">
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

@push('scripts')
{{-- Inline handlers removed; refresh and export handled via delegated JS in inline-cleanup.js --}}
@endpush