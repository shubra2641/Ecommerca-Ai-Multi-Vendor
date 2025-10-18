
    <div class="product-card" role="group" aria-label="Product card">
        <div class="product-image <?php echo e(empty($product->main_image) ? 'is-empty' : ''); ?>">
            <a href="<?php echo e(route('products.show',$product->slug)); ?>" class="product-image-link" aria-label="View <?php echo e($product->name); ?>">
                <img src="<?php echo e($cardImageUrl); ?>" alt="<?php echo e($product->name); ?>" loading="lazy" decoding="async" class="product-image-media <?php echo e(empty($product->main_image)?'product-placeholder-img':''); ?>">
            </a>

            <div class="badges-inline" aria-hidden="true">
                <?php if($cardOnSale): ?>
                    <span class="product-badge sale-badge" title="Sale">-<?php echo e($cardDiscountPercent); ?>%</span>
                <?php endif; ?>
                <?php if($product->is_featured): ?>
                    <span class="product-badge featured-badge" title="Featured">Best Seller</span>
                <?php endif; ?>
                <?php if($cardAvailable === 0): ?>
                    <span class="product-badge out-badge" title="Out of stock"><?php echo e(__('Out of stock')); ?></span>
                <?php endif; ?>
            </div>

            
            <div class="top-controls" aria-hidden="false">
                <div class="top-controls-row">
                    
                    <form action="<?php echo e(route('wishlist.toggle')); ?>" method="POST" class="wishlist-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                        <button class="circle-btn icon-btn fav-btn <?php echo e($cardWishActive ? 'active' : ''); ?>"
                            aria-pressed="<?php echo e($cardWishActive ? 'true' : 'false'); ?>" aria-label="Toggle wishlist" type="submit">
                            <i class="fas fa-heart" aria-hidden="true"></i>
                        </button>
                    </form>
                    
                    
                    <form action="<?php echo e(route('compare.toggle')); ?>" method="POST" class="compare-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                        <button class="circle-btn icon-btn compare-btn <?php echo e($cardCmpActive ? 'is-active' : ''); ?>" title="Compare"
                            aria-label="Compare product" type="submit">
                            <i class="fas fa-chart-bar" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
                <div class="top-controls-col">
                    <button class="circle-btn icon-btn cart-quick"
                        title="<?php echo e($cardAvailable === 0 ? __('Out of stock') : __('Add to cart')); ?>"
                        aria-label="<?php echo e($cardAvailable === 0 ? __('Out of stock') : __('Add to cart')); ?>" data-product="<?php echo e($product->id); ?>" <?php echo e($cardAvailable === 0 ? 'disabled' : ''); ?>>
                        <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-content product-card-body">
            <div class="product-category"><a href="<?php echo e(route('products.category',$product->category->slug)); ?>"><?php echo e($product->category->name); ?></a></div>
            <h3 class="product-title"><a href="<?php echo e(route('products.show',$product->slug)); ?>"><?php echo e($product->name); ?></a></h3>
            <div class="product-rating">
                <span class="stars">
                    <?php for($i=1;$i<=5;$i++): ?> <span class="star <?php echo e($i <= $cardFullStars ? 'filled' : ''); ?>"><?php echo e($i <= $cardFullStars ? '★' : '☆'); ?></span>
                <?php endfor; ?>
                </span>
                <span><?php echo e(number_format($cardRating,1)); ?> • <?php echo e($cardReviewsCount); ?></span>
            </div>
            <?php if(!empty($cardSnippet)): ?>
            <div class="product-desc"><?php echo e($cardSnippet); ?></div>
            <?php endif; ?>
            <div class="product-price">
                <?php if($cardOnSale && $cardDisplaySalePrice): ?>
                <span class="price-sale"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($cardDisplaySalePrice,0)); ?></span>
                <span class="price-original"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($cardDisplayPrice,0)); ?></span>
                <span class="price-badge"><?php echo e($cardDiscountPercent); ?>% OFF</span>
                <?php else: ?>
                <span class="price-current"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($cardDisplayPrice,0)); ?></span>
                <?php endif; ?>
            </div>

            <?php if($product->type === 'variable'): ?>
            <a href="<?php echo e(route('products.show',$product->slug)); ?>"
                class="btn btn-primary btn-sm"><?php echo e(__('Select options')); ?></a>
            <?php else: ?>
            <?php if($cardAvailable === 0): ?>
            <button class="btn btn-outline btn-sm notify-btn" data-product="<?php echo e($product->id); ?>"
                data-type="back_in_stock" <?php if(auth()->guard()->check()): ?> data-email="<?php echo e(auth()->user()->email); ?>" <?php endif; ?>>
                <span class="notify-label"><?php echo e(__('Notify Me')); ?></span>
                <span class="notify-subscribed d-none"><?php echo e(__('Subscribed')); ?></span>
            </button>
            <?php else: ?>
            <form action="<?php echo e(route('cart.add')); ?>" method="POST" class="add-to-cart-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                <button class="btn btn-primary btn-sm" type="submit"><?php echo e(__('Add to Cart')); ?></button>
            </form>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/products/partials/product-card.blade.php ENDPATH**/ ?>