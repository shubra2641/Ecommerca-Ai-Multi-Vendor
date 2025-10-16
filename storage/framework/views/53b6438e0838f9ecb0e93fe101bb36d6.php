

<?php $__env->startSection('content'); ?>
<h1><?php echo e(__('Payments')); ?></h1>
<table class="table">
    <thead><tr><th><?php echo e(__('ID')); ?></th><th><?php echo e(__('Order')); ?></th><th><?php echo e(__('User')); ?></th><th><?php echo e(__('Method')); ?></th><th><?php echo e(__('Amount')); ?></th><th><?php echo e(__('Status')); ?></th><th><?php echo e(__('Created')); ?></th></tr></thead>
    <tbody>
    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($p->id); ?></td>
            <td><a href="<?php echo e(route('admin.orders.show', $p->order_id)); ?>">#<?php echo e($p->order_id); ?></a></td>
            <td><?php echo e($p->user->email ?? __('Guest')); ?></td>
            <td><?php echo e($p->method); ?></td>
            <td><?php echo e($p->amount); ?></td>
            <td><?php echo e($p->status); ?></td>
            <td><?php echo e($p->created_at); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    </table>
    <?php echo e($payments->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/orders/payments.blade.php ENDPATH**/ ?>