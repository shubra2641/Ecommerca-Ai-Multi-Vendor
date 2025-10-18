<?php $__env->startSection('title', __('Homepage Sections')); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
  <h1 class="h4 mb-3"><?php echo e(__('Homepage Sections')); ?></h1>
  <form method="POST" action="<?php echo e(route('admin.homepage.sections.update')); ?>" class="card shadow-sm p-3">
    <?php echo csrf_field(); ?>
    <div class="table-responsive mb-3">
      <table class="table align-middle table-sm">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th><?php echo e(__('Key')); ?></th>
            <th><?php echo e(__('Enabled')); ?></th>
            <th><?php echo e(__('Order')); ?></th>
            <th><?php echo e(__('Limit')); ?></th>
            <th class="table-head-wide"><?php echo e(__('Titles')); ?></th>
            <th class="table-head-wide"><?php echo e(__('Subtitles')); ?></th>
            <th class="table-head-wide"><?php echo e(__('CTA Labels')); ?></th>
            <th class="table-head-medium"><?php echo e(__('CTA Settings')); ?></th>
          </tr>
        </thead>
        <tbody>
  <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($loop->iteration); ?><input type="hidden" name="sections[<?php echo e($loop->index); ?>][id]" value="<?php echo e($section->id); ?>"></td>
            <td><code><?php echo e($section->key); ?></code></td>
            <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="sections[<?php echo e($loop->index); ?>][enabled]" value="1" <?php if($section->enabled): echo 'checked'; endif; ?>></div></td>
            <td><input type="number" class="form-control form-control-sm input-w-90" name="sections[<?php echo e($loop->index); ?>][sort_order]" value="<?php echo e($section->sort_order); ?>"></td>
            <td><input type="number" class="form-control form-control-sm input-w-90" name="sections[<?php echo e($loop->index); ?>][item_limit]" value="<?php echo e($section->item_limit); ?>" min="1" max="100"></td>
            <td>
              <?php $__currentLoopData = $activeLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mb-1 d-flex align-items-center gap-1">
                  <span class="badge bg-secondary"><?php echo e(strtoupper($lang->code)); ?></span>
                  <input type="text" class="form-control form-control-sm" name="sections[<?php echo e($loop->parent->index); ?>][title][<?php echo e($lang->code); ?>]" value="<?php echo e($section->title_i18n[$lang->code] ?? ''); ?>" placeholder="<?php echo e($lang->code); ?>">
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td>
              <?php $__currentLoopData = $activeLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mb-1 d-flex align-items-center gap-1">
                  <span class="badge bg-secondary"><?php echo e(strtoupper($lang->code)); ?></span>
                  <input type="text" class="form-control form-control-sm" name="sections[<?php echo e($loop->parent->index); ?>][subtitle][<?php echo e($lang->code); ?>]" value="<?php echo e($section->subtitle_i18n[$lang->code] ?? ''); ?>" placeholder="<?php echo e($lang->code); ?>">
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td>
              <?php $__currentLoopData = $activeLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mb-1 d-flex align-items-center gap-1">
                  <span class="badge bg-info"><?php echo e(strtoupper($lang->code)); ?></span>
                  <input type="text" class="form-control form-control-sm" name="sections[<?php echo e($loop->parent->index); ?>][cta_label][<?php echo e($lang->code); ?>]" value="<?php echo e($section->cta_label_i18n[$lang->code] ?? ''); ?>" placeholder="<?php echo e(__('CTA')); ?> <?php echo e($lang->code); ?>">
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td>
              <div class="form-check form-switch mb-1">
                <input class="form-check-input" type="checkbox" name="sections[<?php echo e($loop->index); ?>][cta_enabled]" value="1" <?php if($section->cta_enabled): echo 'checked'; endif; ?>>
                <label class="form-check-label small"><?php echo e(__('Enabled')); ?></label>
              </div>
              <input type="text" class="form-control form-control-sm" name="sections[<?php echo e($loop->index); ?>][cta_url]" value="<?php echo e($section->cta_url); ?>" placeholder="/products">
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <div>
      <button class="btn btn-primary"><?php echo e(__('Save Changes')); ?></button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/homepage/sections/index.blade.php ENDPATH**/ ?>