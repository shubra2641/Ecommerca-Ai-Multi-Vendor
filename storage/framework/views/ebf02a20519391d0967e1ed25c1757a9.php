<?php $__env->startSection('content'); ?>
<div class="container">
    <h1><?php echo e(__('Return Requests')); ?></h1>
    <div class="card modern-card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Order</th>
                <th>User</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($item->id); ?></td>
                <td><?php echo e($item->name); ?></td>
                <td><a href="<?php echo e(route('admin.orders.show', $item->order)); ?>">#<?php echo e($item->order_id); ?></a></td>
                <td><?php echo e($item->order->user?->name ?? $item->order->user_id); ?></td>
                <td><?php echo e($item->updated_at->toDateTimeString()); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo e($item->return_status); ?></span></td>
                        <td><a class="btn btn-sm btn-primary" href="<?php echo e(route('admin.returns.show', $item)); ?>"><?php echo e(__('View')); ?></a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
        </div>
    </div>
    <?php echo e($items->links()); ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/returns/index.blade.php ENDPATH**/ ?>