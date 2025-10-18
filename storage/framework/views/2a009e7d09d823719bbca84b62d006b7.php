<?php $__env->startSection('title', $product->seo_title ?: $product->name . ' - ' . config('app.name')); ?>

<?php $__env->startSection('meta'); ?>
<?php if($product->seo_description): ?>
<meta name="description" content="<?php echo e($product->seo_description); ?>">
<?php else: ?>
<meta name="description" content="<?php echo e($product->short_description ?: $product->name); ?>">
<?php endif; ?>
<?php if($product->seo_keywords): ?>
<meta name="keywords" content="<?php echo e($product->seo_keywords); ?>">
<?php endif; ?>
<meta property="og:title" content="<?php echo e($product->name); ?>">
<meta property="og:description" content="<?php echo e($product->short_description ?: $product->name); ?>">
<meta property="og:type" content="product">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="product-details-section">
    <div class="container">
        <div class="product-details-layout">
            <div class="product-gallery">
                
                <div class="main-image" role="group" aria-label="Product images">
                    <img id="productMainImage" src="<?php echo e($mainImage['url'] ?? asset('front/images/default-product.png')); ?>" alt="<?php echo e($product->name); ?>" loading="lazy">
                </div>
                <?php if(($gallery->count() ?? 0) > 1): ?>
                <div class="thumbnail-gallery" aria-label="Product thumbnails">
                    <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="thumbnail <?php echo e($index === 0 ? 'active' : ''); ?>" data-image="<?php echo e($img['url']); ?>" aria-label="Show image <?php echo e($index + 1); ?>">
                            <img src="<?php echo e($img['url']); ?>" alt="<?php echo e($product->name); ?> - Image <?php echo e($index + 1); ?>" loading="lazy">
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="product-info product-info-card">
                <div class="pi-meta-row">
                    <?php if($brandName): ?>
                        <span class="pi-brand"><a href="<?php echo e(route('products.brand', $product->brand->slug)); ?>"><?php echo e(strtoupper($brandName)); ?></a></span>
                        <span class="sep">›</span>
                    <?php endif; ?>
                    <span class="pi-category"><a href="<?php echo e(route('products.category', $product->category->slug)); ?>"><?php echo e($product->category->name); ?></a></span>
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(!empty($inCart)): ?><span class="badge-pill in-cart"><?php echo e(__('In Your Cart')); ?></span><?php endif; ?>
                    <?php endif; ?>
                </div>
                <h1 class="product-title clamp-lines"><?php echo e($product->name); ?></h1>
                <?php if($product->short_description): ?>
                    <p class="product-subtitle clamp-2"><?php echo e($product->short_description); ?></p>
                <?php endif; ?>
                <div class="rating-line" aria-label="Rating <?php echo e(number_format($rating,1)); ?> out of 5">
                    <span class="stars" aria-hidden="true">
                        <?php $__currentLoopData = $stars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="star <?php echo e($s['filled'] ? 'filled' : ''); ?>"><?php echo e($s['filled'] ? '★' : '☆'); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </span>
                    <span class="rating-value"><?php echo e(number_format($rating,1)); ?></span>
                    <a href="#reviews" class="rating-count"><?php echo e($reviewsCount); ?> <?php echo e(__('Ratings')); ?></a>
                </div>
                <div class="price-line product-pricing" data-original-price="1">
                    <?php if($product->type === 'variable'): ?>
                        <?php if($minP !== null): ?>
                            <?php if($minP == $maxP): ?>
                                <span class="price-current"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($minP,2)); ?></span>
                            <?php else: ?>
                                <span class="price-current"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($minP,2)); ?></span>
                                <span class="price-range-sep">-</span>
                                <span class="price-current"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($maxP,2)); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="price-current"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($basePrice,2)); ?></span>
                        <?php if($onSale): ?>
                            <span class="price-original"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($origPrice,2)); ?></span>
                            <?php if($discountPercent): ?><span class="discount-badge"><?php echo e($discountPercent); ?>% <?php echo e(__('Off')); ?></span><?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="interest-count mt-1" aria-label="Interest count">
                    <small class="text-muted"><?php echo e(__('Interested users')); ?>: <?php echo e($interestCount); ?></small>
                </div>
                <div class="badges-row">
                    <?php if($onSale && $discountPercent): ?>
                        <span class="badge-soft badge-sale" id="globalSaleBadge"><?php echo e($discountPercent); ?>% <?php echo e(__('Off')); ?></span>
                    <?php elseif($product->type === 'variable'): ?>
                        <span class="badge-soft badge-sale" id="globalSaleBadge"></span>
                    <?php endif; ?>
                    <span class="badge-soft badge-fast">express</span>
                    <span class="badge-soft badge-stock <?php echo e($stockClass); ?>" id="topStockBadge"><?php echo e($levelLabel); ?></span>
                </div>
                <div class="divider-line"></div>

                <?php if($product->type === 'variable' && count($variationAttributes)): ?>
                <div class="variation-grid-card mt-4" id="variationGridCard" data-used='<?php echo e(e(json_encode($usedAttrs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'>
                    <h3 class="variation-card-title"><?php echo e(__('Choose Options')); ?></h3>
                    <div class="variation-grid">
                        <?php $__currentLoopData = $variationAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="variation-attr-block" data-attr="<?php echo e($attr['name']); ?>">
                            <div class="attr-label"><span class="attr-icon"><?php echo e($attr['icon']); ?></span><?php echo e($attr['label']); ?></div>
                            <div class="attr-options">
                                <?php $__currentLoopData = $attr['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($attr['is_color']): ?>
                                        <div class="color-swatch-wrapper">
                                            <button type="button" class="option-btn color attr-option-btn" aria-label="<?php echo e($v); ?>" title="<?php echo e($v); ?>" data-attr-value="<?php echo e($v); ?>" data-swatch="<?php echo e($v); ?>"></button>
                                            <span class="swatch-label"><?php echo e($v); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <button type="button" class="option-btn attr-option-btn" data-attr-value="<?php echo e($v); ?>"><?php echo e($v); ?></button>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="stock-status">
                    <?php if($available === 0): ?>
                        <?php echo e(__('Out of stock')); ?>

                    <?php elseif(is_null($available)): ?>
                        <?php echo e(__('In stock')); ?>

                    <?php else: ?>
                        <?php echo e($available); ?> <?php echo e(__('in stock')); ?>

                    <?php endif; ?>
                </div>

                <div class="product-meta mt-4" id="productMeta">
                    <?php if($product->sku): ?>
                    <div class="meta-item sku-item">
                        <span class="meta-label"><?php echo e(__('SKU')); ?> :</span>
                        <div class="sku-wrapper">
                            <span class="meta-value" id="skuValue"><?php echo e($product->sku); ?></span>
                            <button type="button" class="btn-copy-sku" id="copySkuBtn" title="<?php echo e(__('Copy SKU')); ?>" aria-label="<?php echo e(__('Copy SKU')); ?>">📋</button>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($tagsCount): ?>
                    <div class="meta-item tags-item">
                        <span class="meta-label"><?php echo e(__('Tags')); ?> :</span>
                        <div class="tags-list" id="tagsListInline">
                            <?php $__currentLoopData = $tagsFirst; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('products.tag', $tag->slug)); ?>" class="tag-chip" title="<?php echo e($tag->name); ?>"><?php echo e($tag->name); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($tagsMore->count()): ?>
                                <button type="button" class="tag-more" id="toggleAllTags" data-more="<?php echo e($tagsMore->count()); ?>" aria-expanded="false">+<?php echo e($tagsMore->count()); ?> <?php echo e(__('more')); ?></button>
                                <div class="tag-more-hidden" id="allTagsHidden">
                                    <?php $__currentLoopData = $tagsMore; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('products.tag', $tag->slug)); ?>" class="tag-chip" title="<?php echo e($tag->name); ?>"><?php echo e($tag->name); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->weight): ?>
                    <div class="meta-item weight-item">
                        <span class="meta-label"><?php echo e(__('Weight')); ?>:</span>
                        <span class="meta-value"><?php echo e($product->weight); ?> <?php echo e(__('kg')); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($hasDims): ?>
                    <div class="meta-item dimensions-item" title="<?php echo e(__('Dimensions')); ?>">
                        <span class="meta-label"><?php echo e(__('Dimensions')); ?>:</span>
                        <span class="meta-value"><?php echo e($product->length ?? '-'); ?> × <?php echo e($product->width ?? '-'); ?> × <?php echo e($product->height ?? '-'); ?> <?php echo e(__('cm')); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($product->refund_days): ?>
                    <div class="meta-item return-item">
                        <span class="meta-label"><?php echo e(__('Returns')); ?>:</span>
                        <span class="meta-value"><?php echo e($product->refund_days); ?> <?php echo e(__('day')); ?> <?php echo e(__('return guarantee')); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="purchase-box sticky">
                <div class="seller">
                    <div class="avatar"></div>
                    <div>
                        <div class="seller-name"><?php echo e(__('Sold by')); ?> <?php echo e($product->seller ? $product->seller->name : 'Store'); ?></div>
                        <div class="seller-score"><?php echo e(number_format($rating,1)); ?> • <small><?php echo e($formattedReviewsCount); ?> <?php echo e(__('ratings')); ?></small></div>
                    </div>
                </div>
                <?php if($onSale): ?>
                    <div class="sale-badge-inline"><?php echo e(__('On Sale')); ?></div>
                <?php endif; ?>
                <ul class="seller-flags">
                    <li><?php echo e(__('No warranty')); ?></li>
                    <li><?php echo e(__('Free delivery on lockers & pickup points')); ?></li>
                    <li><?php echo e(__('Return eligible')); ?>: <strong><?php echo e($product->refund_days ?? 7); ?></strong> <?php echo e(__('days')); ?></li>
                    <li><?php echo e(__('Secure payments')); ?></li>
                </ul>
                <?php if($isOut): ?>
                    <div class="stock-out">
                        <button type="button" class="front-button pill-out" disabled><?php echo e(__('Out of stock')); ?></button>
                        <div class="notify-block">
                            <button type="button" class="front-button pill-notify notify-btn" id="notifyBtn" data-type="back_in_stock" data-product-id="<?php echo e($product->id); ?>" <?php if(auth()->guard()->check()): ?> data-email="<?php echo e(auth()->user()->email); ?>" <?php if(auth()->user() && auth()->user()->phone): ?> data-phone="<?php echo e(auth()->user()->phone); ?>" <?php endif; ?> <?php endif; ?>>
                                <span class="notify-label"><?php echo e(__('Notify me when available')); ?></span>
                                <span class="notify-subscribed d-none"><?php echo e(__('Subscribed')); ?></span>
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="<?php echo e(route('cart.add')); ?>" method="POST" class="add-to-cart-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                        <?php if($product->type === 'variable'): ?>
                            <input type="hidden" name="variation_id" id="selectedVariationId" value="">
                        <?php endif; ?>
                        <input type="hidden" name="buy_now" id="buyNowFlag" value="">
                        <div class="quantity-selector">
                            <input id="qtyInputSide" type="number" name="qty" class="quantity-field hidden-block" value="1" min="1" max="<?php echo e($product->stock_quantity ?: 999); ?>">
                            <div class="qty-pill" role="group" aria-label="Quantity selector">
                                <button type="button" class="qty-action qty-trash" aria-label="Remove item"><span class="icon-trash">🗑</span></button>
                                <div class="qty-display" id="qtyDisplay">1</div>
                                <button type="button" class="qty-action qty-increase" aria-label="Increase quantity">+</button>
                            </div>
                        </div>
                        <button class="btn-buy front-button" type="submit" <?php echo e(($product->stock_quantity ?? 1) == 0 ? 'disabled' : ''); ?>><?php echo e(__('ADD TO CART')); ?></button>
                    </form>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<!-- Product Details Tabs -->
<section class="product-tabs-section">
    <div class="container">
        <div class="product-tabs">
            <div class="tab-nav" role="tablist" aria-label="Product details tabs">
                <button class="tab-btn active" data-tab="description" role="tab" aria-controls="description"
                    id="tab-desc"><?php echo e(__('Description')); ?></button>
                <button class="tab-btn" data-tab="specifications" role="tab" aria-controls="specifications"
                    id="tab-specs"><?php echo e(__('Specifications')); ?> <span
                        class="spec-count-badge"><?php echo e($specCount); ?></span></button>
                <button class="tab-btn" data-tab="reviews" role="tab" aria-controls="reviews"
                    id="tab-reviews"><?php echo e(__('Reviews')); ?> (<?php echo e($reviewsCount); ?>)</button>
            </div>

            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane active" id="description" role="tabpanel" aria-labelledby="tab-desc">
                    <div class="description-content">
                        <?php if($product->description): ?>
                        
                        <?php echo nl2br(e($product->description)); ?>

                        <?php else: ?>
                        <p class="text-muted"><?php echo e(__('No detailed description available for this product.')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane" id="specifications" role="tabpanel" aria-labelledby="tab-specs">
                    <div class="specifications-content">
                        <div class="spec-grid">
                            <?php if($product->sku): ?>
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">#</span><?php echo e(__('SKU')); ?></span><span
                                    class="spec-value"><?php echo e($product->sku); ?></span></div>
                            <?php endif; ?>
                            <?php if($product->weight): ?>
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">⚖️</span><?php echo e(__('Weight')); ?></span><span
                                    class="spec-value"><?php echo e($product->weight); ?> <?php echo e(__('kg')); ?></span></div>
                            <?php endif; ?>
                            <?php if($hasDims): ?>
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">📦</span><?php echo e(__('Dimensions')); ?></span><span
                                    class="spec-value"><?php echo e($product->length ?? '-'); ?> × <?php echo e($product->width ?? '-'); ?> ×
                                    <?php echo e($product->height ?? '-'); ?> <?php echo e(__('cm')); ?></span></div>
                            <?php endif; ?>
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">🗂️</span><?php echo e(__('Category')); ?></span><span
                                    class="spec-value"><?php echo e($product->category->name); ?></span></div>
                            <?php if($product->refund_days): ?>
                            <div class="spec-item"><span class="spec-label"><span
                                        class="spec-icon">↩️</span><?php echo e(__('Returns')); ?></span><span
                                    class="spec-value"><?php echo e($product->refund_days); ?> <?php echo e(__('days')); ?></span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane" id="reviews" role="tabpanel" aria-labelledby="tab-reviews" data-lazy="reviews"
                    data-loaded="1">
                    <?php echo $__env->make('front.products.partials.reviews', ['reviews' => $product->reviews()->where('approved',
                    true)->get(), 'product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
<?php echo $__env->make('front.partials.notify-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($product->type === 'variable'): ?>

<div id="productVariations" data-json='<?php echo e(e(json_encode($product->variations->where("active", true)->values(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'
    class="hidden-block"></div>
<?php endif; ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/products/show.blade.php ENDPATH**/ ?>