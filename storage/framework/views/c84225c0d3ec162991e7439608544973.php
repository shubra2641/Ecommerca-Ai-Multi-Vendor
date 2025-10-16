<?php $__env->startSection('title', __('Tags')); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
<li class="breadcrumb-item active"><?php echo e(__('Tags')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title mb-1"><?php echo e(__('Tags')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Manage blog tags and create new ones')); ?></p>
    </div>
</div>

<div class="row">
  <div class="col-md-5">
    <div class="card card-body">
      <h6 class="fw-bold mb-3"><?php echo e(__('Create Tag')); ?></h6>
      <form method="POST" action="<?php echo e(route('admin.blog.tags.store')); ?>"><?php echo csrf_field(); ?>
      <div class="mb-2">
        <label class="form-label small"><?php echo e(__('Name')); ?></label>
        <input name="name" class="form-control form-control-sm" required>
      </div>
      <div class="mb-2">
        <label class="form-label small"><?php echo e(__('Slug')); ?></label>
      <input name="slug" class="form-control form-control-sm" placeholder="auto" readonly>
      <div class="form-text small"><?php echo e(__('Slug will be generated from the tag name.')); ?></div>
      </div>
        <button class="btn btn-primary"><?php echo e(__('Save')); ?></button>
      </form>
    </div>
  </div>
  <div class="col-md-7">
  <div class="card modern-card">
      <div class="card-header">
        <h5 class="mb-0"><?php echo e(__('Tags List')); ?></h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead><tr><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Updated')); ?></th><th width="300"><?php echo e(__('Actions')); ?></th></tr></thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><strong><?php echo e($tag->name); ?></strong><br><small class="text-muted">/<?php echo e($tag->slug); ?></small></td>
              <td><?php echo e($tag->updated_at->diffForHumans()); ?></td>
              <td class="text-end">
                <form method="POST" action="<?php echo e(route('admin.blog.tags.update',$tag)); ?>" class="d-inline-flex align-items-center gap-1"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                  <input name="name" value="<?php echo e($tag->name); ?>" class="form-control form-control-sm w-120">
                  <input name="slug" value="<?php echo e($tag->slug); ?>" class="form-control form-control-sm w-120" readonly>
                  <button class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                </form>
                <form method="POST" action="<?php echo e(route('admin.blog.tags.destroy',$tag)); ?>" class="js-confirm" data-confirm="<?php echo e(__('Are you sure you want to delete this tag?')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3" class="text-center text-muted py-3"><?php echo e(__('No tags found')); ?></td></tr>
            <?php endif; ?>
          </tbody>
          </table>
        </div>
      </div>
      <?php if($tags->hasPages()): ?>
      <div class="card-footer">
        <?php echo e($tags->links()); ?>

      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/blog/tags/index.blade.php ENDPATH**/ ?>