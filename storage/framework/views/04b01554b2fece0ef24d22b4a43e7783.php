<?php $__env->startSection('title', __('Performance Dashboard')); ?>
<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold"><?php echo e(__('Performance Dashboard')); ?></h1>
        <button id="refreshBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"><?php echo e(__('Refresh')); ?></button>
    </div>
    <div id="perfGrid" class="row gx-3 gy-3">
        <?php $__currentLoopData = $snapshot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card modern-card stats-card stats-card-primary h-100">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-number font-mono" data-metric="<?php echo e($metric); ?>" data-field="sum"><?php echo e($row['sum']); ?></div>
                            <div class="stats-label text-sm"><?php echo e(str_replace('_',' ', $metric)); ?></div>
                            <div class="stats-trend">
                                <i class="fas fa-tachometer-alt text-primary"></i>
                                <span class="text-primary"><?php echo e(__('Performance metric')); ?></span>
                            </div>
                        </div>
                        <div class="stats-icon"><i class="fas fa-tachometer-alt"></i></div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <p class="text-[11px] text-gray-400 mt-6"><?php echo e(__('Window')); ?>: <?php echo e(config('performance.snapshot_window')); ?> <?php echo e(__('minutes (rolling)')); ?></p>
    <div id="adminPerformanceConfig" data-perf-url="<?php echo e(route('admin.performance.snapshot')); ?>" hidden></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/performance/index.blade.php ENDPATH**/ ?>