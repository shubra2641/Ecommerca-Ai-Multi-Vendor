<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Easy')); ?> - <?php echo $__env->yieldContent('title'); ?></title>
    <!-- Selected Font Meta -->
    <meta name="selected-font" content="<?php echo e($selectedFont); ?>">
    <!-- Local font-face (Google Fonts removed for CSP) -->
    <!-- Bootstrap (local) -->
    <link href="<?php echo e(asset('vendor/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">
    <!-- Unified Admin CSS - All styles consolidated -->
    <link rel="preload" href="<?php echo e(asset('assets/admin/css/admin.css')); ?>" as="style">
    <link href="<?php echo e(asset('assets/admin/css/admin.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="body" data-font-active="<?php echo e($selectedFont); ?>" data-admin-base="<?php echo e(url('')); ?>" <?php if(session()->pull('refresh_admin_notifications')): ?> data-refresh-admin-notifications="1" <?php endif; ?>>
    <?php echo $__env->make('components.noscript-warning', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- Sidebar -->
    <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- Main Content -->
        <main class="main-content">
                <?php echo $__env->make('admin.top-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="page-content">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
        </div>
    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <!-- Essential Dependencies -->
    <script src="<?php echo e(asset('vendor/jquery/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendor/chart.js/chart.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/countup.js')); ?>" defer></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script src="<?php echo e(asset('vendor/bootstrap/bootstrap.bundle.min.js')); ?>" defer></script>

    <!-- Unified Admin JS - All functionality consolidated -->
    <link rel="preload" href="<?php echo e(asset('assets/admin/js/admin.js')); ?>" as="script">
    <script src="<?php echo e(asset('assets/admin/js/admin.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/admin-charts.js')); ?>" defer></script>
    <!-- Font Loader Script -->

</body>

</html><?php /**PATH D:\xampp1\htdocs\easy\resources\views/layouts/admin.blade.php ENDPATH**/ ?>