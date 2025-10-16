<?php $__env->startSection('page_title', __('Create Blog Post')); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?php echo e(__('Create Blog Post')); ?></h1>
    <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="btn btn-sm btn-outline-secondary"><?php echo e(__('Back')); ?></a>
</div>
<form method="POST" action="<?php echo e(route('admin.blog.posts.store')); ?>" id="blogPostForm" enctype="multipart/form-data" class="needs-validation" novalidate>
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('admin.blog.posts._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary"><?php echo e(__('Save Post')); ?></button>
        <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="btn btn-outline-secondary"><?php echo e(__('Cancel')); ?></a>
    </div>
</form>
<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('admin/js/product-form.js')); ?>?v=<?php echo e(asset_modified_time('admin/js/product-form.js')); ?>" defer></script>
    <script src="<?php echo e(asset('admin/js/blog-post-form.js')); ?>?v=<?php echo e(asset_modified_time('admin/js/blog-post-form.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/blog/posts/create.blade.php ENDPATH**/ ?>