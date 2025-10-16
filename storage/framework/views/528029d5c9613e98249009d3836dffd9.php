<?php $__env->startSection('title', __('Cities')); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
  <h3><?php echo e(__('Cities')); ?></h3>
  <a href="<?php echo e(route('admin.cities.create')); ?>" class="btn btn-sm btn-primary"><?php echo e(__('Add City')); ?></a>
</div>
<form method="GET" class="mb-3 js-auto-submit">
  <select name="governorate" class="form-select form-select-sm">
    <option value="">-- <?php echo e(__('All Governorates')); ?> --</option>
    <?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($g->id); ?>" <?php echo e($govId==$g->id? 'selected':''); ?>><?php echo e($g->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
</form>
<table class="table table-striped">
  <thead><tr><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Governorate')); ?></th><th><?php echo e(__('Active')); ?></th><th></th></tr></thead>
  <tbody>
    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($c->name); ?></td>
      <td><?php echo e($c->governorate? $c->governorate->name : ''); ?></td>
      <td><?php echo e($c->active?__('Yes'):__('No')); ?></td>
      <td>
        <a href="<?php echo e(route('admin.cities.edit',$c)); ?>" class="btn btn-sm btn-outline-secondary"><?php echo e(__('Edit')); ?></a>
        <form action="<?php echo e(route('admin.cities.destroy',$c)); ?>" method="POST" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><?php echo e(__('Delete')); ?></button></form>
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>
<?php echo e($cities->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/locations/cities/index.blade.php ENDPATH**/ ?>