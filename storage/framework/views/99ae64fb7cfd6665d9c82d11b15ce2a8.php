<!-- Reviews Section: two-column layout (main reviews + side form) -->
<div class="reviews-section enhanced">
    <div class="reviews-main">
        <?php if($reviews->count() > 0): ?>
        <!-- Reviews List -->
        <div class="reviews-list">
            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="review-item">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">
                            <?php if($review->user->avatar): ?>
                            <img src="<?php echo e(asset($review->user->avatar)); ?>" alt="<?php echo e($review->user->name); ?>">
                            <?php else: ?>
                            <div class="avatar-placeholder"><?php echo e(strtoupper(substr($review->user->name, 0, 1))); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="reviewer-details">
                            <h4 class="reviewer-name"><?php echo e($review->user->name); ?></h4>
                            <div class="review-meta">
                                <div class="review-rating">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star star <?php echo e($i <= $review->rating ? 'filled' : ''); ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="review-date"><?php echo e($review->created_at->format('M d, Y')); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php if($review->verified_purchase): ?>
                    <div class="verified-badge">
                        <i class="fas fa-shield-check"></i>
                        Verified Purchase
                    </div>
                    <?php endif; ?>
                </div>

                <div class="review-content">
                    <p class="review-text"><?php echo e($review->comment); ?></p>

                    <?php if($review->images && count($review->images) > 0): ?>
                    <div class="review-images">
                        <?php $__currentLoopData = $review->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="review-image">
                            <img src="<?php echo e(asset($image)); ?>" alt="Review image" loading="lazy">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if($review->helpful_count > 0): ?>
                <div class="review-actions">
                    <div class="helpful-count">
                        <i class="fas fa-thumbs-up"></i>
                        <?php echo e($review->helpful_count); ?> found this helpful
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Reviews Pagination -->
        <?php if($reviews instanceof \Illuminate\Pagination\LengthAwarePaginator && $reviews->hasPages()): ?>
        <div class="reviews-pagination"><?php echo e($reviews->links()); ?></div>
        <?php endif; ?>
        <?php else: ?>
        <div class="no-reviews fancy-empty">
            <div class="no-reviews-icon">
                <i class="fas fa-star"></i>
            </div>
            <h3><?php echo e(__('No Reviews Yet')); ?></h3>
            <p><?php echo e(__('Be the first to review this product and help others make informed decisions.')); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <aside class="reviews-side">
        <?php if(auth()->guard()->check()): ?>
        <?php if($reviewCanSubmit): ?>
        <div class="write-review-section card-surface">
            <h3 class="write-title"><?php echo e(__('Write a Review')); ?></h3>
            <form class="review-form" id="reviewForm" action="<?php echo e(route('reviews.store', $product->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-label"><?php echo e(__('Your Rating')); ?></label>
                    <div class="rating-input" id="ratingInput">
                        <?php for($i = 1; $i <= 5; $i++): ?> <button type="button" class="star-btn" data-rating="<?php echo e($i); ?>"
                            aria-label="<?php echo e(__('Rate :n star(s)', ['n'=>$i])); ?>">
                            <i class="fas fa-star star"></i>
                            </button>
                            <?php endfor; ?>
                            <input type="hidden" name="rating" id="ratingValue" required>
                    </div>
                    <fieldset class="rating-fallback">
                        <legend><?php echo e(__('Choose rating (fallback)')); ?></legend>
                        <div class="stars-inline">
                            <?php for($i=1;$i<=5;$i++): ?> <input type="radio" id="rf<?php echo e($i); ?>" name="rating_fallback"
                                value="<?php echo e($i); ?>">
                                <label for="rf<?php echo e($i); ?>"><?php echo e($i); ?></label>
                                <?php endfor; ?>
                        </div>
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="reviewComment" class="form-label"><?php echo e(__('Your Review')); ?></label>
                    <textarea class="form-control" id="reviewComment" name="comment" rows="4"
                        placeholder="<?php echo e(__('Share your experience with this product...')); ?>" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane btn-icon"></i>
                        <?php echo e(__('Submit Review')); ?>

                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="buyer-only-info card-surface">
            <h3><?php echo e(__('Reviews are for verified buyers only')); ?></h3>
            <p><?php echo e(__('You can submit a review after your purchase is completed.')); ?></p>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="login-prompt card-surface">
            <div class="login-prompt-content">
                <h3><?php echo e(__('Want to Write a Review?')); ?></h3>
                <p><?php echo e(__('Please log in to share your experience with this product.')); ?></p>
                <div class="login-actions">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary"><?php echo e(__('Log In')); ?></a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-outline"><?php echo e(__('Sign Up')); ?></a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </aside>
</div><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/products/partials/reviews.blade.php ENDPATH**/ ?>