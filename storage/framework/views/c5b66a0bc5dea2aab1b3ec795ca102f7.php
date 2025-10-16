

<?php $__env->startSection('title', __('Manage Translations') . ' - ' . $language->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Manage Translations')); ?></h1>
        <p class="page-description"><?php echo e(__('Language')); ?>: <strong><?php echo e($language->name); ?></strong>
            (<?php echo e(strtoupper($language->code)); ?>)
            <?php if($language->flag): ?> <?php echo e($language->flag); ?> <?php endif; ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.languages.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to Languages')); ?>

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
        <!-- Add New Translation -->
        <div class="col-md-4">
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle text-primary"></i>
                        <?php echo e(__('Add New Translation')); ?>

                    </h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.languages.translations.add', $language)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="key" class="form-label"><?php echo e(__('Translation Key')); ?> <span
                                    class="required">*</span></label>
                            <input type="text" id="key" name="key"
                                class="form-control <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('key')); ?>"
                                placeholder="<?php echo e(__('e.g., Welcome Message')); ?>" required>
                            <?php $__errorArgs = ['key'];
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
                            <label for="value" class="form-label"><?php echo e(__('Translation Value')); ?> <span
                                    class="required">*</span></label>
                            <textarea id="value" name="value" class="form-control <?php $__errorArgs = ['value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="<?php echo e(__('Enter the translated text...')); ?>" rows="3"
                                required><?php echo e(old('value')); ?></textarea>
                            <?php $__errorArgs = ['value'];
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

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <?php echo e(__('Add Translation')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Translations -->
        <div class="col-md-8">
            <div class="card modern-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-list text-primary"></i>
                            <?php echo e(__('Existing Translations')); ?> (<?php echo e(count($translations)); ?>)
                        </h3>
                        <?php if(count($translations) > 0): ?>
                        <div class="header-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="expandAll">
                                <i class="fas fa-expand-alt"></i> <?php echo e(__('Expand All')); ?>

                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAll">
                                <i class="fas fa-compress-alt"></i> <?php echo e(__('Collapse All')); ?>

                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count($translations) > 0): ?>
                    <!-- Search Box -->
                    <div class="search-section mb-3">
                        <div class="input-group">
                            <input type="text" id="translationSearch" class="form-control"
                                placeholder="<?php echo e(__('Search translations...')); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo e(route('admin.languages.translations.update', $language)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="translations-list">
                            <?php $__currentLoopData = $translations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="translation-item">
                                <div class="translation-header">
                                    <label class="translation-key"><?php echo e($key); ?></label>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                        data-action="delete-translation" data-translation-key="<?php echo e($key); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="translation-value">
                                    <textarea name="translations[<?php echo e($key); ?>]" class="form-control translation-textarea"
                                        rows="2"
                                        placeholder="<?php echo e(__('Enter translation for: ') . $key); ?>"><?php echo e($value); ?></textarea>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <div class="form-actions d-flex justify-content-between align-items-center">
                            <div class="bulk-actions">
                                <button type="button" class="btn btn-outline-warning btn-sm" id="resetChanges">
                                    <i class="fas fa-undo"></i> <?php echo e(__('Reset Changes')); ?>

                                </button>
                            </div>
                            <div class="save-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> <?php echo e(__('Save All Translations')); ?>

                                </button>
                            </div>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-language fa-3x text-muted mb-3"></i>
                        <h4><?php echo e(__('No translations found')); ?></h4>
                        <p class="text-muted">
                            <?php echo e(__('Start by adding your first translation using the form on the left')); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Translation Form (hidden) -->
<form id="deleteTranslationForm" action="<?php echo e(route('admin.languages.translations.delete', $language)); ?>" method="POST"
    class="d-none">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <input type="hidden" name="key" id="deleteKey">
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script id="languages-translations-data" type="application/json"><?php echo json_encode([
    'i18n'=>[
        'confirmDelete'=>__('Are you sure you want to delete this translation ?'),
        'confirmReset'=>__('Are you sure you want to reset all changes ?'),
        'fillBoth'=>__('Please fill in both key and value fields.')
    ]
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?></script>
<script src="<?php echo e(asset('js/languages.js')); ?>" defer></script>
<script src="<?php echo e(asset('admin/js/languages-translations.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/languages/translations.blade.php ENDPATH**/ ?>