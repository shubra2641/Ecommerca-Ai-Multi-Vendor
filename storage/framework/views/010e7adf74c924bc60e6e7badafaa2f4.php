<?php $__env->startSection('title', __('Categories')); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
<li class="breadcrumb-item active"><?php echo e(__('Categories')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card modern-card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('Categories')); ?></h5>
    <a href="<?php echo e(route('admin.blog.categories.create')); ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?></a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead><tr><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Parent')); ?></th><th><?php echo e(__('Updated')); ?></th><th width="120"><?php echo e(__('Actions')); ?></th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><strong><?php echo e($cat->name); ?></strong><br><small class="text-muted">/<?php echo e($cat->slug); ?></small></td>
          <td><?php echo e($cat->parent?->name ?? '-'); ?></td>
          <td><?php echo e($cat->updated_at->diffForHumans()); ?></td>
          <td class="text-end">
            <div class="btn-group btn-group-sm">
              <a href="<?php echo e(route('admin.blog.categories.edit',$cat)); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-edit"></i></a>
              <form method="POST" action="<?php echo e(route('admin.blog.categories.destroy',$cat)); ?>" class="js-confirm" data-confirm="<?php echo e(__('Are you sure you want to delete this category?')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button></form>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="4" class="text-center text-muted py-3"><?php echo e(__('No categories found')); ?></td></tr>
        <?php endif; ?>
      </tbody>
      </table>
    </div>
  </div>
  <?php if($categories->hasPages()): ?>
  <div class="card-footer">
    <?php echo e($categories->links()); ?>

  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/blog/categories/index.blade.php ENDPATH**/ ?>