<?php $__env->startSection('title', __('Profile Settings')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('Profile Settings')); ?></h1>
    <p class="page-description"><?php echo e(__('Manage your account information and security settings')); ?></p>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-md-6">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title"><?php echo e(__('Profile Information')); ?></h3>
            </div>
            <form action="<?php echo e(route('admin.profile.update')); ?>" method="POST" class="card-body">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="form-group">
                    <label for="name" class="form-label"><?php echo e(__('Full Name')); ?></label>
                    <input type="text" id="name" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('name', $user->name)); ?>" required>
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
                    <label for="email" class="form-label"><?php echo e(__('Email Address')); ?></label>
                    <input type="email" id="email" name="email"
                        class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('email', $user->email)); ?>" required>
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

                <div class="form-group">
                    <label for="phone_number" class="form-label"><?php echo e(__('Phone Number')); ?></label>
                    <input type="text" id="phone_number" name="phone_number"
                        class="form-control <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('phone_number', $user->phone_number)); ?>">
                    <?php $__errorArgs = ['phone_number'];
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
                    <label class="form-label"><?php echo e(__('Role')); ?></label>
                    <input type="text" class="form-control" value="<?php echo e(ucfirst($user->role)); ?>" readonly disabled>
                    <small class="form-text text-muted"><?php echo e(__('Your role cannot be changed')); ?></small>
                </div>

                <div class="form-group">
                    <label class="form-label"><?php echo e(__('Account Status')); ?></label>
                    <div class="d-flex align-items-center">
                        <?php if($user->approved_at): ?>
                        <span class="badge bg-success"><?php echo e(__('Approved')); ?></span>
                        <small class="text-muted ms-2"><?php echo e(__('Approved on')); ?>

                            <?php echo e(\Carbon\Carbon::parse($user->approved_at)->format('Y-m-d H:i')); ?></small>
                        <?php else: ?>
                        <span class="badge bg-warning"><?php echo e(__('Pending Approval')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <?php echo e(__('Update Profile')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Change -->
    <div class="col-md-6">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title"><?php echo e(__('Change Password')); ?></h3>
            </div>
            <form action="<?php echo e(route('admin.profile.password')); ?>" method="POST" class="card-body">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="form-group">
                    <label for="current_password" class="form-label"><?php echo e(__('Current Password')); ?></label>
                    <input type="password" id="current_password" name="current_password"
                        class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['current_password'];
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
                    <label for="password" class="form-label"><?php echo e(__('New Password')); ?></label>
                    <input type="password" id="password" name="password"
                        class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
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
                    <small class="form-text text-muted"><?php echo e(__('Minimum 8 characters required')); ?></small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label"><?php echo e(__('Confirm New Password')); ?></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                        required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i>
                        <?php echo e(__('Change Password')); ?>

                    </button>
                </div>
            </form>
        </div>

        <!-- Account Information -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title"><?php echo e(__('Account Information')); ?></h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label><?php echo e(__('Member Since')); ?></label>
                    <span><?php echo e($user->created_at->format('F j, Y')); ?></span>
                </div>

                <div class="info-item">
                    <label><?php echo e(__('Last Login')); ?></label>
                    <span><?php echo e($user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never')); ?></span>
                </div>

                <div class="info-item">
                    <label><?php echo e(__('User ID')); ?></label>
                    <span><?php echo e($user->id); ?></span>
                </div>

                <?php if($user->balance !== null): ?>
                <div class="info-item">
                    <label><?php echo e(__('Balance')); ?></label>
                    <span class="badge bg-info"><?php echo e(number_format($user->balance, 2)); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/profile/edit.blade.php ENDPATH**/ ?>