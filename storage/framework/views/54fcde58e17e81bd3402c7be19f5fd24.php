<?php $__env->startSection('title', __('Users Report')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1"><?php echo e(__('Users Report')); ?></h1>
            <p class="text-muted mb-0"><?php echo e(__('Comprehensive analysis of user statistics and activity')); ?></p>
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
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="users"><?php echo e(__('Excel')); ?></a>
                        </li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="users"><?php echo e(__('PDF')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-primary h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($usersData['total_users'] ?? 0)); ?>">
                            <?php echo e(isset($usersData['total_users']) ? number_format($usersData['total_users']) : '0'); ?>

                        </div>
                        <div class="stats-label"><?php echo e(__('Total Users')); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($usersData['active_users'] ?? 0)); ?>">
                            <?php echo e(isset($usersData['active_users']) ? number_format($usersData['active_users']) : '0'); ?>

                        </div>
                        <div class="stats-label"><?php echo e(__('Active Users')); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($usersData['pending_users'] ?? 0)); ?>">
                            <?php echo e(isset($usersData['pending_users']) ? number_format($usersData['pending_users']) : '0'); ?>

                        </div>
                        <div class="stats-label"><?php echo e(__('Pending Users')); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($usersData['new_this_month'] ?? 0)); ?>">
                            <?php echo e(isset($usersData['new_this_month']) ? number_format($usersData['new_this_month']) : '0'); ?>

                        </div>
                        <div class="stats-label"><?php echo e(__('New This Month')); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Registration Chart -->
    <?php if(isset($usersData['registration_chart'])): ?>
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('User Registration Trends')); ?></h6>
        </div>
        <div class="card-body">
            <div class="chart-container h-400">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Role Distribution -->
    <?php if(isset($usersData['role_distribution'])): ?>
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card modern-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('User Role Distribution')); ?></h6>
                </div>
                <div class="card-body">
                    <div class="chart-container h-380 pt-4 pb-2">
                        <canvas id="roleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card modern-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Role Statistics')); ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <?php $__currentLoopData = $usersData['role_distribution']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo e($role === 'admin' ? 'danger' : ($role === 'vendor' ? 'warning' : 'primary')); ?>">
                                            <?php echo e(ucfirst($role)); ?>

                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <strong data-countup data-target="<?php echo e((int)$count); ?>"><?php echo e(number_format($count)); ?></strong>
                                    </td>
                                    <td class="text-right text-muted">
                                        <span data-countup data-decimals="1" data-target="<?php echo e(isset($usersData['total_users']) && $usersData['total_users'] > 0 ? number_format(($count / $usersData['total_users']) * 100, 1, '.', '') : '0'); ?>"><?php echo e(isset($usersData['total_users']) && $usersData['total_users'] > 0 ? number_format(($count / $usersData['total_users']) * 100, 1) : '0'); ?></span>%
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Users Table -->
    <?php if(isset($usersData['recent_users'])): ?>
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Recent Users')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Email')); ?></th>
                            <th><?php echo e(__('Role')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                            <th><?php echo e(__('Balance')); ?></th>
                            <th><?php echo e(__('Registered')); ?></th>
                            <th><?php echo e(__('Last Login')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $usersData['recent_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <?php if($user->avatar): ?>
                                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>"
                                            class="rounded-circle" width="32" height="32">
                                        <?php else: ?>
                                        <div class="avatar-initials rounded-circle bg-primary text-white d-flex align-items-center justify-content-center w-32 h-32 fs-14"
                                           >
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold"><?php echo e($user->name); ?></div>
                                        <?php if($user->phone): ?>
                                        <div class="text-muted small"><?php echo e($user->phone); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                <span
                                    class="badge badge-<?php echo e($user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'primary')); ?>">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo e($user->status === 'active' ? 'success' : 'warning'); ?>">
                                    <?php echo e(ucfirst($user->status)); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($user->balance): ?>
                                <span class="text-success font-weight-bold">
                                    <?php echo e(number_format($user->balance->amount, 2)); ?>

                                    <?php echo e($user->balance->currency ?? 'USD'); ?>

                                </span>
                                <?php else: ?>
                                <span class="text-muted"><?php echo e(__('No Balance')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-muted"><?php echo e($user->created_at->format('M d, Y')); ?></span>
                                <div class="small text-muted"><?php echo e($user->created_at->diffForHumans()); ?></div>
                            </td>
                            <td>
                                <?php if($user->last_login_at): ?>
                                <span class="text-muted"><?php echo e($user->last_login_at->format('M d, Y H:i')); ?></span>
                                <div class="small text-muted"><?php echo e($user->last_login_at->diffForHumans()); ?></div>
                                <?php else: ?>
                                <span class="text-muted"><?php echo e(__('Never')); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <?php echo e(__('No users found')); ?>

                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- User Activity Summary -->
    <?php if(isset($usersData['activity_summary'])): ?>
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('User Activity Summary')); ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-primary" data-countup data-target="<?php echo e((int)($usersData['activity_summary']['daily_active'] ?? 0)); ?>">
                            <?php echo e(isset($usersData['activity_summary']['daily_active']) ? number_format($usersData['activity_summary']['daily_active']) : '0'); ?>

                        </div>
                        <div class="text-muted"><?php echo e(__('Daily Active Users')); ?></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-success" data-countup data-target="<?php echo e((int)($usersData['activity_summary']['weekly_active'] ?? 0)); ?>">
                            <?php echo e(isset($usersData['activity_summary']['weekly_active']) ? number_format($usersData['activity_summary']['weekly_active']) : '0'); ?>

                        </div>
                        <div class="text-muted"><?php echo e(__('Weekly Active Users')); ?></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-info" data-countup data-target="<?php echo e((int)($usersData['activity_summary']['monthly_active'] ?? 0)); ?>">
                            <?php echo e(isset($usersData['activity_summary']['monthly_active']) ? number_format($usersData['activity_summary']['monthly_active']) : '0'); ?>

                        </div>
                        <div class="text-muted"><?php echo e(__('Monthly Active Users')); ?></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-warning" data-countup data-suffix=" min" data-target="<?php echo e(isset($usersData['activity_summary']['avg_session_duration']) ? preg_replace('/[^0-9.]/','',$usersData['activity_summary']['avg_session_duration']) : '0'); ?>">
                            <?php echo e(isset($usersData['activity_summary']['avg_session_duration']) ? $usersData['activity_summary']['avg_session_duration'] : '0 min'); ?>

                        </div>
                        <div class="text-muted"><?php echo e(__('Avg Session Duration')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script id="report-users-data" type="application/json"><?php echo json_encode([
    'charts'=>[
        'registration'=> isset($usersData['registration_chart']) ? [
            'labels'=>array_keys($usersData['registration_chart']),
            'values'=>array_values($usersData['registration_chart']),
            'label'=>__('New Users')
        ]: null,
        'roles'=> isset($usersData['role_distribution']) ? [
            'labels'=>array_map('ucfirst', array_keys($usersData['role_distribution'])),
            'values'=>array_values($usersData['role_distribution'])
        ]: null,
    ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/reports/users.blade.php ENDPATH**/ ?>