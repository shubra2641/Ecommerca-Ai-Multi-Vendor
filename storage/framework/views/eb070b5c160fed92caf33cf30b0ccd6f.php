<?php $__env->startSection('title', __('Countries')); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
  <h3><?php echo e(__('Countries')); ?></h3>
  <a href="<?php echo e(route('admin.countries.create')); ?>" class="btn btn-sm btn-primary"><?php echo e(__('Add Country')); ?></a>
</div>
<table class="table table-striped">
  <thead><tr><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('ISO')); ?></th><th><?php echo e(__('Active')); ?></th><th></th></tr></thead>
  <tbody>
    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($c->name); ?></td>
      <td><?php echo e($c->iso_code); ?></td>
      <td><?php echo e($c->active?__('Yes'):__('No')); ?></td>
      <td>
        <a href="<?php echo e(route('admin.countries.edit',$c)); ?>" class="btn btn-sm btn-outline-secondary"><?php echo e(__('Edit')); ?></a>
        <form action="<?php echo e(route('admin.countries.destroy',$c)); ?>" method="POST" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><?php echo e(__('Delete')); ?></button></form>
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>
<?php echo e($countries->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/locations/countries/index.blade.php ENDPATH**/ ?>