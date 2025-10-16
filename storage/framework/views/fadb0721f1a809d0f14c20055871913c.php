
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card modern-card">

                <div class="page-header">
        <h1 class="mb-0"><?php echo e(__('Brands')); ?></h1>
        <a href="<?php echo e(route('admin.brands.index')); ?>" class="btn btn-primary"><?php echo e(__('All Brand')); ?></a>
    </div>
    
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0"><?php echo e(__('Edit Brand')); ?></h5>
        </div>
        <div class="card-body">
            <form method="post" action="<?php echo e(route('admin.brands.update', $brand)); ?>" class="admin-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('put'); ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('Name')); ?></label>
                    <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $brand->name)); ?>" />
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('Slug')); ?></label>
                    <input type="text" name="slug" class="form-control" value="<?php echo e(old('slug', $brand->slug)); ?>" />
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="active" id="active" class="form-check-input" value="1" <?php echo e(old('active', $brand->active) ? 'checked' : ''); ?> />
                    <label class="form-check-label" for="active"><?php echo e(__('Active')); ?></label>
                </div>
                <div>
                    <button class="btn btn-primary"><?php echo e(__('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/brands/edit.blade.php ENDPATH**/ ?>