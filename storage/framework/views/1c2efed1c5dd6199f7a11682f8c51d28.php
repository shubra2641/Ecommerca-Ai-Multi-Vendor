
<?php $__env->startSection('content'); ?>
<h5><?php echo e(__('Create Coupon')); ?></h5>
<form method="post" action="<?php echo e(route('admin.coupons.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label"><?php echo e(__('Code')); ?></label>
        <input type="text" name="code" class="form-control" />
    </div>
    <div class="mb-3">
        <label class="form-label"><?php echo e(__('Type')); ?></label>
        <select name="type" class="form-select">
            <option value="fixed"><?php echo e(__('Fixed')); ?></option>
            <option value="percent"><?php echo e(__('Percent')); ?></option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label"><?php echo e(__('Value')); ?></label>
        <input type="number" step="0.01" name="value" class="form-control" />
    </div>
    <div class="mb-3">
        <label class="form-label"><?php echo e(__('Uses Total (leave empty for unlimited)')); ?></label>
        <input type="number" name="uses_total" class="form-control" />
    </div>
    <div class="mb-3">
        <label class="form-label"><?php echo e(__('Min Order Total (optional)')); ?></label>
        <input type="number" step="0.01" name="min_total" class="form-control" />
    </div>
    <div class="row g-2">
        <div class="col">
            <label class="form-label"><?php echo e(__('Starts At')); ?></label>
            <input type="datetime-local" name="starts_at" class="form-control" />
        </div>
        <div class="col">
            <label class="form-label"><?php echo e(__('Ends At')); ?></label>
            <input type="datetime-local" name="ends_at" class="form-control" />
        </div>
    </div>
    <div class="form-check my-3">
        <input type="checkbox" name="active" id="active" class="form-check-input" value="1" />
        <label class="form-check-label" for="active"><?php echo e(__('Active')); ?></label>
    </div>
    <button class="btn btn-primary"><?php echo e(__('Create')); ?></button>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/coupons/create.blade.php ENDPATH**/ ?>