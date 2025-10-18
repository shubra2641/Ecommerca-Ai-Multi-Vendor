<?php $__env->startSection('title', __('Compare Products')); ?>
<?php $__env->startSection('content'); ?>
<section class="products-section">
 <div class="container container-wide">
  <h1 class="results-title"><?php echo e(__('Compare')); ?></h1>
   <?php if(($items ?? collect())->isEmpty()): ?>
     <?php $__env->startComponent('front.components.empty-state', [
        'title' => __('No products in compare list.'),
        'actionLabel' => __('Browse Products'),
        'actionUrl' => route('products.index')
     ]); ?><?php echo $__env->renderComponent(); ?>
  <?php else: ?>
    <div class="table-scroll">
     <table class="compare-table">
    <thead>
     <tr>
            <th class="compare-th"><?php echo e(__('Feature')); ?></th>
      <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <th class="compare-th">
                <a href="<?php echo e(route('products.show',$p->slug)); ?>" class="compare-link"><?php echo e($p->name); ?></a>
                <form action="<?php echo e(route('compare.toggle')); ?>" method="POST" class="compare-remove-form">
         <?php echo csrf_field(); ?>
         <input type="hidden" name="product_id" value="<?php echo e($p->id); ?>">
                 <button class="btn-remove">×</button>
                </form>
             </th>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     </tr>
    </thead>
        <tbody class="compare-tbody">
     <tr>
            <td class="compare-td feature-td"><?php echo e(__('Image')); ?></td>
      <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <td class="compare-td">
                <?php if($p->main_image): ?>
                    <img src="<?php echo e(asset($p->main_image)); ?>" alt="<?php echo e($p->name); ?>" class="compare-img">
                <?php else: ?>
                    <span class="compare-no-image">No Image</span>
                <?php endif; ?>
             </td>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     </tr>
     <tr>
            <td class="compare-td feature-td"><?php echo e(__('Category')); ?></td>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="compare-td"><?php echo e($p->category->name ?? '-'); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     </tr>
       <tr>
        <td class="compare-td feature-td"><?php echo e(__('Brand')); ?></td>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="compare-td"><?php echo e($p->brand->name ?? '-'); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </tr>
    <tr>
       <td class="compare-td feature-td"><?php echo e(__('Price')); ?></td>
       <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="compare-td"><?php echo e($currency_symbol ?? '$'); ?> <?php echo e(number_format($p->display_price ?? $p->effectivePrice(),0)); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
    <tr>
       <td class="compare-td feature-td">SKU</td>
       <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="compare-td"><?php echo e($p->sku ?? '-'); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
    <tr>
       <td class="compare-td feature-td"><?php echo e(__('Stock')); ?></td>
       <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="compare-td"><?php echo e($p->availableStock() ?? '∞'); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
    <tr>
       <td class="compare-td feature-td"><?php echo e(__('Weight')); ?></td>
       <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="compare-td"><?php echo e($p->weight ?? '-'); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
       <tr>
        <td class="compare-td feature-td"><?php echo e(__('Featured')); ?></td>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="compare-td"><?php echo e($p->is_featured? __('Yes'):__('No')); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </tr>
    </tbody>
   </table>
  </div>
  <?php endif; ?>
 </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/products/compare.blade.php ENDPATH**/ ?>