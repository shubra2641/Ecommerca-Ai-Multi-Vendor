
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', ['title'=>__('Coupons'),'actions'=>'<a href="'.route('admin.coupons.create').'" class="btn btn-primary">'.__('Create Coupon').'</a>'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card modern-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table mt-3">
    <thead>
        <tr>
            <th><?php echo e(__('Code')); ?></th>
            <th><?php echo e(__('Type')); ?></th>
            <th><?php echo e(__('Value')); ?></th>
            <th><?php echo e(__('Uses')); ?></th>
            <th><?php echo e(__('Expires')); ?></th>
            <th><?php echo e(__('Active')); ?></th>
            <th><?php echo e(__('Actions')); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($coupon->code); ?></td>
            <td><?php echo e(ucfirst($coupon->type)); ?></td>
            <td><?php echo e($coupon->value); ?></td>
            <td><?php echo e($coupon->uses); ?></td>
            <td><?php echo e($coupon->ends_at); ?></td>
            <td><?php echo e($coupon->active ? __('Active') : __('Inactive')); ?></td>
            <td>
                <a href="<?php echo e(route('admin.coupons.edit', $coupon)); ?>" class="btn btn-sm btn-secondary"><?php echo e(__('Edit')); ?></a>
                <form method="post" action="<?php echo e(route('admin.coupons.destroy', $coupon)); ?>" class="d-inline-block js-confirm-delete" data-confirm="<?php echo e(__('Delete?')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('delete'); ?>
                    <button class="btn btn-sm btn-danger"><?php echo e(__('Delete')); ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
            </table>
        </div>
        <?php echo e($coupons->links()); ?>

    </div>
</div>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('admin/js/confirm-delete.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/coupons/index.blade.php ENDPATH**/ ?>