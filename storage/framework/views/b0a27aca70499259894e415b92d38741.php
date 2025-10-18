<?php $__env->startSection('title', __('Maintenance Settings')); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
  <h1 class="h4 mb-3"><?php echo e(__('Maintenance Settings')); ?></h1>
  <form method="POST" action="<?php echo e(route('admin.maintenance-settings.update')); ?>" class="card p-3 shadow-sm">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
  <div class="row g-3">
      <div class="col-md-4">
        <label class="form-check">
          <input type="hidden" name="maintenance_enabled" value="0">
          <input type="checkbox" class="form-check-input" name="maintenance_enabled" value="1" <?php if(old('maintenance_enabled', $setting->maintenance_enabled ?? false)): echo 'checked'; endif; ?>>
          <span class="form-check-label fw-semibold"><?php echo e(__('Enable Maintenance Mode')); ?></span>
        </label>
        <small class="text-muted d-block mt-1"><?php echo e(__('Front visitors see maintenance page; admins still access panel.')); ?></small>
      </div>
      <div class="col-md-4">
        <label class="form-label"><?php echo e(__('Reopen At (optional)')); ?></label>
        <input type="datetime-local" name="maintenance_reopen_at" value="<?php echo e(old('maintenance_reopen_at', isset($setting->maintenance_reopen_at)? $setting->maintenance_reopen_at->format('Y-m-d\TH:i'): '')); ?>" class="form-control">
        <small class="text-muted"><?php echo e(__('Leave blank for indefinite maintenance.')); ?></small>
      </div>
      <div class="col-md-4">
        <label class="form-label"><?php echo e(__('Maintenance Message (per language)')); ?></label>
    <?php $__currentLoopData = ($activeLanguages ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <input type="text" name="maintenance_message[<?php echo e($lang->code); ?>]" class="form-control mb-2" placeholder="<?php echo e($lang->name ?? strtoupper($lang->code)); ?>" value="<?php echo e(old('maintenance_message.'.$lang->code, $messages[$lang->code] ?? '')); ?>" maxlength="255">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <small class="text-muted"><?php echo e(__('Shown on the maintenance landing page.')); ?></small>
      </div>
    </div>
    <div class="mt-4">
      <button class="btn btn-primary"><?php echo e(__('Save Maintenance Settings')); ?></button>
    <a href="<?php echo e(route('admin.maintenance-settings.preview')); ?>" target="_blank" class="btn btn-outline-info ms-2"><?php echo e(__('Preview Page')); ?></a>
      <a href="<?php echo e(route('admin.footer-settings.edit')); ?>" class="btn btn-outline-secondary ms-2"><?php echo e(__('Back to Footer')); ?></a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/maintenance/settings.blade.php ENDPATH**/ ?>