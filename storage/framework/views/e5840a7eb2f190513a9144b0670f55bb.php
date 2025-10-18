<?php $__env->startSection('title', __('Add City')); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', ['title'=>__('Add City')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card modern-card">
	<div class="card-header d-flex align-items-center gap-2">
		<h5 class="m-0"><?php echo e(__('Add City')); ?></h5>
		<div class="ms-auto">
			<a href="<?php echo e(route('admin.cities.index')); ?>" class="btn btn-outline-secondary btn-sm"><?php echo e(__('Back')); ?></a>
		</div>
	</div>

	<form class="admin-form" method="POST" action="<?php echo e(route('admin.cities.store')); ?>"><?php echo csrf_field(); ?>
		<div class="card-body">
			<div class="mb-3">
				<label class="form-label"><?php echo e(__('Governorate')); ?></label>
				<select name="governorate_id" class="form-select" required aria-label="<?php echo e(__('Governorate')); ?>">
					<?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<option value="<?php echo e($g->id); ?>"><?php echo e($g->name); ?></option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</select>
			</div>

			<div class="mb-3">
				<label class="form-label"><?php echo e(__('Name')); ?></label>
				<input name="name" class="form-control" required aria-label="<?php echo e(__('Name')); ?>">
			</div>

			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" name="active" id="active" checked>
				<label class="form-check-label" for="active"><?php echo e(__('Active')); ?></label>
			</div>
		</div>

		<div class="card-footer text-end">
			<a href="<?php echo e(route('admin.cities.index')); ?>" class="btn btn-secondary me-2"><?php echo e(__('Cancel')); ?></a>
			<button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
		</div>
	</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/locations/cities/create.blade.php ENDPATH**/ ?>