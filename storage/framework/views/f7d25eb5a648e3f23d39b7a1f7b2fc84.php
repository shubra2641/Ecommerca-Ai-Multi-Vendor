<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_','-',app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale()=='ar'?'rtl':'ltr'); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php if(config('services.webpush.vapid_public_key')): ?>
    <meta name="vapid-public-key" content="<?php echo e(config('services.webpush.vapid_public_key')); ?>">
    <?php endif; ?>
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>
    <meta name="theme-color" content="#ffffff">
    <?php if(app()->environment('production')): ?>
    <link rel="manifest" href="<?php echo e(asset('manifest.webmanifest')); ?>">
    <?php endif; ?>
    <meta name="app-base" content="<?php echo e(url('/')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <?php echo $__env->yieldContent('meta'); ?>
    <meta name="selected-font" content="<?php echo e($selectedFont); ?>">
    
    <meta name="allow-google-fonts" content="0">
    <!-- Bootstrap (local) -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/bootstrap/bootstrap.min.css')); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">
    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="<?php echo e(asset('assets/front/css/front.css')); ?>" rel="stylesheet">
    <!-- Critical CSS is now in external file -->
    <?php echo $__env->yieldContent('styles'); ?>
</head>

<body class="<?php if(request()->routeIs('user.*')): ?> account-body <?php endif; ?>">
    <div id="app-loader" class="app-loader" aria-hidden="false">
        <div class="loader-core">
            <div class="spinner"></div>
            <div class="loader-brand"><?php echo e(config('app.name')); ?></div>
        </div>
    </div>
    <?php echo $__env->make('front.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('front.partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
     <main class="site-main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <?php echo $__env->renderWhen(View::exists('front.partials.footer_extended'),'front.partials.footer_extended', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
    <?php echo $__env->yieldContent('modals'); ?>
    <?php if(request()->routeIs('products.index') || request()->routeIs('products.category') ||
    request()->routeIs('products.tag')): ?>
    <?php echo $__env->make('front.partials.notify-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <!-- Removed local toast test button now that unified notification system is stable -->
    <!-- Essential Dependencies -->
    <script src="<?php echo e(asset('vendor/bootstrap/bootstrap.bundle.min.js')); ?>" defer></script>
    
    <!-- Unified Customer JS - All functionality consolidated -->
    <script src="<?php echo e(asset('assets/front/js/front.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/front/js/pwa.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/front/js/flash.js')); ?>"></script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/layout.blade.php ENDPATH**/ ?>