<?php $__env->startSection('title', __('Profile').' - '.config('app.name')); ?>
<?php $__env->startSection('content'); ?>

<section class="account-section">
    <div class="container account-grid">
        <?php echo $__env->make('front.account._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <main class="account-main">
            <div class="profile-panels">
                <h1 class="page-title"><?php echo e(__('Profile')); ?></h1>
                <p class="page-sub"><?php echo e(__('View & Update Your Personal and Contact Information')); ?></p>
                <div class="profile-layout">
                    <div class="profile-main">
                        <form method="post" action="<?php echo e(route('user.profile.update')); ?>" class="profile-form">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="panel-row">
                                <div class="panel-block">
                                    <h4><?php echo e(__('Contact Information')); ?></h4>
                                    <div class="two-cols">
                                        <div class="field">
                                            <label><?php echo e(__('Email')); ?></label>
                                            <input type="email" name="email"
                                                value="<?php echo e(old('email', auth()->user()->email)); ?>">
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="field">
                                            <label><?php echo e(__('Phone number')); ?></label>
                                            <input type="text" name="phone_number"
                                                value="<?php echo e(old('phone_number', auth()->user()->phone_number)); ?>">
                                            <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="two-cols mt-1">
                                        <div class="field">
                                            <label><?php echo e(__('Balance')); ?></label>
                                            <input type="text" value="<?php echo e(auth()->user()->formatted_balance); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="two-cols mt-1">
                                        <div class="field">
                                            <label><?php echo e(__('WhatsApp number')); ?></label>
                                            <input type="text" name="whatsapp_number"
                                                value="<?php echo e(old('whatsapp_number', auth()->user()->whatsapp_number)); ?>">
                                            <?php $__errorArgs = ['whatsapp_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                    </div>
                                </div>
                                <div class="panel-block">
                                    <h4><?php echo e(__('Personal Information')); ?></h4>
                                    <div class="two-cols">
                                        <div class="field">
                                            <label><?php echo e(__('Name')); ?></label>
                                            <input type="text" name="name"
                                                value="<?php echo e(old('name', auth()->user()->name)); ?>">
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="field">
                                            <label><?php echo e(__('Password')); ?></label>
                                            <input type="password" name="password" autocomplete="new-password">
                                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="two-cols mt-1">
                                        <div class="field">
                                            <label><?php echo e(__('Confirm Password')); ?></label>
                                            <input type="password" name="password_confirmation"
                                                autocomplete="new-password">
                                        </div>
                                        <div class="field">
                                            <label>&nbsp;</label>
                                            <div class="muted small">
                                                <?php echo e(__('Leave password fields empty to keep current password.')); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="actions">
                                <button class="btn btn-primary btn-sm" type="submit"><?php echo e(__('Update Profile')); ?></button>
                            </div>
                        </form>
                    </div>
                    <aside class="profile-side">
                        <div class="user-card">
                            <div class="profile-header">
                                <div class="avatar">
                                    <?php echo e(strtoupper(substr(auth()->user()->name ?? auth()->user()->email,0,1))); ?></div>
                                <div class="user-meta">
                                    <div class="user-line"><?php echo e(auth()->user()->name ?? __('User')); ?></div>
                                    <div class="muted"><?php echo e(auth()->user()->email); ?></div>
                                </div>
                            </div>
                            <div class="quick-links">
                                <a href="<?php echo e(route('user.addresses')); ?>" class="quick-link"><?php echo e(__('Manage addresses')); ?>

                                    <span class="muted">›</span></a>
                                <a href="<?php echo e(route('user.orders')); ?>" class="quick-link"><?php echo e(__('My orders')); ?> <span
                                        class="muted">›</span></a>
                                <a href="<?php echo e(route('user.invoices')); ?>" class="quick-link"><?php echo e(__('Invoices')); ?> <span
                                        class="muted">›</span></a>
                            </div>
                        </div>
                            <div class="panel-block">
                            <h4><?php echo e(__('Profile Completion')); ?></h4>
                            <div class="progress-bar progress-track">
                                <span data-progress="<?php echo e(auth()->user()->profile_completion); ?>" class="progress-fill"></span>
                            </div>
                            <div class="progress-label"><?php echo e(__('Completion')); ?>

                                <strong><?php echo e(auth()->user()->profile_completion); ?>%</strong>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/account/profile.blade.php ENDPATH**/ ?>