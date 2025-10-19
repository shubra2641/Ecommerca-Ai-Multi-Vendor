<?php if(($banners ?? collect())->count()): ?>
    <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $placement => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <section class="homepage-banners placement-<?php echo e(Str::slug($placement)); ?> animate-fade-in-up" aria-label="<?php echo e(__('Promotions')); ?>">
            <div class="container">
                <div class="banners-grid">
                    <?php $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a <?php if($bn->link_url): ?> href="<?php echo e($bn->link_url); ?>" <?php endif; ?> class="banner-item" aria-label="<?php echo e($bn->alt_text ?? __('Banner')); ?>">
                            <img loading="lazy" src="<?php echo e($bn->image ? asset('storage/'.$bn->image) : asset('images/product-placeholder.svg')); ?>" alt="<?php echo e($bn->alt_text ?? ''); ?>" class="banner-img">
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/partials/homepage-banners.blade.php ENDPATH**/ ?>