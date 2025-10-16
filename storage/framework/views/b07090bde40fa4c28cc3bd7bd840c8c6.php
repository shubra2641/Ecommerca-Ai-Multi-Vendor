<?php $__env->startSection('title', $link->exists ? __('Edit Social Link') : __('Add Social Link')); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', ['title'=>$link->exists ? __('Edit Social Link') : __('Add Social Link'),'actions'=>'<a href="'.route('admin.social.index').'" class="btn btn-secondary">'.__('Back').'</a>'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?php echo e($link->exists ? route('admin.social.update', $link) : route('admin.social.store')); ?>" novalidate>
    <?php echo csrf_field(); ?>
    <?php if($link->exists): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label"><?php echo app('translator')->get('Platform'); ?> <span class="text-danger">*</span></label>
            <input type="text" name="platform" class="form-control" value="<?php echo e(old('platform', $link->platform)); ?>" required maxlength="50">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo app('translator')->get('Label'); ?></label>
            <input type="text" name="label" class="form-control" value="<?php echo e(old('label', $link->label)); ?>" maxlength="100" placeholder="<?php echo app('translator')->get('Optional display text'); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo app('translator')->get('URL'); ?> <span class="text-danger">*</span></label>
            <input type="url" name="url" class="form-control" value="<?php echo e(old('url', $link->url)); ?>" required maxlength="255" placeholder="https://">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo app('translator')->get('Icon'); ?></label>
            <select name="icon" class="form-select">
                <?php $__currentLoopData = ($socialIcons ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class); ?>" <?php if(($socialCurrentIcon ?? '') === $class): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="form-text"><?php echo app('translator')->get('Select an icon to display'); ?></div>
        </div>
        <div class="col-md-2">
            <label class="form-label"><?php echo app('translator')->get('Order'); ?></label>
            <input type="number" name="order" class="form-control" value="<?php echo e(old('order', $link->order)); ?>" min="0" max="9999">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo e(old('is_active', $link->is_active) ? 'checked' : ''); ?>>
                <label class="form-check-label" for="is_active"><?php echo app('translator')->get('Active'); ?></label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary"><?php echo e($link->exists ? __('Update') : __('Create')); ?></button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/social/form.blade.php ENDPATH**/ ?>