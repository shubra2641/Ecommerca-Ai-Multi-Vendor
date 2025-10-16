<?php $__env->startSection('title', __('Edit Category')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title mb-1"><?php echo e(__('Edit Category')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Update category information')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.product-categories.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo e(__('Back')); ?>

        </a>
        <button type="submit" form="category-form" class="btn btn-primary">
            <i class="fas fa-save me-1"></i><?php echo e(__('Update Category')); ?>

        </button>
    </div>
</div>

<div class="card card-body">
    <form id="category-form" method="POST" action="<?php echo e(route('admin.product-categories.update',$productCategory)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <?php echo $__env->make('admin.products.categories._form',['model'=>$productCategory], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/media-picker.js')); ?>" defer></script>
<script src="<?php echo e(asset('admin/js/media-picker-init.js')); ?>" defer></script>
<script src="<?php echo e(asset('admin/js/product-form.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/categories/edit.blade.php ENDPATH**/ ?>