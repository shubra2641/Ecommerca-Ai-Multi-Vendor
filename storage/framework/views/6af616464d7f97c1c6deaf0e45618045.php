
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header mb-3">
        <div class="page-header-content">
            <h1 class="page-title"><?php echo e(__('Inventory Report')); ?></h1>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card modern-card stats-card p-3 h-100">
                <div class="stats-card-body d-flex align-items-center">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($totals['total_products'] ?? 0)); ?>"><?php echo e($totals['total_products']); ?></div>
                        <div class="stats-label"><?php echo e(__('Total Products')); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card modern-card stats-card p-3 h-100">
                <div class="stats-card-body d-flex align-items-center">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($totals['manage_stock_count'] ?? 0)); ?>"><?php echo e($totals['manage_stock_count']); ?></div>
                        <div class="stats-label"><?php echo e(__('Manage Stock')); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card modern-card stats-card p-3 h-100">
                <div class="stats-card-body d-flex align-items-center">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($totals['out_of_stock'] ?? 0)); ?>"><?php echo e($totals['out_of_stock']); ?></div>
                        <div class="stats-label"><?php echo e(__('Out of Stock')); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card modern-card stats-card p-3 h-100">
                <div class="stats-card-body d-flex align-items-center">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="<?php echo e((int)($totals['serials_low'] ?? 0)); ?>"><?php echo e($totals['serials_low']); ?></div>
                        <div class="stats-label"><?php echo e(__('Serials low (<=5)')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo e(__('ID')); ?></th>
                <th><?php echo e(__('SKU')); ?></th>
                <th><?php echo e(__('Name')); ?></th>
                <th><?php echo e(__('Manage Stock')); ?></th>
                <th><?php echo e(__('Available Stock')); ?></th>
                <th><?php echo e(__('Has Serials')); ?></th>
                <th><?php echo e(__('Unsold Serials')); ?></th>
                <th><?php echo e(__('Variations')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($p['id']); ?></td>
                <td><?php echo e($p['sku']); ?></td>
                <td><?php echo e($p['name']); ?></td>
                <td><?php echo e($p['manage_stock'] ? __('Yes') : __('No')); ?></td>
                <td><?php echo e($p['available_stock'] === null ? __('Unlimited') : $p['available_stock']); ?></td>
                <td><?php echo e($p['has_serials'] ? __('Yes') : __('No')); ?></td>
                <td><?php echo e($p['unsold_serials']); ?></td>
                <td>
                    <?php if(!empty($p['variations']) && $p['variations']->count() > 0): ?>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#vars-<?php echo e($p['id']); ?>"><?php echo e($p['variations']->count()); ?> <?php echo e(__('Variations')); ?></button>
                        <div class="collapse mt-2" id="vars-<?php echo e($p['id']); ?>">
                            <table class="table table-sm table-bordered mb-0">
                                <thead><tr><th><?php echo e(__('SKU')); ?></th><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Manage Stock')); ?></th><th><?php echo e(__('Available')); ?></th></tr></thead>
                                <tbody>
                                    <?php $__currentLoopData = $p['variations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($v['sku']); ?></td>
                                        <td><?php echo e(e($v['name'])); ?></td>
                                        <td><?php echo e($v['manage_stock'] ? __('Yes') : __('No')); ?></td>
                                        <td><?php echo e($v['available_stock'] === null ? __('Unlimited') : $v['available_stock']); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/reports/inventory.blade.php ENDPATH**/ ?>