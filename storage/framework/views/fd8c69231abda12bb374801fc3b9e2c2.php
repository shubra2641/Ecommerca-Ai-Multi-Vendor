<?php $__env->startSection('title', __('User Details')); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('User Details')); ?>: <?php echo e($user->name); ?></h1>
        <p class="page-description"><?php echo e(__('View and manage user information')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            <?php echo e(__('Edit User')); ?>

        </a>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to Users')); ?>

        </a>
    </div>
</div>

<!-- User Info Cards -->
<div class="row">
    <div class="col-md-4">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-circle text-primary"></i>
                <h3 class="card-title mb-0"><?php echo e(__('User Profile')); ?></h3>
            </div>
            <div class="card-body text-center">
                <div class="user-avatar-large">
                    <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                </div>
                <h4 class="mt-3"><?php echo e($user->name); ?></h4>
                <p class="text-muted"><?php echo e($user->email); ?></p>

                <div class="user-status">
                    <?php if($user->approved_at): ?>
                    <span class="badge bg-success">
                        <i class="fas fa-check"></i>
                        <?php echo e(__('Approved')); ?>

                    </span>
                    <?php else: ?>
                    <span class="badge bg-warning">
                        <i class="fas fa-clock"></i>
                        <?php echo e(__('Pending Approval')); ?>

                    </span>
                    <?php endif; ?>
                </div>

                <?php if(!$user->approved_at): ?>
                <div class="mt-3">
                    <form action="<?php echo e(route('admin.users.approve', $user)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-sm" data-confirm="approve-user"
                            data-confirm-message="<?php echo e(__('Are you sure you want to approve this user?')); ?>">
                            <i class="fas fa-check"></i>
                            <?php echo e(__('Approve User')); ?>

                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-bolt text-warning"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Quick Actions')); ?></h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i>
                        <?php echo e(__('Edit Profile')); ?>

                    </a>
                    <a href="<?php echo e(route('admin.users.balance', $user)); ?>" class="btn btn-outline-success">
                        <i class="fas fa-wallet"></i>
                        <?php echo e(__('Manage Balance')); ?>

                    </a>
                    <a href="<?php echo e(route('admin.users.activity', $user)); ?>" class="btn btn-outline-info">
                        <i class="fas fa-history"></i>
                        <?php echo e(__('View Activity')); ?>

                    </a>
                    <?php if($user->role === 'vendor'): ?>
                    <span class="btn btn-outline-warning disabled">
                        <i class="fas fa-store"></i>
                        <?php echo e(__('Vendor Details')); ?>

                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

        <div class="col-md-8">
        <!-- User Details -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-id-card text-info"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Personal Information')); ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><?php echo e(__('Full Name')); ?></label>
                            <div class="info-value"><?php echo e($user->name); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><?php echo e(__('Email Address')); ?></label>
                            <div class="info-value"><?php echo e($user->email); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><?php echo e(__('Phone Number')); ?></label>
                            <div class="info-value"><?php echo e($user->phone ?? __('Not provided')); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><?php echo e(__('Role')); ?></label>
                            <div class="info-value">
                                <?php switch($user->role):
                                case ('admin'): ?>
                                <span class="badge bg-danger"><?php echo e(__('Admin')); ?></span>
                                <?php break; ?>
                                <?php case ('vendor'): ?>
                                <span class="badge bg-warning"><?php echo e(__('Vendor')); ?></span>
                                <?php break; ?>
                                <?php default: ?>
                                <span class="badge bg-secondary"><?php echo e(__('Customer')); ?></span>
                                <?php endswitch; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><?php echo e(__('Account Balance')); ?></label>
                            <div class="info-value text-success">
                                $<?php echo e(number_format($user->balance ?? 0, 2)); ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><?php echo e(__('Member Since')); ?></label>
                            <div class="info-value"><?php echo e($user->created_at->format('F j, Y')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Status -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-shield-check text-success"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Account Status')); ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="status-item">
                            <div class="status-icon <?php echo e($user->approved_at ? 'text-success' : 'text-warning'); ?>">
                                <i class="fas <?php echo e($user->approved_at ? 'fa-check-circle' : 'fa-clock'); ?>"></i>
                            </div>
                            <div class="status-content">
                                <h5><?php echo e(__('Approval Status')); ?></h5>
                                <p>
                                    <?php if($user->approved_at): ?>
                                    <?php echo e(__('Approved on')); ?> <?php echo e($user->approved_at->format('Y-m-d H:i')); ?>

                                    <?php else: ?>
                                    <?php echo e(__('Pending approval since')); ?> <?php echo e($user->created_at->format('Y-m-d H:i')); ?>

                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="status-item">
                            <div class="status-icon <?php echo e($user->email_verified_at ? 'text-success' : 'text-danger'); ?>">
                                <i
                                    class="fas <?php echo e($user->email_verified_at ? 'fa-shield-check' : 'fa-shield-exclamation'); ?>"></i>
                            </div>
                            <div class="status-content">
                                <h5><?php echo e(__('Email Verification')); ?></h5>
                                <p>
                                    <?php if($user->email_verified_at): ?>
                                    <?php echo e(__('Verified on')); ?> <?php echo e($user->email_verified_at->format('Y-m-d H:i')); ?>

                                    <?php else: ?>
                                    <?php echo e(__('Email not verified')); ?>

                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-history text-secondary"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Recent Activity')); ?></h3>
                <div class="card-actions ms-auto">
                    <a href="<?php echo e(route('admin.users.activity', $user)); ?>" class="btn btn-sm btn-outline-primary">
                        <?php echo e(__('View All')); ?>

                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if(isset($recentActivity) && count($recentActivity) > 0): ?>
                <div class="activity-timeline">
                    <?php $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas <?php echo e($activity['icon'] ?? 'fa-circle'); ?>"></i>
                        </div>
                        <div class="activity-content">
                            <h4><?php echo e($activity['title']); ?></h4>
                            <p><?php echo e($activity['description']); ?></p>
                            <small><?php echo e($activity['created_at']->diffForHumans()); ?></small>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-history fa-2x"></i>
                    <h4><?php echo e(__('No Recent Activity')); ?></h4>
                    <p><?php echo e(__('This user has no recent activity to display.')); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/users/show.blade.php ENDPATH**/ ?>