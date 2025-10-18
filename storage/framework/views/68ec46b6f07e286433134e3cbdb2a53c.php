<?php $__env->startSection('title', __('Orders').' - '.config('app.name')); ?>
<?php $__env->startSection('content'); ?>

<section class="account-section">
 <div class="container account-grid">
  <?php echo $__env->make('front.account._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <main class="account-main">
   <div class="orders-page">
     <h1 class="page-title"><?php echo e(__('Orders')); ?></h1>
     <div class="order-filters">
       <input type="text" placeholder="<?php echo e(__('Find items')); ?>" disabled>
       <select disabled><option><?php echo e(now()->year); ?></option></select>
     </div>
     <?php if(!$orders->count()): ?>
       <div class="alert alert-info small"><?php echo e(__('No orders yet.')); ?></div>
     <?php else: ?>
       <div class="orders-list">
         <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <div class="order-card">
           <div class="order-status-line">
             <span class="status-dot status-<?php echo e($o->status); ?>"></span>
             <span class="status-text"><?php echo e(ucfirst($o->status)); ?> <small><?php echo e($o->created_at->format('l, j M, H:i A')); ?></small></span>
             <a href="<?php echo e(route('user.orders.show',$o)); ?>" class="btn btn-primary btn-place"><?php echo e(__('View')); ?></a>
           </div>
           <div class="order-summary">
              <div class="thumb-stack">
                <?php $__currentLoopData = $o->items->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="thumb"><?php echo e(strtoupper(substr($it->name,0,1))); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
              <div class="details">
                <div class="title"><?php echo e($ordersFirstSummaries[$o->id] ?? __('Order')); ?></div>
                <div class="meta"><?php echo e(__('Items')); ?>: <?php echo e($o->items->count()); ?> · <?php echo e(number_format($o->total,2)); ?> <?php echo e($o->currency); ?></div>
                <div class="meta"><?php echo e(__('Payment')); ?>: <?php echo e(ucfirst($o->payment_status)); ?></div>
              </div>
              <div class="badges">
                 <span class="badge subtle">#<?php echo e($o->id); ?></span>
              </div>
           </div>
         </div>
         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </div>
       <div class="pagination-wrap"><?php echo e($orders->links()); ?></div>
     <?php endif; ?>
   </div>
  </main>
 </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/account/orders.blade.php ENDPATH**/ ?>