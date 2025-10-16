<!-- Form Toolbar -->
<div class="form-toolbar sticky-top bg-body pb-3 mb-3 border-bottom z-6">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="fw-semibold text-muted small"><?php echo e(__('Tag Form')); ?></div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-collapse-all>
                <span class="d-none d-sm-inline"><?php echo e(__('Collapse All')); ?></span>
                <span class="d-sm-none"><?php echo e(__('Collapse')); ?></span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-expand-all>
                <span class="d-none d-sm-inline"><?php echo e(__('Expand All')); ?></span>
                <span class="d-sm-none"><?php echo e(__('Expand')); ?></span>
            </button>
        </div>
    </div>
</div>

<!-- Basic Information Section -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-tag me-1 text-primary"></i><?php echo e(__('Basic Information')); ?>

        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label required"><?php echo e(__('Name')); ?></label>
                <input type="text" name="name" id="tag-name" 
                       value="<?php echo e(old('name', $model->name ?? '')); ?>" 
                       class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       placeholder="<?php echo e(__('Enter tag name')); ?>" required>
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
            <div class="col-12 col-md-6">
                <label class="form-label required"><?php echo e(__('Slug')); ?></label>
                <input type="text" name="slug" id="tag-slug" readonly
                       value="<?php echo e(old('slug', $model->slug ?? '')); ?>" 
                       class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       placeholder="<?php echo e(__('Auto-generated from name')); ?>" required>
                <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="form-text"><?php echo e(__('URL-friendly version of the name. Leave empty to auto-generate.')); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Tag Configuration Section -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-cogs me-1 text-info"></i><?php echo e(__('Tag Configuration')); ?>

        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label"><?php echo e(__('Color')); ?></label>
                <input type="color" name="color" 
                       value="<?php echo e(old('color', $model->color ?? '#007bff')); ?>" 
                       class="form-control form-control-color <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="form-text"><?php echo e(__('Choose a color to represent this tag')); ?></div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label"><?php echo e(__('Display Order')); ?></label>
                <input type="number" name="sort_order" 
                       value="<?php echo e(old('sort_order', $model->sort_order ?? 0)); ?>" 
                       class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       min="0" placeholder="0">
                <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="form-text"><?php echo e(__('Lower numbers appear first')); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Settings Section -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-sliders-h me-1 text-warning"></i><?php echo e(__('Advanced Settings')); ?>

        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label"><?php echo e(__('Description')); ?></label>
                <textarea name="description" rows="3" 
                          class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          placeholder="<?php echo e(__('Optional description for this tag')); ?>"><?php echo e(old('description', $model->description ?? '')); ?></textarea>
                <?php $__errorArgs = ['description'];
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
            <div class="col-12 col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" 
                           class="form-check-input <?php $__errorArgs = ['is_featured'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           id="is_featured" <?php echo e(old('is_featured', $model->is_featured ?? false) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_featured">
                        <?php echo e(__('Featured Tag')); ?>

                    </label>
                    <?php $__errorArgs = ['is_featured'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="form-text"><?php echo e(__('Featured tags appear prominently in listings')); ?></div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" 
                           class="form-check-input <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           id="is_active" <?php echo e(old('is_active', $model->is_active ?? true) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_active">
                        <?php echo e(__('Active')); ?>

                    </label>
                    <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="form-text"><?php echo e(__('Only active tags can be assigned to products')); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/slugify.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/tags/_form.blade.php ENDPATH**/ ?>