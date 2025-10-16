<?php $__env->startSection('title', __('Edit Tag')); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-8">
        <h1 class="page-title mb-1"><?php echo e(__('Edit Tag')); ?></h1>
        <p class="text-muted mb-0"><?php echo e($productTag->name); ?></p>
    </div>
    <div class="col-12 col-md-4 mt-3 mt-md-0">
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
            <a href="<?php echo e(route('admin.product-tags.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                <span class="d-none d-sm-inline"><?php echo e(__('Back')); ?></span>
                <span class="d-sm-none"><?php echo e(__('Back')); ?></span>
            </a>
            <button type="submit" form="tag-form" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                <span class="d-none d-sm-inline"><?php echo e(__('Update Tag')); ?></span>
                <span class="d-sm-none"><?php echo e(__('Update')); ?></span>
            </button>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="card modern-card">
    <div class="card-header">
        <div>
            <h5 class="card-title mb-0"><?php echo e(__('Tag Information')); ?></h5>
            <small class="text-muted"><?php echo e(__('Update the tag details below')); ?></small>
        </div>
    </div>
    <div class="card-body">
        <form id="tag-form" method="POST" action="<?php echo e(route('admin.product-tags.update', $productTag)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('admin.products.tags._form', ['model' => $productTag], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/tags/edit.blade.php ENDPATH**/ ?>