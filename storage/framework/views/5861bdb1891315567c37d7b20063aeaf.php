

<?php $__env->startSection('title', __('Vendors Report')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1"><?php echo e(__('Vendors Report')); ?></h1>
            <p class="text-muted mb-0"><?php echo e(__('Comprehensive vendors analysis and statistics')); ?></p>
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
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="vendors"><?php echo e(__('Excel')); ?></a>
                        </li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="vendors"><?php echo e(__('PDF')); ?></a>
                        </li>
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
                        <div class="stats-number"><?php echo e(number_format($stats['total'])); ?></div>
                        <div class="stats-label"><?php echo e(__('Total Vendors')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-store"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number"><?php echo e(number_format($stats['active'])); ?></div>
                        <div class="stats-label"><?php echo e(__('Active Vendors')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number"><?php echo e(number_format($stats['pending'])); ?></div>
                        <div class="stats-label"><?php echo e(__('Pending Vendors')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number">$<?php echo e(number_format($stats['totalBalance'], 2)); ?></div>
                        <div class="stats-label"><?php echo e(__('Total Balance')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Vendors List')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo e(__('ID')); ?></th>
                            <th><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Email')); ?></th>
                            <th><?php echo e(__('Balance')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                            <th><?php echo e(__('Joined Date')); ?></th>
                            <th><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($vendor->id); ?></td>
                            <td><?php echo e($vendor->name); ?></td>
                            <td><?php echo e($vendor->email); ?></td>
                            <td>$<?php echo e(number_format($vendor->balance, 2)); ?></td>
                            <td>
                                <?php if($vendor->approved_at): ?>
                                <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                                <?php else: ?>
                                <span class="badge bg-warning"><?php echo e(__('Pending')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($vendor->created_at->format('Y-m-d')); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('admin.users.show', $vendor->id)); ?>"
                                        class="btn btn-sm btn-outline-secondary" title="<?php echo e(__('View')); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p><?php echo e(__('No vendors found')); ?></p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($vendors->hasPages()): ?>
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($vendors->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/reports/vendors.blade.php ENDPATH**/ ?>