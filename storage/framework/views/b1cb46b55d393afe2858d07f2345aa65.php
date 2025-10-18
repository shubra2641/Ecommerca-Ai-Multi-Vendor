<?php if(($slides ?? collect())->count()): ?>
<section class="hero-slider" aria-label="<?php echo e(__('Featured')); ?>">
    <div class="hero-slider-viewport">
        <div class="hero-slider-track" data-hero-slider-track>
            <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="hero-slide" role="group" aria-roledescription="slide" aria-label="<?php echo e($loop->iteration.' / '.$slides->count()); ?>">
                    <img class="hero-slide-img" src="<?php echo e($sl->image ? asset('storage/'.$sl->image) : asset('images/product-placeholder.svg')); ?>" alt="<?php echo e($sl->title ?? $sl->subtitle ?? ('Slide '.$loop->iteration)); ?>">
                    <div class="hero-slide-overlay">
                        <?php if(!empty($sl->title)): ?><h2 class="hero-slide-title"><?php echo e($sl->title); ?></h2><?php endif; ?>
                        <?php if(!empty($sl->subtitle)): ?><p class="hero-slide-sub"><?php echo e($sl->subtitle); ?></p><?php endif; ?>
                        <?php if(!empty($sl->button_text) && !empty($sl->link_url)): ?>
                            <a href="<?php echo e($sl->link_url); ?>" class="btn btn-primary hero-slide-cta"><?php echo e($sl->button_text); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="button" class="hero-nav prev" data-hero-prev aria-label="<?php echo e(__('Previous slide')); ?>" hidden>‹</button>
        <button type="button" class="hero-nav next" data-hero-next aria-label="<?php echo e(__('Next slide')); ?>" hidden>›</button>
        <div class="hero-dots" data-hero-dots aria-label="<?php echo e(__('Slide navigation')); ?>" hidden></div>
    </div>
    <noscript>
        <div class="hero-noscript-fallback">
            <?php if(($slides ?? collect())->first()): ?>
                <img src="<?php echo e(($slides->first()->image ? asset('storage/'.$slides->first()->image) : asset('images/product-placeholder.svg'))); ?>" alt="<?php echo e($slides->first()->title ?? 'Slide'); ?>" class="hero-slide-img">
            <?php endif; ?>
        </div>
    </noscript>
</section>
<?php endif; ?>
<?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/partials/hero-slider.blade.php ENDPATH**/ ?>