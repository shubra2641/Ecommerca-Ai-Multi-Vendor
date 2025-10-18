<?php $__env->startSection('title', __('My Wishlist')); ?>
<?php $__env->startSection('content'); ?>
<section class="products-section">
 <div class="container container-wide">
  <h1 class="results-title"><?php echo e(__('My Wishlist')); ?></h1>
  <?php if(($items ?? collect())->isEmpty()): ?>
    <?php $__env->startComponent('front.components.empty-state', [
        'title' => __('No wishlist items yet.'),
        'actionLabel' => __('Browse Products'),
        'actionUrl' => route('products.index')
    ]); ?><?php echo $__env->renderComponent(); ?>
  <?php else: ?>
   <div class="products-grid">
  <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('front.products.partials.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds ?? [], 'compareIds' => $compareIds ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
   </div>
  <?php endif; ?>
 </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/products/wishlist.blade.php ENDPATH**/ ?>