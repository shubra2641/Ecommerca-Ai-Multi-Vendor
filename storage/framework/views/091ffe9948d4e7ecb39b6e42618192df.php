

<?php $__env->startSection('content'); ?>
<h1><?php echo e(__('Orders')); ?></h1>
<table class="table align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th><?php echo e(__('Items')); ?></th>
            <th><?php echo e(__('Customer')); ?></th>
            <th><?php echo e(__('Shipping')); ?></th>
            <th><?php echo e(__('Total')); ?></th>
            <th><?php echo e(__('Status')); ?></th>
            <th><?php echo e(__('Created')); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><span class="badge bg-secondary">#<?php echo e($order->id); ?></span></td>
            <td class="max-w-220">
                <?php if(($ordersPrepared[$order->id]['firstItem'] ?? null)): ?>
                <strong><?php echo e($ordersPrepared[$order->id]['firstItem']->name); ?></strong>
                <?php if($ordersPrepared[$order->id]['variantLabel']): ?><div class="text-muted small"><?php echo e($ordersPrepared[$order->id]['variantLabel']); ?></div><?php endif; ?>
                <?php if($order->items->count()>1): ?>
                <div class="small text-muted">+ <?php echo e($order->items->count()-1); ?> <?php echo e(__('more')); ?></div>
                <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <div class="small fw-bold"><?php echo e($order->user->name ?? __('Guest')); ?></div>
                <div class="text-muted small"><?php echo e($order->user->email ?? ''); ?></div>
            </td>
            <td>
                <div class="small"><?php echo e(e($ordersPrepared[$order->id]['shipText'] ?? '')); ?></div>
            </td>
            <td><?php echo e(number_format($order->total,2)); ?> <?php echo e($order->currency); ?></td>
            <td>
                <span class="badge bg-info text-dark"><?php echo e(ucfirst($order->status)); ?></span><br>
                <span
                    class="badge bg-<?php echo e($order->payment_status==='paid' ? 'success':'warning'); ?> mt-1"><?php echo e(ucfirst($order->payment_status)); ?></span>
            </td>
            <td class="small"><?php echo e($order->created_at->format('Y-m-d H:i')); ?></td>
            <td><a class="btn btn-sm btn-outline-primary"
                    href="<?php echo e(route('admin.orders.show', $order->id)); ?>"><?php echo e(__('View')); ?></a></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php echo e($orders->links()); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>