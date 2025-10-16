<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h3><?php echo e(__('Product Reviews')); ?></h3>
    <div class="card modern-card">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-striped mb-0">
        <thead><tr><th><?php echo e(__('ID')); ?></th><th><?php echo e(__('Product')); ?></th><th><?php echo e(__('User')); ?></th><th><?php echo e(__('Rating')); ?></th><th><?php echo e(__('Title')); ?></th><th><?php echo e(__('Images')); ?></th><th><?php echo e(__('Created')); ?></th><th><?php echo e(__('Approved')); ?></th><th><?php echo e(__('Actions')); ?></th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($r->id); ?></td>
                <td><?php echo e($r->product?->name); ?></td>
                <td><?php echo e($r->user?->email ?? __('Guest')); ?></td>
                <td><?php echo e($r->rating); ?></td>
                <td><?php echo e(Str::limit($r->title,40)); ?></td>
                <td>
                    <?php if($r->images && count($r->images)>0): ?>
                        <?php $__currentLoopData = $r->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <img src="<?php echo e(asset($img)); ?>" class="me-1 rounded obj-cover w-48 h-48" />
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </td>
                <td><?php echo e($r->created_at->format('Y-m-d')); ?></td>
                <td><?php echo e($r->approved ? __('Yes') : __('No')); ?></td>
                <td>
                    <a href="<?php echo e(route('admin.reviews.show',$r)); ?>" class="btn btn-sm btn-secondary"><?php echo e(__('View')); ?></a>
                    <?php if($r->approved): ?>
                        <form method="post" action="<?php echo e(route('admin.reviews.unapprove',$r)); ?>" class="d-inline"><?php echo csrf_field(); ?><button class="btn btn-sm btn-warning"><?php echo e(__('Unapprove')); ?></button></form>
                    <?php else: ?>
                        <form method="post" action="<?php echo e(route('admin.reviews.approve',$r)); ?>" class="d-inline-block"><?php echo csrf_field(); ?><button class="btn btn-sm btn-success"><?php echo e(__('Approve')); ?></button></form>
                    <?php endif; ?>
-                    <form method="post" action="<?php echo e(route('admin.reviews.destroy',$r)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('delete'); ?><button class="btn btn-sm btn-danger"><?php echo e(__('Delete')); ?></button></form>
+                    <form method="post" action="<?php echo e(route('admin.reviews.destroy',$r)); ?>" class="d-inline-block js-confirm-delete" data-confirm="<?php echo e(__('Delete?')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('delete'); ?><button class="btn btn-sm btn-danger"><?php echo e(__('Delete')); ?></button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer">
            <?php echo e($reviews->links()); ?>

        </div>
    </div>


</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/reviews/index.blade.php ENDPATH**/ ?>