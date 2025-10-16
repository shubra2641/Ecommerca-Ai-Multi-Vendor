<?php $__env->startSection('title', __('Add Language')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Add Language')); ?></h1>
        <p class="page-description"><?php echo e(__('Create a new language for the system')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.languages.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to Languages')); ?>

        </a>
    </div>
</div>

<div class="container-fluid">

    <div class="card modern-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-language text-primary"></i>
                <?php echo e(__('Language Information')); ?>

            </h3>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.languages.store')); ?>" method="POST" class="language-form">
                <?php echo csrf_field(); ?>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label"><?php echo e(__('Language Name')); ?> <span
                                class="required">*</span></label>
                        <input type="text" id="name" name="name"
                            class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>"
                            placeholder="<?php echo e(__('e.g., English')); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="code" class="form-label"><?php echo e(__('Language Code')); ?> <span
                                class="required">*</span></label>
                        <input type="text" class="form-control language-code-input <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="code" name="code" value="<?php echo e(old('code')); ?>" placeholder="<?php echo e(__('e.g., en')); ?>"
                            maxlength="2" required>
                        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text"><?php echo e(__('2-letter ISO language code')); ?></small>
                    </div>

                    <div class="form-group">
                        <label for="flag" class="form-label"><?php echo e(__('Flag Emoji')); ?></label>
                        <input type="text" id="flag" name="flag"
                            class="form-control <?php $__errorArgs = ['flag'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('flag')); ?>"
                            placeholder="<?php echo e(__('e.g., 🇺🇸')); ?>" maxlength="10">
                        <?php $__errorArgs = ['flag'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text"><?php echo e(__('Optional flag emoji for visual identification')); ?></small>
                    </div>
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1"
                            <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                        <label for="is_active" class="form-check-label">
                            <?php echo e(__('Active')); ?>

                        </label>
                        <small class="form-text"><?php echo e(__('Whether this language is available for use')); ?></small>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="is_default" name="is_default" class="form-check-input" value="1"
                            <?php echo e(old('is_default') ? 'checked' : ''); ?>>
                        <label for="is_default" class="form-check-label">
                            <?php echo e(__('Set as Default Language')); ?>

                        </label>
                        <small class="form-text"><?php echo e(__('This will replace the current default language')); ?></small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo e(__('Create Language')); ?>

                    </button>
                    <a href="<?php echo e(route('admin.languages.index')); ?>" class="btn btn-secondary">
                        <?php echo e(__('Cancel')); ?>

                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/languages.css')); ?>">
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/languages/create.blade.php ENDPATH**/ ?>