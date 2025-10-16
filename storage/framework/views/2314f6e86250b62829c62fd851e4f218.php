<?php $__env->startSection('title', __('Edit Language') . ' - ' . $language->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Edit Language')); ?></h1>
        <p class="page-description"><?php echo e(__('Update language information and settings')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.languages.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to Languages')); ?>

        </a>
        <a href="<?php echo e(route('admin.languages.translations', $language)); ?>" class="btn btn-primary">
            <i class="fas fa-language"></i>
            <?php echo e(__('Manage Translations')); ?>

        </a>
    </div>
</div>

<div class="container-fluid">

    <?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-language text-primary"></i>
                        <?php echo e(__('Language Information')); ?>

                    </h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.languages.update', $language)); ?>" method="POST" class="language-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

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
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('name', $language->name)); ?>" placeholder="<?php echo e(__('e.g., English')); ?>"
                                    required>
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
                                <label for="native_name" class="form-label"><?php echo e(__('Native Name')); ?></label>
                                <input type="text" id="native_name" name="native_name"
                                    class="form-control <?php $__errorArgs = ['native_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('native_name', $language->native_name)); ?>"
                                    placeholder="<?php echo e(__('e.g., English')); ?>">
                                <?php $__errorArgs = ['native_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text"><?php echo e(__('Language name in its native script')); ?></small>
                            </div>

                            <div class="form-group">
                                <label for="code" class="form-label"><?php echo e(__('Language Code')); ?> <span
                                        class="required">*</span></label>
                                <input type="text"
                                    class="form-control language-code-input <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="code" name="code" value="<?php echo e(old('code', $language->code)); ?>"
                                    placeholder="<?php echo e(__('e.g., en')); ?>" maxlength="2" required>
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
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('flag', $language->flag)); ?>" placeholder="<?php echo e(__('e.g., 🇺🇸')); ?>"
                                    maxlength="10">
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
                                <small
                                    class="form-text"><?php echo e(__('Optional flag emoji for visual identification')); ?></small>
                            </div>

                            <div class="form-group">
                                <label for="direction" class="form-label"><?php echo e(__('Text Direction')); ?></label>
                                <select id="direction" name="direction"
                                    class="form-control <?php $__errorArgs = ['direction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="ltr"
                                        <?php echo e(old('direction', $language->direction) == 'ltr' ? 'selected' : ''); ?>>
                                        <?php echo e(__('Left to Right (LTR)')); ?></option>
                                    <option value="rtl"
                                        <?php echo e(old('direction', $language->direction) == 'rtl' ? 'selected' : ''); ?>>
                                        <?php echo e(__('Right to Left (RTL)')); ?></option>
                                </select>
                                <?php $__errorArgs = ['direction'];
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
                        </div>

                        <div class="form-options">
                            <div class="form-check">
                                <input type="checkbox" id="is_active" name="is_active" class="form-check-input"
                                    value="1" <?php echo e(old('is_active', $language->is_active) ? 'checked' : ''); ?>>
                                <label for="is_active" class="form-check-label">
                                    <?php echo e(__('Active')); ?>

                                </label>
                                <small class="form-text"><?php echo e(__('Whether this language is available for use')); ?></small>
                            </div>

                            <?php if(!$language->is_default): ?>
                            <div class="form-check">
                                <input type="checkbox" id="is_default" name="is_default" class="form-check-input"
                                    value="1" <?php echo e(old('is_default', $language->is_default) ? 'checked' : ''); ?>>
                                <label for="is_default" class="form-check-label">
                                    <?php echo e(__('Set as Default Language')); ?>

                                </label>
                                <small
                                    class="form-text"><?php echo e(__('This will replace the current default language')); ?></small>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <?php echo e(__('This is the default language and cannot be changed here.')); ?>

                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo e(__('Update Language')); ?>

                            </button>
                            <a href="<?php echo e(route('admin.languages.index')); ?>" class="btn btn-secondary">
                                <?php echo e(__('Cancel')); ?>

                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Language Statistics -->
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar text-primary"></i>
                        <?php echo e(__('Language Statistics')); ?>

                    </h3>
                </div>
                <div class="card-body">
                    <div class="card modern-card stats-card mb-3">
                        <div class="stats-card-body">
                            <div class="stats-card-content">
                                <div class="stats-label"><?php echo e(__('Total Translations')); ?></div>
                                <div class="stats-number"><?php echo e($language->translations_count ?? 0); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="card modern-card stats-card mb-3">
                        <div class="stats-card-body">
                            <div class="stats-card-content">
                                <div class="stats-label"><?php echo e(__('Created')); ?></div>
                                <div class="stats-number"><?php echo e($language->created_at->format('M d, Y')); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="card modern-card stats-card">
                        <div class="stats-card-body">
                            <div class="stats-card-content">
                                <div class="stats-label"><?php echo e(__('Last Updated')); ?></div>
                                <div class="stats-number"><?php echo e($language->updated_at->format('M d, Y')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt text-primary"></i>
                        <?php echo e(__('Quick Actions')); ?>

                    </h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="<?php echo e(route('admin.languages.translations', $language)); ?>"
                            class="btn btn-outline-primary btn-block">
                            <i class="fas fa-language"></i> <?php echo e(__('Manage Translations')); ?>

                        </a>

                        <?php if($language->is_active): ?>
                        <form action="<?php echo e(route('admin.languages.deactivate', $language)); ?>" method="POST" class="mt-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-pause"></i> <?php echo e(__('Deactivate')); ?>

                            </button>
                        </form>
                        <?php else: ?>
                        <form action="<?php echo e(route('admin.languages.activate', $language)); ?>" method="POST" class="mt-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="btn btn-outline-success btn-block">
                                <i class="fas fa-play"></i> <?php echo e(__('Activate')); ?>

                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if(!$language->is_default): ?>
                        <form action="<?php echo e(route('admin.languages.destroy', $language)); ?>" method="POST"
                            class="mt-2 delete-form">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-block"
                                data-confirm="<?php echo e(__('Are you sure you want to delete this language? This action cannot be undone.')); ?>">
                                <i class="fas fa-trash"></i> <?php echo e(__('Delete Language')); ?>

                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/languages.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/languages.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/languages/edit.blade.php ENDPATH**/ ?>