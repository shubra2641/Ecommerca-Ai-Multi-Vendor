

<?php if(($showcaseSectionsActiveCount ?? 0) > 0): ?>
<section class="footer-showcase" aria-labelledby="showcase-heading">
    <div class="container">
        <div class="showcase-grid cols-<?php echo e($showcaseSectionsActiveCount); ?>" role="list">
            <?php $__currentLoopData = $showcaseSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($sec['type'] !== 'brands'): ?>
                <div class="showcase-col" role="listitem">
                    <?php if($sec['title']): ?><h3 class="showcase-title"><?php echo e($sec['title']); ?></h3><?php endif; ?>
                    <ul class="product-mini-list" role="list">
                        <?php $__currentLoopData = $sec['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="mini-item <?php echo e($p->mini_flags); ?>">
                                <a href="<?php echo e(route('products.show',$p->slug)); ?>" class="mini-thumb" aria-label="<?php echo e($p->name); ?>">
                                    <img loading="lazy" src="<?php echo e($p->mini_image_url); ?>" alt="<?php echo e($p->name); ?>" class="<?php echo e($p->mini_image_is_placeholder ? 'is-placeholder' : ''); ?>">
                                </a>
                                <div class="mini-meta">
                                    <a href="<?php echo e(route('products.show',$p->slug)); ?>" class="mini-name"><?php echo e($p->mini_trunc_name); ?></a>
                                    <div class="mini-price">
                                        <strong><?php echo e($p->mini_price_html); ?></strong>
                                        <?php echo app('App\\Services\\HtmlSanitizer')->clean($p->mini_extra_html); ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if(isset($brandSec) && $brandSec): ?>
            <div class="brands-row" aria-labelledby="brands-heading">
                <h3 id="brands-heading" class="brands-title"><?php echo e($brandSec['title'] ?? __('Brands')); ?></h3>
                <div class="brands-slider" role="list">
                    <?php $__empty_1 = true; $__currentLoopData = $brandSec['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('products.index', ['brand'=>$b->slug])); ?>" class="brand-item" role="listitem" aria-label="<?php echo e($b->name); ?> <?php if(isset($b->products_count)): ?> (<?php echo e($b->products_count); ?>) <?php endif; ?>">
                            <span class="brand-name"><?php echo e($b->name); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="brand-empty"><?php echo e(__('No brands')); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/partials/footer-showcase.blade.php ENDPATH**/ ?>