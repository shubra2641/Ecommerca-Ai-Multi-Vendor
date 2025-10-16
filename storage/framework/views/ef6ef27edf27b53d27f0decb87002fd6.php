<?php $__env->startSection('title', __('Add Attribute')); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div class="page-header-content">
        <h1 class="page-title mb-1"><?php echo e(__('Add Attribute')); ?></h1>
        <p class="page-description mb-0 text-muted"><?php echo e(__('Define a new attribute for product variations')); ?></p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.product-attributes.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <span class="d-none d-sm-inline ms-1"><?php echo e(__('Back')); ?></span>
        </a>
        <button type="submit" form="attribute-form" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <span class="d-none d-sm-inline ms-1"><?php echo e(__('Save Attribute')); ?></span>
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="card modern-card">
    <div class="card-header">
        <div>
            <h5 class="card-title mb-0"><?php echo e(__('Attribute Information')); ?></h5>
            <small class="text-muted"><?php echo e(__('Fill in the attribute details below')); ?></small>
        </div>
    </div>
    <div class="card-body">
        <form id="attribute-form" method="POST" action="<?php echo e(route('admin.product-attributes.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.products.attributes._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/attributes/create.blade.php ENDPATH**/ ?>