<?php $__env->startSection('title', isset($user) && $user->exists ? __('Edit User') : __('Create User')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-start flex-wrap">
    <div>
        <h1 class="page-title">
            <?php if(isset($user) && $user->exists): ?>
                <?php echo e(__('Edit User')); ?>: <?php echo e($user->name); ?>

            <?php else: ?>
                <?php echo e(__('Create New User')); ?>

            <?php endif; ?>
        </h1>
        <p class="page-description">
            <?php if(isset($user) && $user->exists): ?>
                <?php echo e(__('Update user information and settings')); ?>

            <?php else: ?>
                <?php echo e(__('Add a new user to the system')); ?>

            <?php endif; ?>
        </p>
    </div>
    <div class="page-actions mt-2">
        <?php if(isset($user) && $user->exists): ?>
            <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-secondary me-2">
                <i class="fas fa-eye"></i>
                <?php echo e(__('View User')); ?>

            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to Users')); ?>

        </a>
    </div>
</div>

<form action="<?php echo e($userFormAction); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php if(isset($user) && $user->exists): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="card modern-card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-user text-primary"></i>
            <h3 class="card-title mb-0"><?php echo e(__('Basic Information')); ?></h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="form-label required"><?php echo e(__('Full Name')); ?></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name"
                            name="name" value="<?php echo e(old('name', $user->name ?? '')); ?>" required
                            placeholder="<?php echo e(__('Enter full name')); ?>">
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
                        <label for="email" class="form-label required"><?php echo e(__('Email Address')); ?></label>
                        <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email"
                            name="email" value="<?php echo e(old('email', $user->email ?? '')); ?>" required
                            placeholder="<?php echo e(__('Enter email address')); ?>">
                        <?php $__errorArgs = ['email'];
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
            </div>
        </div>
    </div>

    <div class="card modern-card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0"><?php echo e(__('Password Information')); ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password"
                            class="form-label <?php echo e(isset($user) && $user->exists ? '' : 'required'); ?>"><?php echo e(__('Password')); ?></label>
                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="password" name="password" placeholder="<?php echo e(__('Enter password')); ?>"
                            <?php echo e(isset($user) && $user->exists ? '' : 'required'); ?>>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php if(isset($user) && $user->exists): ?>
                            <small class="form-text text-muted"><?php echo e(__('Leave blank to keep current password')); ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation"
                            class="form-label <?php echo e(isset($user) && $user->exists ? '' : 'required'); ?>"><?php echo e(__('Confirm Password')); ?></label>
                        <input type="password" class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="password_confirmation" name="password_confirmation"
                            placeholder="<?php echo e(__('Confirm password')); ?>"
                            <?php echo e(isset($user) && $user->exists ? '' : 'required'); ?>>
                        <?php $__errorArgs = ['password_confirmation'];
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
            </div>
        </div>
    </div>

    <div class="card modern-card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0"><?php echo e(__('Contact Information')); ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone" class="form-label"><?php echo e(__('Phone Number')); ?></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="phone"
                            name="phone" value="<?php echo e(old('phone', $user->phone ?? '')); ?>"
                            placeholder="<?php echo e(__('Enter phone number')); ?>">
                        <?php $__errorArgs = ['phone'];
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
                        <label for="whatsapp" class="form-label"><?php echo e(__('WhatsApp Number')); ?></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="whatsapp"
                            name="whatsapp" value="<?php echo e(old('whatsapp', $user->whatsapp ?? '')); ?>"
                            placeholder="<?php echo e(__('Enter WhatsApp number')); ?>">
                        <?php $__errorArgs = ['whatsapp'];
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
            </div>
        </div>
    </div>

    <div class="card modern-card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0"><?php echo e(__('Role & Permissions')); ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role" class="form-label required"><?php echo e(__('Role')); ?></label>
                        <select class="form-control <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="role" name="role" required>
                            <option value=""><?php echo e(__('Select Role')); ?></option>
                            <option value="user" <?php echo e(old('role', $user->role ?? '') === 'user' ? 'selected' : ''); ?>>
                                <?php echo e(__('User')); ?>

                            </option>
                            <option value="vendor" <?php echo e(old('role', $user->role ?? '') === 'vendor' ? 'selected' : ''); ?>>
                                <?php echo e(__('Vendor')); ?>

                            </option>
                            <option value="admin" <?php echo e(old('role', $user->role ?? '') === 'admin' ? 'selected' : ''); ?>>
                                <?php echo e(__('Admin')); ?>

                            </option>
                        </select>
                        <?php $__errorArgs = ['role'];
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
                        <label for="balance" class="form-label"><?php echo e(__('Balance')); ?></label>
                        <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="balance" name="balance" value="<?php echo e(old('balance', $user->balance ?? 0)); ?>"
                            placeholder="<?php echo e(__('Enter balance')); ?>">
                        <?php $__errorArgs = ['balance'];
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
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="approved" name="approved" value="1"
                            <?php echo e(old('approved', (isset($user) && $user->exists && $user->approved_at) ? '1' : '0') === '1' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="approved">
                            <?php echo e(__('Approved User')); ?>

                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card modern-card">
                <div class="card-body">
                    <div class="form-actions d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <?php if(isset($user) && $user->exists): ?>
                                <i class="fas fa-save"></i>
                                <?php echo e(__('Update User')); ?>

                            <?php else: ?>
                                <i class="fas fa-plus"></i>
                                <?php echo e(__('Create User')); ?>

                            <?php endif; ?>
                        </button>
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            <?php echo e(__('Cancel')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>

            <div class="col-md-4">
            <?php if(isset($user) && $user->exists): ?>
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <h3 class="card-title mb-0"><?php echo e(__('User Summary')); ?></h3>
                </div>
                <div class="card-body">
                    <div class="user-summary">
                        <div class="summary-item">
                            <label><?php echo e(__('Created')); ?>:</label>
                            <span><?php echo e($user->created_at->format('M d, Y')); ?></span>
                        </div>
                        <div class="summary-item">
                            <label><?php echo e(__('Status')); ?>:</label>
                            <span>
                                <?php if($user->approved_at): ?>
                                    <span class="badge bg-success"><?php echo e(__('Approved')); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><?php echo e(__('Pending')); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="summary-item">
                            <label><?php echo e(__('Role')); ?>:</label>
                            <span class="badge bg-primary"><?php echo e(ucfirst($user->role)); ?></span>
                        </div>
                    </div>

                    <?php if(!$user->approved_at): ?>
                    <div class="quick-actions">
                        <form action="<?php echo e(route('admin.users.approve', $user)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i>
                                <?php echo e(__('Approve User')); ?>

                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/users/form.blade.php ENDPATH**/ ?>