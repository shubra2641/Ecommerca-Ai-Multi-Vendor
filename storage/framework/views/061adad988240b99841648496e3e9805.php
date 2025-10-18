<?php $__env->startSection('title', __('Invoices').' - '.config('app.name')); ?>
<?php $__env->startSection('content'); ?>

<section class="account-section">
 <div class="container account-grid">
  <?php echo $__env->make('front.account._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <main class="account-main">
    <div class="invoices-wrapper">
      <h3><?php echo e(__('Invoices / Payments')); ?></h3>
      <?php if(!$payments->count()): ?>
        <div class="alert alert-info small"><?php echo e(__('No payments yet.')); ?></div>
      <?php else: ?>
      <div class="invoices-table-wrapper">
        <table class="invoices-table">
          <thead><tr><th>#</th><th><?php echo e(__('Order')); ?></th><th><?php echo e(__('Amount')); ?></th><th><?php echo e(__('Status')); ?></th><th><?php echo e(__('Method')); ?></th></tr></thead>
          <tbody>
            <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($p->id); ?></td>
              <td><a href="<?php echo e(route('user.orders.show',$p->order_id)); ?>">#<?php echo e($p->order_id); ?></a></td>
              <td><?php echo e(number_format($p->amount,2)); ?> <?php echo e($p->currency); ?></td>
              <td><?php echo e(ucfirst($p->status)); ?></td>
              <td><?php echo e($p->method); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <?php echo e($payments->links()); ?>

      <?php endif; ?>
    </div>
  </main>
 </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/account/invoices.blade.php ENDPATH**/ ?>