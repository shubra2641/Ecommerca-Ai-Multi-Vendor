<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', config('app.name', 'Easy')); ?></title>
    <?php echo $__env->yieldContent('meta'); ?>
    <!-- Selected Font Meta -->
    <meta name="selected-font" content="<?php echo e($selectedFont); ?>">
    <!-- Bootstrap -->
    <link href="<?php echo e(asset('vendor/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">

</head>

<body class="guest-layout">
    <header class="auth-topbar">
        <div class="topbar-inner">
            <div class="topbar-left">
                <a href="<?php echo e(url('/')); ?>" class="topbar-logo" aria-label="<?php echo e($siteName); ?>">
                    <?php if($logoPath && file_exists(public_path('storage/'.$logoPath))): ?>
                    <img src="<?php echo e(asset('storage/'.$logoPath)); ?>" alt="<?php echo e($siteName); ?>" />
                    <?php else: ?>
                    <span class="logo-text"><?php echo e($siteName); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="topbar-right">
                <div class="topbar-lang">
                    <details class="profile-menu" role="list">
                        <summary class="profile-trigger" aria-haspopup="menu">
                            <i class="fas fa-globe" aria-hidden="true"></i>
                            <span class="d-none d-sm-inline"><?php echo e(strtoupper(app()->getLocale())); ?></span>
                            <i class="chevron" aria-hidden="true">▾</i>
                        </summary>
                        <ul class="profile-dropdown" role="menu">
                            
                            <?php $__currentLoopData = ($languages ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <form method="POST" action="<?php echo e(route('language.switch')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="language" value="<?php echo e($lang->code); ?>">
                                    <button type="submit" <?php if(app()->getLocale()===$lang->code): echo 'disabled'; endif; ?>><?php echo e($lang->name); ?></button>
                                </form>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </details>
                </div>
                <div class="topbar-profile">
                    <?php if(auth()->guard()->check()): ?>
                    <details class="profile-menu" role="list">
                        <summary class="profile-trigger" aria-haspopup="menu">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>&background=6366f1&color=fff"
                                alt="<?php echo e(auth()->user()->name); ?>" class="avatar" />
                            <span class="d-none d-sm-inline"><?php echo e(auth()->user()->name); ?></span>
                            <i class="chevron" aria-hidden="true">▾</i>
                        </summary>
                        <ul class="profile-dropdown" role="menu">
                            <?php if(Route::has('profile.edit')): ?>
                            <li><a href="<?php echo e(route('profile.edit')); ?>"><?php echo e(__('Edit Profile')); ?></a></li>
                            <?php endif; ?>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"><?php echo e(__('Logout')); ?></button>
                                </form>
                            </li>
                        </ul>
                    </details>
                    <?php else: ?>
                    <details class="profile-menu" role="list">
                        <summary class="profile-trigger" aria-haspopup="menu">
                            <i class="fas fa-user-circle guest-icon" aria-hidden="true"></i>
                            <span class="d-none d-sm-inline"><?php echo e(__('Account')); ?></span>
                            <i class="chevron" aria-hidden="true">▾</i>
                        </summary>
                        <ul class="profile-dropdown" role="menu">
                            <?php if(Route::has('login')): ?>
                            <li><a href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a></li>
                            <?php endif; ?>
                            <?php if(Route::has('register')): ?>
                            <li><a href="<?php echo e(route('register')); ?>"><?php echo e(__('Register')); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </details>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <div class="auth-container">
        <div class="auth-branding-panel">
            <h1><?php echo e($siteName); ?></h1>
            <p><?php echo e(__('A new experience in online shopping.')); ?></p>
        </div>
        <div class="auth-form-panel">
            <?php if (! empty(trim($__env->yieldContent('content')))): ?>
            <?php echo $__env->yieldContent('content'); ?>
            <?php elseif(isset($slot)): ?>
            <?php echo e($slot); ?>

            <?php endif; ?>
        </div>
    </div>

    <!-- Essential Dependencies -->
    <script src="<?php echo e(asset('vendor/bootstrap/bootstrap.bundle.min.js')); ?>" defer></script>
    <!-- Unified Customer JS - All functionality consolidated -->
    <script src="<?php echo e(asset('assets/customer/js/customer.js')); ?>"></script>

</body>

</html><?php /**PATH D:\xampp1\htdocs\easy\resources\views/layouts/guest.blade.php ENDPATH**/ ?>