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
    <?php echo $__env->yieldPushContent('meta'); ?>
    <meta name="selected-font" content="<?php echo e($selectedFont); ?>">
    
    <meta name="allow-google-fonts" content="0">
    <link rel="stylesheet" href="<?php echo e(asset('css/local-fonts.css')); ?>">
    <!-- Inline font CSS removed for CSP compliance; default font rules moved to envato-fixes.css; JS font-loader applies selected font at runtime -->
    <!-- Bootstrap (local) -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/bootstrap/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/envato-fixes.css')); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">

    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="<?php echo e(asset('assets/customer/css/customer.css')); ?>" rel="stylesheet">
    <!-- Critical CSS is now in external file -->
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <?php if(request()->is('checkout*') || request()->routeIs('checkout.*')): ?>
    <script src="<?php echo e(asset('front/js/checkout-pattern-sanitizer.js')); ?>"></script>
    <?php endif; ?>
</head>

<body class="<?php if(request()->routeIs('user.*')): ?> account-body <?php endif; ?>">
    <div id="app-loader" class="app-loader hidden" aria-hidden="true">
        <div class="loader-core">
            <div class="spinner"></div>
            <div class="loader-brand"><?php echo e(config('app.name')); ?></div>
        </div>
    </div>
    <?php echo $__env->make('front.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div id="flash-messages-root" class="position-fixed flash-root"
        data-flash-success='<?php echo e(e(json_encode(session('success'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'
        data-flash-error='<?php echo e(e(json_encode(session('error'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'
        data-flash-warning='<?php echo e(e(json_encode(session('warning'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'
        data-flash-info='<?php echo e(e(json_encode(session('info'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'></div>
    <?php // backup: layout.blade.php original l10n-data block saved as layout.blade.php.bak ?>
    <template id="l10n-data"><?php echo json_encode([
        'added_to_cart' => __('Added to cart'),
        'failed_add_to_cart' => __('Failed to add to cart'),
        'select_options_first' => __('Please select product options first.'),
        'subscription_saved' => __('Subscription saved'),
        'network_error' => __('Network error'),
        'removed_from_cart' => __('Removed from cart'),
        'moved_to_wishlist' => __('Moved to wishlist'),
        'coupon_applied' => __('Coupon applied'),
        'failed_apply_coupon' => __('Failed to apply coupon'),
        'please_select_required_options' => __('Please select required options first'),
        'sku_copied' => __('SKU copied'),
        'failed_copy' => __('Failed to copy'),
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?></template>
    <main class="site-main"><?php echo $__env->yieldContent('content'); ?></main>
    <?php echo $__env->renderWhen(View::exists('front.partials.footer_extended'),'front.partials.footer_extended', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
    <?php echo $__env->yieldPushContent('modals'); ?>
    <?php if(request()->routeIs('products.index') || request()->routeIs('products.category') ||
    request()->routeIs('products.tag')): ?>
    <?php echo $__env->make('front.partials.notify-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <!-- Removed local toast test button now that unified notification system is stable -->
    <!-- Essential Dependencies -->
    <script src="<?php echo e(asset('vendor/bootstrap/bootstrap.bundle.min.js')); ?>" defer></script>
    
    <!-- Unified Customer JS - All functionality consolidated -->
    <script src="<?php echo e(asset('assets/customer/js/customer.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Font Loader Script -->
    <script>
        // Load selected font
        document.addEventListener('DOMContentLoaded', function() {
            const fontName = document.querySelector('meta[name="selected-font"]').getAttribute('content');
            if (fontName && fontName !== 'Inter') {
                document.body.style.fontFamily = fontName + ', sans-serif';
            }
        });
    </script>
</body>

</html><?php /**PATH D:\xampp\htdocs\easy\resources\views/front/layout.blade.php ENDPATH**/ ?>