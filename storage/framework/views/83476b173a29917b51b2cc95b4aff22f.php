<?php $__env->startSection('title', __('Governorates')); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
  <h3><?php echo e(__('Governorates')); ?></h3>
  <a href="<?php echo e(route('admin.governorates.create')); ?>" class="btn btn-sm btn-primary"><?php echo e(__('Add Governorate')); ?></a>
</div>
<form method="GET" class="mb-3 js-auto-submit">
  <select name="country" class="form-select form-select-sm">
    <option value="">-- <?php echo e(__('All Countries')); ?> --</option>
    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($c->id); ?>" <?php echo e($countryId==$c->id? 'selected':''); ?>><?php echo e($c->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
</form>
<table class="table table-striped">
  <thead><tr><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Country')); ?></th><th><?php echo e(__('Active')); ?></th><th></th></tr></thead>
  <tbody>
    <?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($g->name); ?></td>
      <td><?php echo e($g->country? $g->country->name : ''); ?></td>
      <td><?php echo e($g->active?__('Yes'):__('No')); ?></td>
      <td>
        <a href="<?php echo e(route('admin.governorates.edit',$g)); ?>" class="btn btn-sm btn-outline-secondary"><?php echo e(__('Edit')); ?></a>
        <form action="<?php echo e(route('admin.governorates.destroy',$g)); ?>" method="POST" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><?php echo e(__('Delete')); ?></button></form>
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>
<?php echo e($governorates->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/locations/governorates/index.blade.php ENDPATH**/ ?>