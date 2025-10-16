<?php $__env->startSection('title', config('app.name')); ?>
<?php $__env->startSection('content'); ?>
<main role="main">
    <?php echo $__env->make('front.partials.hero-slider', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(($flashSaleProducts ?? collect())->count()): ?>
    <section class="flash-sale-section animate-fade-in-up" aria-labelledby="flash-sale-title">
        <div class="container">
            <div class="section-header">
                <h2 id="flash-sale-title" class="section-title"><?php echo e(__('Flash Sale')); ?></h2>
                <p class="section-sub"><?php echo e(__('Limited time deals – don\'t miss out!')); ?></p>
                <?php if(!empty($flashSaleEndsAt)): ?>
                <div class="flash-countdown" data-flash-countdown data-end="<?php echo e($flashSaleEndsAt->utc()->format('Y-m-d\TH:i:s\Z')); ?>" aria-live="polite">
                    <span class="cd-label"><?php echo e(__('Ends in')); ?>:</span>
                    <span class="cd-part" data-d>00</span><span class="cd-sep">:</span>
                    <span class="cd-part" data-h>00</span><span class="cd-sep">:</span>
                    <span class="cd-part" data-m>00</span><span class="cd-sep">:</span>
                    <span class="cd-part" data-s>00</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="products-grid flash-sale-grid">
                <?php $__currentLoopData = ($flashSaleProducts ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('front.products.partials.product-card', ['product'=>$product, 'wishlistIds'=>$wishlistIds, 'compareIds'=>$compareIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="section-footer">
                <a href="<?php echo e(route('products.index', ['filter'=>'on-sale'])); ?>" class="btn btn-outline btn-lg"><?php echo e(__('View All Deals')); ?></a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Categories Section (Circular) -->
    <section class="shop-categories animate-fade-in-up" aria-labelledby="categories-title">
        <div class="container">
            <div class="section-header">
                <h2 id="categories-title" class="section-title"><?php echo e(__('Shop by Category')); ?></h2>
                <p class="section-sub"><?php echo e(__('Browse our main categories')); ?></p>
            </div>
            <ul class="cat-main-list" role="list" aria-label="<?php echo e(__('Main categories')); ?>">
        <?php $__currentLoopData = ($landingCategories ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="cat-main-item">
            <a href="<?php echo e(route('products.category', $mc->slug)); ?>" class="cat-pill" aria-label="<?php echo e(__('Browse :name products', ['name'=>$mc->name])); ?>">
                            <span class="icon-ring">
                <?php if($mc->has_image): ?>
                    <img loading="lazy" src="<?php echo e($mc->image_url); ?>" alt="<?php echo e($mc->name); ?>">
                                <?php else: ?>
                                    <svg class="placeholder-icon" viewBox="0 0 24 24" aria-hidden="true"><path stroke="currentColor" stroke-width="1.6" d="M3 18V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path stroke="currentColor" stroke-width="1.6" d="m3 15 4.5-4.5 5.5 5.5M14 14l2-2 5 5"/><circle cx="10" cy="9" r="2" stroke="currentColor" stroke-width="1.6" fill="none"/></svg>
                                <?php endif; ?>
                            </span>
                            <span class="cat-label"><?php echo e(__($mc->name)); ?></span>
                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <div class="section-footer">
                <a href="<?php echo e(route('products.index')); ?>" class="btn btn-outline btn-lg"><?php echo e(__('View All Products')); ?></a>
            </div>
        </div>
    </section>

    <?php if(($latestProducts ?? collect())->count()): ?>
            <section class="latest-products-section animate-fade-in-up" aria-labelledby="latest-products-title">
                <div class="container">
                    <div class="section-header">
                        <h2 id="latest-products-title" class="section-title"><?php echo e(__('Latest Products')); ?></h2>
                        <p class="section-sub"><?php echo e(__('Fresh arrivals just added to our catalog')); ?></p>
                    </div>
                    <div class="products-grid latest-products-grid">
                        <?php $__currentLoopData = ($latestProducts ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('front.products.partials.product-card', [ 'product'=>$product, 'wishlistIds'=>$wishlistIds, 'compareIds'=>$compareIds ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="section-footer">
                        <a href="<?php echo e(route('products.index', ['sort'=>'newest'])); ?>" class="btn btn-outline btn-lg"><?php echo e(__('View All New Arrivals')); ?></a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if(($latestPosts ?? collect())->count()): ?>
            <section class="latest-articles-section animate-fade-in-up" aria-labelledby="latest-articles-title" data-placeholder="<?php echo e(asset('images/product-placeholder.svg')); ?>">
                <div class="container">
                    <div class="section-header">
                        <h2 id="latest-articles-title" class="section-title"><?php echo e(__('Latest Articles')); ?></h2>
                        <p class="section-sub"><?php echo e(__('Insights & updates from our blog')); ?></p>
                    </div>
                    <div class="latest-articles-grid">
                        <?php $__currentLoopData = $latestPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="blog-card">
                                <a href="<?php echo e(route('blog.show',$post->slug)); ?>" class="thumb-wrapper" aria-label="<?php echo e($post->title); ?>">
                                    <?php if(!empty($post->featured_image)): ?>
                                        <img loading="lazy" src="<?php echo e($post->featured_image_url); ?>" alt="<?php echo e($post->title); ?>" />
                                    <?php else: ?>
                                        <img loading="lazy" src="<?php echo e($post->featured_image_url); ?>" class="is-placeholder" alt="<?php echo e($post->title); ?>" />
                                    <?php endif; ?>
                                </a>
                                <div class="card-body">
                                    <div class="meta-row">
                                        <?php if($post->published_at): ?>
                                            <span class="date"><?php echo e($post->published_at->format('M d, Y')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="post-title"><a href="<?php echo e(route('blog.show',$post->slug)); ?>"><?php echo e($post->title); ?></a></h3>
                                    <?php if($post->prepared_excerpt): ?>
                                        <p class="excerpt"><?php echo e($post->prepared_excerpt); ?></p>
                                    <?php endif; ?>
                                    <a class="read-more" href="<?php echo e(route('blog.show',$post->slug)); ?>"><?php echo e(__('Read more')); ?> →</a>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="section-footer">
                        <a href="<?php echo e(route('blog.index')); ?>" class="btn btn-outline btn-lg"><?php echo e(__('View All Articles')); ?></a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    <?php echo $__env->make('front.partials.homepage-banners', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('front.partials.footer-showcase', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/landing.blade.php ENDPATH**/ ?>