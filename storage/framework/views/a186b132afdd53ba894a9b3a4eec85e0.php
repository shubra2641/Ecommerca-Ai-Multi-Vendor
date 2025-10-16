<?php $__env->startSection('title', __('Currency Details')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Currency Details')); ?></h1>
        <p class="page-description"><?php echo e(__('View and manage currency information')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.currencies.edit', $currency)); ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            <?php echo e(__('Edit Currency')); ?>

        </a>
        <a href="<?php echo e(route('admin.currencies.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to List')); ?>

        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    <?php echo e(__('Currency Information')); ?>

                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Name')); ?></label>
                            <div class="info-value"><?php echo e($currency->name); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Code')); ?></label>
                            <div class="info-value"><?php echo e($currency->code); ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Symbol')); ?></label>
                            <div class="info-value"><?php echo e($currency->symbol); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Exchange Rate')); ?></label>
                            <div class="info-value"><?php echo e(number_format($currency->exchange_rate, 4)); ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Status')); ?></label>
                            <div class="info-value">
                                <?php if($currency->is_active): ?>
                                <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
            <?php else: ?>
                <span class="badge bg-secondary"><?php echo e(__('Inactive')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Default Currency')); ?></label>
                            <div class="info-value">
                                <?php if($currency->is_default): ?>
                                <span class="badge badge-primary"><?php echo e(__('Yes')); ?></span>
                                <?php else: ?>
                                <span class="badge bg-secondary"><?php echo e(__('No')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Created At')); ?></label>
                            <div class="info-value"><?php echo e($currency->created_at->format('Y-m-d H:i:s')); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label"><?php echo e(__('Last Updated')); ?></label>
                            <div class="info-value"><?php echo e($currency->updated_at->format('Y-m-d H:i:s')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt text-primary"></i>
                    <?php echo e(__('Quick Actions')); ?>

                </h3>
            </div>
            <div class="card-body">
                <?php if(!$currency->is_default): ?>
                <form action="<?php echo e(route('admin.currencies.set-default', $currency)); ?>" method="POST" class="mb-3">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-star"></i>
                        <?php echo e(__('Set as Default')); ?>

                    </button>
                </form>
                <?php endif; ?>

                <a href="<?php echo e(route('admin.currencies.edit', $currency)); ?>" class="btn btn-warning btn-block mb-3">
                    <i class="fas fa-edit"></i>
                    <?php echo e(__('Edit Currency')); ?>

                </a>

                <?php if(!$currency->is_default): ?>
                <form action="<?php echo e(route('admin.currencies.destroy', $currency)); ?>" method="POST" class="js-confirm" data-confirm="<?php echo e(__('Are you sure you want to delete this currency?')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-trash"></i>
                        <?php echo e(__('Delete Currency')); ?>

                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/currencies/show.blade.php ENDPATH**/ ?>