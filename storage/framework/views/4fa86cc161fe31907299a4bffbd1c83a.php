
<?php $__env->startSection('title', __('Shipping Zones')); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', ['title'=>__('Shipping Zones'),'actions'=>'<a href="'.route('admin.shipping-zones.create').'" class="btn btn-primary">'.__('Create').'</a>'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card modern-card">
   <div class="card-header d-flex align-items-center gap-2">
      <h3 class="card-title mb-0"><?php echo e(__('Shipping Zones')); ?></h3>
   </div>
   <div class="card-body p-0">
      <div class="table-responsive">
         <table class="table table-sm mb-0">
            <thead><tr><th><?php echo e(__('ID')); ?></th><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Code')); ?></th><th><?php echo e(__('Rules')); ?></th><th><?php echo e(__('Active')); ?></th><th></th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $z): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <tr>
                  <td><?php echo e($z->id); ?></td>
                  <td><?php echo e($z->name); ?></td>
                  <td><?php echo e($z->code ?? '-'); ?></td>
                  <td><?php echo e($z->rules_count); ?></td>
                  <td>
                     <?php if($z->active): ?>
                        <span class="badge bg-success"><?php echo e(__('Yes')); ?></span>
                     <?php else: ?>
                        <span class="badge bg-secondary"><?php echo e(__('No')); ?></span>
                     <?php endif; ?>
                  </td>
                  <td class="text-nowrap">
                     <a href="<?php echo e(route('admin.shipping-zones.edit',$z)); ?>" class="btn btn-sm btn-secondary"><?php echo e(__('Edit')); ?></a>
                     <form method="POST" action="<?php echo e(route('admin.shipping-zones.destroy',$z)); ?>" class="d-inline admin-form js-confirm-delete" data-confirm="<?php echo e(__('Delete?')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-danger"><?php echo e(__('Delete')); ?></button></form>
                  </td>
               </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
<?php echo e($zones->links()); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('admin/js/confirm-delete.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/shipping_zones/index.blade.php ENDPATH**/ ?>