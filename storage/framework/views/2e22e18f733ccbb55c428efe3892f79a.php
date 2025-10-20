<?php $__env->startSection('title', __('Cart').' - '.config('app.name')); ?>
<?php $__env->startSection('content'); ?>
<section class="products-section cart-section">
    <div class="container cart-container">
        <section class="cart-inner">
            <div class="panel-card">
                <?php if(!count($items)): ?>
                <div class="empty-cart">
                    <p class="text-muted"><?php echo e(__('Your cart is empty.')); ?></p>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary"><?php echo e(__('Shop Now')); ?></a>
                </div>
                <?php else: ?>
                <div class="checkout-row">
                    <div class="cart-items-col">
                        <h2 class="panel-title">Cart (<?php echo e(count($items)); ?> item<?php echo e(count($items)>1?'s':''); ?>)</h2>

                        <?php $__currentLoopData = ($cartItemsPrepared ?? $items); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cart-item">
                            <div class="cart-thumb">
                                <a href="<?php echo e(route('products.show',$it['product']->slug)); ?>">
                                    <?php if($it['product']->main_image): ?>
                                    <img src="<?php echo e(asset($it['product']->main_image)); ?>" alt="<?php echo e($it['product']->name); ?>">
                                    <?php else: ?>
                                    <div class="no-image">No Image</div>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="meta">
                                <a href="<?php echo e(route('products.show',$it['product']->slug)); ?>" class="name"><?php echo e($it['product']->name); ?></a>
                                <?php if(!empty($it['variant_label'])): ?>
                                <div class="small-muted"><?php echo e($it['variant_label']); ?></div>
                                <?php endif; ?>
                                <?php if($it['product']->short_description): ?>
                                <div class="desc"><?php echo e(Str::limit($it['product']->short_description,120)); ?></div>
                                <?php endif; ?>

                                <div class="row">
                                    <?php if(method_exists($it['product'],'seller') && $it['product']->seller): ?>
                                    <div class="seller-note">Sold by <strong><?php echo e($it['product']->seller->name); ?></strong></div>
                                    <?php endif; ?>
                                    <?php if(($it['product']->stock_qty ?? null) !== null): ?>
                                    <div class="stock">
                                        <?php echo e(($it['product']->stock_qty ?? 0) > 0 ? ($it['product']->stock_qty.' in stock') : 'Out of stock'); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="cart-actions">
                                    
                                    <form action="<?php echo e(route('cart.update')); ?>" method="post" class="qty-form"><?php echo csrf_field(); ?>
                                        <input type="hidden" name="lines[<?php echo e($loop->index); ?>][cart_key]" value="<?php echo e($it['cart_key']); ?>">
                                        <label for="qty-input-<?php echo e($loop->index); ?>" class="qty-label">Qty</label>
                                        <div class="qty-input-group">
                                            <?php if($it['qty'] > 1): ?>
                                            <button type="submit" name="lines[<?php echo e($loop->index); ?>][qty]" value="<?php echo e($it['qty'] - 1); ?>" class="qty-btn qty-decrease" aria-label="Decrease quantity">−</button>
                                            <?php else: ?>
                                            <button type="button" class="qty-btn qty-decrease" disabled aria-label="Cannot decrease below 1">−</button>
                                            <?php endif; ?>
                                            <input id="qty-input-<?php echo e($loop->index); ?>" name="lines[<?php echo e($loop->index); ?>][qty]" type="number" min="1" <?php if(!is_null($it['available'])): ?> max="<?php echo e($it['available']); ?>" <?php endif; ?> value="<?php echo e($it['qty']); ?>" class="qty-input" />
                                            <?php if($it['qty'] < ($it['available'] ?? 999)): ?>
                                            <button type="submit" name="lines[<?php echo e($loop->index); ?>][qty]" value="<?php echo e($it['qty'] + 1); ?>" class="qty-btn qty-increase" aria-label="Increase quantity">+</button>
                                            <?php else: ?>
                                            <button type="button" class="qty-btn qty-increase" disabled aria-label="Cannot increase above available stock">+</button>
                                            <?php endif; ?>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-outline">Update</button>
                                    </form>
                                    
                                    
                                    <form action="<?php echo e(route('cart.remove')); ?>" method="post" class="remove-form"><?php echo csrf_field(); ?>
                                        <input type="hidden" name="cart_key" value="<?php echo e($it['cart_key']); ?>">
                                        <button type="submit" class="circle-btn icon-btn remove-btn" aria-label="Remove item">
                                            <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 3v1H4v2h16V4h-5V3H9zm1 5v9h2V8H10zm4 0v9h2V8h-2zM7 8v9h2V8H7z" fill="currentColor"/></svg>
                                        </button>
                                    </form>
                                    
                                    <span class="line-price"><?php echo e($currency_symbol ?? '$'); ?><?php echo e(number_format($it['display_price'],2)); ?></span>
                                    <?php if(!empty($it['on_sale'])): ?>
                                        <div class="sale-flag"><?php echo e($it['sale_percent']); ?>% OFF</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="cart-price">
                                    <div data-cart-line-price>
                                        <?php echo e($currency_symbol ?? '$'); ?>

                                        <?php echo e(number_format($it['display_price'] ?? ($price ?? 0),2)); ?>

                                    </div>
                                    <?php if(!empty($it['cart_on_sale'])): ?>
                                    <div class="original-price">
                                        <?php echo e($currency_symbol ?? '$'); ?>

                                        <?php echo e(number_format($it['display_line_total'] ? ($it['product']->price) : ($it['product']->price),2)); ?>

                                    </div>
                                    <div class="sale-badge">
                                        <?php echo e($it['cart_sale_percent']); ?>% OFF
                                    </div>
                                    <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <aside class="checkout-right">
                        <div class="summary-box panel-card">
                            <h3>Order Summary</h3>
                            <?php if(isset($coupon) && $coupon): ?>
                            <div class="coupon-applied">
                                <div class="row">
                                    <div>
                                        <strong><?php echo e($coupon->code); ?></strong>
                                        <div class="applied-label">Applied</div>
                                    </div>
                                    <form action="<?php echo e(route('cart.removeCoupon')); ?>" method="post" class="m-0"><?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-secondary">Remove</button>
                                    </form>
                                </div>
                            </div>
                            <?php else: ?>
                            <form action="<?php echo e(route('cart.applyCoupon') ?? '#'); ?>" method="post" data-coupon-form>
                                <?php echo csrf_field(); ?>
                                <div class="coupon-form-row">
                                    <input type="text" name="coupon" placeholder="Coupon Code">
                                    <button class="btn btn-primary" type="submit">APPLY</button>
                                </div>
                            </form>
                            <?php endif; ?>

                            <div class="summary-break">
                                <div class="line subtotal">
                                    Subtotal (<?php echo e(count($items)); ?> item): <span
                                        class="subtotal-amount"><?php echo e($currency_symbol ?? '$'); ?>

                                        <?php echo e(number_format($displayTotal ?? $total,2)); ?></span>
                                </div>
                                <div class="line discount">
                                    Discount:
                                    <span class="discount-amount coupon-discount-value">
                                        <?php if($displayDiscount > 0): ?>
                                        - <?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($displayDiscount,2)); ?>

                                        <?php else: ?>
                                        <?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format(0,2)); ?>

                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="line shipping">Shipping Fee: <span>Calculated at checkout</span></div>
                                <div class="line total">Total: <span class="total-amount"><?php echo e($currency_symbol ?? '$'); ?>

                                        <?php echo e(number_format(($discounted_total ?? false) ? (($displayTotal ?? $total ?? 0) - ($discount ?? 0)) : ($displayTotal ?? $total ?? 0),2)); ?></span>
                                </div>
                            </div>

                            <a href="<?php echo e(route('checkout.form')); ?>" class="btn btn-primary w-100">CHECKOUT</a>
                        </div>
                    </aside>

                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/cart/index.blade.php ENDPATH**/ ?>