

<?php $__env->startSection('title', __('Edit Currency')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Edit Currency')); ?>: <?php echo e($currency->name); ?></h1>
        <p class="page-description"><?php echo e(__('Update currency information and exchange rate')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.currencies.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to Currencies')); ?>

        </a>
        <a href="<?php echo e(route('admin.currencies.show', $currency)); ?>" class="btn btn-outline-primary">
            <i class="fas fa-eye"></i>
            <?php echo e(__('View Details')); ?>

        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title"><?php echo e(__('Currency Information')); ?></h3>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.currencies.update', $currency)); ?>" method="POST" class="currency-form">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label"><?php echo e(__('Currency Name')); ?> <span
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
                                    value="<?php echo e(old('name', $currency->name)); ?>" placeholder="<?php echo e(__('e.g., US Dollar')); ?>"
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
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code" class="form-label"><?php echo e(__('Currency Code')); ?> <span
                                        class="required">*</span></label>
                                <input type="text" id="code" name="code"
                                    class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> text-uppercase"
                                    value="<?php echo e(old('code', $currency->code)); ?>" placeholder="<?php echo e(__('e.g., USD')); ?>"
                                    maxlength="3" required>
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
                                <small class="form-text"><?php echo e(__('3-letter ISO currency code')); ?></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="symbol" class="form-label"><?php echo e(__('Currency Symbol')); ?> <span
                                        class="required">*</span></label>
                                <input type="text" id="symbol" name="symbol"
                                    class="form-control <?php $__errorArgs = ['symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('symbol', $currency->symbol)); ?>" placeholder="<?php echo e(__('e.g., $')); ?>"
                                    maxlength="5" required>
                                <?php $__errorArgs = ['symbol'];
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exchange_rate" class="form-label"><?php echo e(__('Exchange Rate')); ?> <span
                                        class="required">*</span></label>
                                <input type="number" id="exchange_rate" name="exchange_rate"
                                    class="form-control <?php $__errorArgs = ['exchange_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('exchange_rate', $currency->exchange_rate)); ?>" step="0.0001" min="0"
                                    placeholder="<?php echo e(__('e.g., 1.0000')); ?>" required>
                                <?php $__errorArgs = ['exchange_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text"><?php echo e(__('Exchange rate against default currency')); ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-options">
                                <div class="form-check">
                                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input"
                                        value="1" <?php echo e(old('is_active', $currency->is_active) ? 'checked' : ''); ?>>
                                    <label for="is_active" class="form-check-label">
                                        <?php echo e(__('Active')); ?>

                                    </label>
                                    <small
                                        class="form-text"><?php echo e(__('Whether this currency is available for use')); ?></small>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" id="is_default" name="is_default" class="form-check-input"
                                        value="1" <?php echo e(old('is_default', $currency->is_default) ? 'checked' : ''); ?>>
                                    <label for="is_default" class="form-check-label">
                                        <?php echo e(__('Set as Default Currency')); ?>

                                    </label>
                                    <small
                                        class="form-text"><?php echo e(__('This will replace the current default currency')); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($currency->is_default): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <?php echo e(__('This is the default currency. Exchange rates for other currencies are calculated relative to this currency.')); ?>

                    </div>
                    <?php endif; ?>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo e(__('Update Currency')); ?>

                        </button>
                        <a href="<?php echo e(route('admin.currencies.index')); ?>" class="btn btn-secondary">
                            <?php echo e(__('Cancel')); ?>

                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title"><?php echo e(__('Currency Statistics')); ?></h3>
            </div>
            <div class="card-body">
                <div class="card modern-card stats-card mb-3">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label"><?php echo e(__('Created')); ?></div>
                            <div class="stats-number"><?php echo e($currency->created_at->format('Y-m-d H:i')); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card modern-card stats-card mb-3">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label"><?php echo e(__('Last Updated')); ?></div>
                            <div class="stats-number"><?php echo e($currency->updated_at->format('Y-m-d H:i')); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card modern-card stats-card">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label"><?php echo e(__('Status')); ?></div>
                            <div>
                                <?php if($currency->is_active): ?>
                                <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                                <?php else: ?>
                                <span class="badge bg-danger"><?php echo e(__('Inactive')); ?></span>
                                <?php endif; ?>
                                <?php if($currency->is_default): ?>
                                <span class="badge bg-warning ms-2"><?php echo e(__('Default')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title"><?php echo e(__('Quick Actions')); ?></h3>
            </div>
            <div class="card-body">
                <?php if(!$currency->is_default): ?>
                <form action="<?php echo e(route('admin.currencies.set-default', $currency)); ?>" method="POST" class="mb-3">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-warning btn-block js-confirm" data-confirm="<?php echo e(__('Are you sure you want to set this as default currency?')); ?>">
                        <i class="fas fa-star"></i>
                        <?php echo e(__('Set as Default')); ?>

                    </button>
                </form>
                <?php endif; ?>

                <?php if(!$currency->is_default): ?>
                <button type="button" class="btn btn-outline-danger btn-block" data-action="delete-currency">
                    <i class="fas fa-trash"></i> <?php echo e(__('Delete Currency')); ?>

                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<?php if(!$currency->is_default): ?>
<form id="deleteForm" action="<?php echo e(route('admin.currencies.destroy', $currency)); ?>" method="POST" class="d-none">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/currencies.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/currencies-edit.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/currencies/edit.blade.php ENDPATH**/ ?>