
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <h1 class="mb-0"><?php echo e(__('Brands')); ?></h1>
        <a href="<?php echo e(route('admin.brands.create')); ?>" class="btn btn-primary"><?php echo e(__('Create Brand')); ?></a>
    </div>
    <div class="card modern-card">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th><?php echo e(__('ID')); ?></th><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Active')); ?></th><th><?php echo e(__('Actions')); ?></th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($brand->id); ?></td>
                        <td><?php echo e($brand->name); ?></td>
                        <td><?php echo e($brand->active ? __('Active') : __('Inactive')); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.brands.edit', $brand)); ?>" class="btn btn-sm btn-secondary"><?php echo e(__('Edit')); ?></a>
                            <form method="post" action="<?php echo e(route('admin.brands.destroy', $brand)); ?>" class="d-inline-block js-confirm-delete" data-confirm="<?php echo e(__('Delete?')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('delete'); ?>
                                <button class="btn btn-sm btn-danger"><?php echo e(__('Delete')); ?></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer">
            <?php echo e($brands->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/brands/index.blade.php ENDPATH**/ ?>