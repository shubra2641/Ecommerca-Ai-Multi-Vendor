<?php $__env->startSection('title', __('Payment Gateways')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><?php echo e(__('Payment Gateways')); ?></h1>
    <a href="<?php echo e(route('admin.payment-gateways.create')); ?>" class="btn btn-primary"><?php echo e(__('Add Gateway')); ?></a>
</div>



<table class="table">
    <thead>
        <tr>
            <th><?php echo e(__('Name')); ?></th>
            <th><?php echo e(__('Driver')); ?></th>
            <th><?php echo e(__('Enabled')); ?></th>
            <th><?php echo e(__('Requires Image')); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($g->name); ?></td>
            <td><?php echo e($g->driver); ?></td>
            <td><?php echo e($g->enabled ? __('Yes') : __('No')); ?></td>
            <td><?php echo e($g->requires_transfer_image ? __('Yes') : __('No')); ?></td>
            <td>
                <a href="<?php echo e(route('admin.payment-gateways.edit', $g->id)); ?>"
                    class="btn btn-sm btn-outline-secondary"><?php echo e(__('Edit')); ?></a>
                <form action="<?php echo e(route('admin.payment-gateways.toggle', $g->id)); ?>" method="POST"
                    class="d-inline-block">
                    <?php echo csrf_field(); ?>
                    <button
                        class="btn btn-sm <?php echo e($g->enabled ? 'btn-success' : 'btn-outline-secondary'); ?>"><?php echo e($g->enabled ? __('Enabled') : __('Enable')); ?></button>
                </form>
                <form action="<?php echo e(route('admin.payment-gateways.destroy', $g->id)); ?>" method="POST"
                    class="d-inline-block js-confirm-delete" data-confirm="<?php echo e(__('Are you sure?')); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-danger"><?php echo e(__('Delete')); ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/payment-gateways.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/payment_gateways/index.blade.php ENDPATH**/ ?>