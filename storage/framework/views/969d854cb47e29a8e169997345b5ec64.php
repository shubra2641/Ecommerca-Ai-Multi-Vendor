<?php $__env->startSection('title', __('Edit Product')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div class="page-header-content">
        <h1 class="page-title mb-1"><?php echo e(__('Edit Product')); ?></h1>
        <p class="page-description mb-0 text-muted"><?php echo e(__('Update product information')); ?></p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span class="d-none d-sm-inline"><?php echo e(__('Back to Products')); ?></span>
        </a>
        <a href="<?php echo e(route('admin.products.show', $product)); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-eye"></i>
            <span class="d-none d-sm-inline"><?php echo e(__('View Product')); ?></span>
        </a>
        <button type="submit" form="product-form" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <span class="d-none d-sm-inline"><?php echo e(__('Update')); ?></span>
            <span class="d-sm-none"><?php echo e(__('Save')); ?></span>
        </button>
    </div>
</div>

<div class="card modern-card content-card">
    <div class="content-card-header card-header">
        <div>
            <h3 class="content-title card-title mb-1"><?php echo e(__('Product Information')); ?></h3>
            <p class="content-description text-muted mb-0"><?php echo e(__('Update the product details below')); ?></p>
        </div>
    </div>
    <div class="content-card-body card-body">
        <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
        <form id="product-form" method="POST" action="<?php echo e(route('admin.products.update', $product)); ?>"
            enctype="multipart/form-data" autocomplete="off">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('admin.products.products._form',['model'=>$product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/lang-tabs.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/media-picker.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/product-form.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/media-inputs.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/products/edit.blade.php ENDPATH**/ ?>