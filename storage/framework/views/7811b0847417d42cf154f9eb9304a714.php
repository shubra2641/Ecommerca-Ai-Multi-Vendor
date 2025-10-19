<?php $__env->startSection('title', __('User Balances')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('User Balances')); ?></h1>
        <p class="page-description"><?php echo e(__('View and export user balance information')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.balances.export', ['format' => 'xlsx'])); ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            <?php echo e(__('Export XLSX')); ?>

        </a>
        <a href="<?php echo e(route('admin.balances.export', ['format' => 'pdf'])); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-file-pdf"></i>
            <?php echo e(__('Export PDF')); ?>

        </a>
    </div>
</div>

<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title"><?php echo e(__('User Balances')); ?></h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo e(__('Name')); ?></th>
                        <th><?php echo e(__('Email')); ?></th>
                        <th><?php echo e(__('Role')); ?></th>
                        <th><?php echo e(__('Balance')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e(ucfirst($user->role)); ?></td>
                            <td><?php echo e(number_format($user->balance, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php echo e($users->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/balances/index.blade.php ENDPATH**/ ?>