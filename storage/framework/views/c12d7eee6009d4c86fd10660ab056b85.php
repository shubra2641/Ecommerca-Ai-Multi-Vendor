<?php $__env->startSection('title', __('System Report')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1"><?php echo e(__('System Report')); ?></h1>
            <p class="text-muted mb-0"><?php echo e(__('System health, performance and storage analysis')); ?></p>
        </div>
        <div class="page-actions">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary js-refresh-page" data-action="refresh">
                    <i class="fas fa-sync-alt"></i> <?php echo e(__('Refresh')); ?>

                </button>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download"></i> <?php echo e(__('Export')); ?>

                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="system"><?php echo e(__('Excel')); ?></a>
                        </li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="system"><?php echo e(__('PDF')); ?></a></li>
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
                            <?php if(isset($systemData['health']['status']) && $systemData['health']['status'] === 'healthy'): ?>
                                <?php echo e(__('Healthy')); ?>

                            <?php else: ?>
                                <?php echo e(__('Warning')); ?>

                            <?php endif; ?>
                        </div>
                        <div class="stats-label"><?php echo e(__('System Status')); ?></div>
                        <div class="stats-trend">
                            <i class="fas fa-heartbeat text-success"></i>
                            <span class="text-success"><?php echo e(__('System health')); ?></span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-heartbeat"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number"><?php echo e(PHP_VERSION); ?></div>
                        <div class="stats-label"><?php echo e(__('PHP Version')); ?></div>
                        <div class="stats-trend">
                            <i class="fab fa-php text-info"></i>
                            <span class="text-info"><?php echo e(__('Server version')); ?></span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fab fa-php"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number"><?php echo e(app()->version()); ?></div>
                        <div class="stats-label"><?php echo e(__('Laravel Version')); ?></div>
                        <div class="stats-trend">
                            <i class="fab fa-laravel text-warning"></i>
                            <span class="text-warning"><?php echo e(__('Framework version')); ?></span>
                        </div>
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
                            <?php if(isset($systemData['health']['uptime'])): ?>
                                <?php echo e($systemData['health']['uptime']); ?>

                            <?php else: ?>
                                <?php echo e(__('N/A')); ?>

                            <?php endif; ?>
                        </div>
                        <div class="stats-label"><?php echo e(__('Uptime')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <?php if(isset($systemData['performance'])): ?>
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Performance Metrics')); ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo e(__('Memory Usage')); ?>:</strong></td>
                                    <td>
                                        <?php if(isset($systemData['performance']['memory_usage'])): ?>
                                        <?php echo e($systemData['performance']['memory_usage']); ?>

                                        <?php else: ?>
                                        <?php echo e(number_format(memory_get_usage(true) / 1024 / 1024, 2)); ?> MB
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Peak Memory')); ?>:</strong></td>
                                    <td>
                                        <?php if(isset($systemData['performance']['peak_memory'])): ?>
                                        <?php echo e($systemData['performance']['peak_memory']); ?>

                                        <?php else: ?>
                                        <?php echo e(number_format(memory_get_peak_usage(true) / 1024 / 1024, 2)); ?> MB
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Memory Limit')); ?>:</strong></td>
                                    <td><?php echo e(ini_get('memory_limit')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Max Execution Time')); ?>:</strong></td>
                                    <td><?php echo e(ini_get('max_execution_time')); ?>s</td>
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
                                    <td><strong><?php echo e(__('Upload Max Size')); ?>:</strong></td>
                                    <td><?php echo e(ini_get('upload_max_filesize')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Post Max Size')); ?>:</strong></td>
                                    <td><?php echo e(ini_get('post_max_size')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Max Input Vars')); ?>:</strong></td>
                                    <td><?php echo e(ini_get('max_input_vars')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Timezone')); ?>:</strong></td>
                                    <td><?php echo e(config('app.timezone')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Storage Information -->
    <?php if(isset($systemData['storage'])): ?>
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Storage Information')); ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <?php if(isset($systemData['storage']['disk_usage'])): ?>
                <div class="col-lg-6">
                    <h6 class="font-weight-bold"><?php echo e(__('Disk Usage')); ?></h6>
                    <div class="progress mb-3">
                        <div class="progress-bar <?php echo e($sysDiskClass ?? ''); ?>" role="progressbar" style="<?php echo e('width: '.($sysDiskPct ?? 0).'%;'); ?>" aria-valuenow="<?php echo e($sysDiskPct ?? 0); ?>" aria-valuemin="0" aria-valuemax="100">
                            <?php echo e($sysDiskPct ?? 0); ?>%
                        </div>
                    </div>
                    <p class="text-muted small">
                        <?php echo e($systemData['storage']['disk_usage']['used']); ?> /
                        <?php echo e($systemData['storage']['disk_usage']['total']); ?>

                    </p>
                </div>
                <?php endif; ?>

                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo e(__('Storage Path')); ?>:</strong></td>
                                    <td><?php echo e(storage_path()); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Public Path')); ?>:</strong></td>
                                    <td><?php echo e(public_path()); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Cache Path')); ?>:</strong></td>
                                    <td><?php echo e(config('cache.default')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Session Driver')); ?>:</strong></td>
                                    <td><?php echo e(config('session.driver')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Database Information -->
    <?php if(isset($systemData['database'])): ?>
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Database Information')); ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo e(__('Database Driver')); ?>:</strong></td>
                                    <td><?php echo e(config('database.default')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Database Host')); ?>:</strong></td>
                                    <td><?php echo e(config('database.connections.' . config('database.default') . '.host')); ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Database Name')); ?>:</strong></td>
                                    <td><?php echo e(config('database.connections.' . config('database.default') . '.database')); ?>

                                    </td>
                                </tr>
                                <?php if(isset($systemData['database']['version'])): ?>
                                <tr>
                                    <td><strong><?php echo e(__('Database Version')); ?>:</strong></td>
                                    <td><?php echo e($systemData['database']['version']); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?php if(isset($systemData['database']['tables_count'])): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo e(__('Total Tables')); ?>:</strong></td>
                                    <td><?php echo e($systemData['database']['tables_count']); ?></td>
                                </tr>
                                <?php if(isset($systemData['database']['size'])): ?>
                                <tr>
                                    <td><strong><?php echo e(__('Database Size')); ?>:</strong></td>
                                    <td><?php echo e($systemData['database']['size']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(isset($systemData['database']['connection_status'])): ?>
                                <tr>
                                    <td><strong><?php echo e(__('Connection Status')); ?>:</strong></td>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo e($systemData['database']['connection_status'] === 'connected' ? 'success' : 'danger'); ?>">
                                            <?php echo e(ucfirst($systemData['database']['connection_status'])); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- System Environment -->
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('System Environment')); ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo e(__('Environment')); ?>:</strong></td>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo e(app()->environment() === 'production' ? 'success' : 'warning'); ?>">
                                            <?php echo e(ucfirst(app()->environment())); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Debug Mode')); ?>:</strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo e(config('app.debug') ? 'warning' : 'success'); ?>">
                                            <?php echo e(config('app.debug') ? __('Enabled') : __('Disabled')); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Maintenance Mode')); ?>:</strong></td>
                                    <td>
                                        <span class="badge bg-success"><?php echo e(__('Disabled')); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Queue Driver')); ?>:</strong></td>
                                    <td><?php echo e(config('queue.default')); ?></td>
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
                                    <td><strong><?php echo e(__('Mail Driver')); ?>:</strong></td>
                                    <td><?php echo e(config('mail.default')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Broadcast Driver')); ?>:</strong></td>
                                    <td><?php echo e(config('broadcasting.default')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Filesystem Driver')); ?>:</strong></td>
                                    <td><?php echo e(config('filesystems.default')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Log Channel')); ?>:</strong></td>
                                    <td><?php echo e(config('logging.default')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/reports/system.blade.php ENDPATH**/ ?>