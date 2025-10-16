

<?php $__env->startSection('title', __('Financial Report')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1"><?php echo e(__('Financial Report')); ?></h1>
            <p class="text-muted mb-0"><?php echo e(__('Financial analysis and balance statistics')); ?></p>
        </div>
        <div class="page-actions">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary js-refresh-page" data-action="refresh" title="<?php echo e(__('Refresh')); ?>">
                    <i class="fas fa-sync-alt" aria-hidden="true"></i> <?php echo e(__('Refresh')); ?>

                </button>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" title="<?php echo e(__('Export')); ?>">
                        <i class="fas fa-download" aria-hidden="true"></i> <?php echo e(__('Export')); ?>

                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="financial" title="<?php echo e(__('Excel')); ?>"><?php echo e(__('Excel')); ?></a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="financial" title="<?php echo e(__('PDF')); ?>"><?php echo e(__('PDF')); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-total-balance" data-countup data-decimals="2" data-target="<?php echo e(number_format($financialData['totalBalance'], 2, '.', '')); ?>">$<?php echo e(number_format($financialData['totalBalance'], 2)); ?></div>
                        <div class="stats-label"><?php echo e(__('Total Balance')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-primary h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-vendor-balance" data-countup data-decimals="2" data-target="<?php echo e(number_format($financialData['vendorBalance'], 2, '.', '')); ?>">$<?php echo e(number_format($financialData['vendorBalance'], 2)); ?></div>
                        <div class="stats-label"><?php echo e(__('Vendor Balance')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-store"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-customer-balance" data-countup data-decimals="2" data-target="<?php echo e(number_format($financialData['customerBalance'], 2, '.', '')); ?>">$<?php echo e(number_format($financialData['customerBalance'], 2)); ?></div>
                        <div class="stats-label"><?php echo e(__('Customer Balance')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-average-balance" data-countup data-decimals="2" data-target="<?php echo e(number_format($financialData['averageBalance'], 2, '.', '')); ?>">$<?php echo e(number_format($financialData['averageBalance'], 2)); ?></div>
                        <div class="stats-label"><?php echo e(__('Average Balance')); ?></div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Statistics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Balance Statistics')); ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo e(__('Maximum Balance')); ?>:</strong></td>
                                    <td class="text-success">$<?php echo e(number_format($financialData['maxBalance'], 2)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Minimum Balance')); ?>:</strong></td>
                                    <td class="text-danger">$<?php echo e(number_format($financialData['minBalance'], 2)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Average Balance')); ?>:</strong></td>
                                    <td class="text-info">$<?php echo e(number_format($financialData['averageBalance'], 2)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo e(__('Total Balance')); ?>:</strong></td>
                                    <td class="text-primary">$<?php echo e(number_format($financialData['totalBalance'], 2)); ?>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Balance Distribution')); ?></h6>
                </div>
                <div class="card-body">
                    <?php if(isset($financialData['balanceDistribution']) && count($financialData['balanceDistribution']) >
                    0): ?>
                    <div class="chart-container h-380">
                        <canvas id="balanceDistributionChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-pie fa-3x text-gray-300 mb-3" aria-hidden="true"></i>
                        <p class="text-muted"><?php echo e(__('No distribution data available')); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <?php if(isset($financialData['monthlyTrends']) && count($financialData['monthlyTrends']) > 0): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Monthly Financial Trends')); ?></h6>
        </div>
        <div class="card-body">
            <div class="chart-container h-400">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Financial Summary Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Financial Summary')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Category')); ?></th>
                            <th><?php echo e(__('Total Balance')); ?></th>
                            <th><?php echo e(__('Average Balance')); ?></th>
                            <th><?php echo e(__('Count')); ?></th>
                            <th><?php echo e(__('Percentage')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?php echo e(__('Vendors')); ?></strong></td>
                            <td class="text-success">$<?php echo e(number_format($financialData['vendorBalance'], 2)); ?></td>
                            <td>$<?php echo e($financialData['totalBalance'] > 0 ? number_format($financialData['vendorBalance'] / max(1, $financialData['totalBalance']) * 100, 1) : '0'); ?>%
                            </td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td><strong><?php echo e(__('Customers')); ?></strong></td>
                            <td class="text-info">$<?php echo e(number_format($financialData['customerBalance'], 2)); ?></td>
                            <td>$<?php echo e($financialData['totalBalance'] > 0 ? number_format($financialData['customerBalance'] / max(1, $financialData['totalBalance']) * 100, 1) : '0'); ?>%
                            </td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <tr class="table-active">
                            <td><strong><?php echo e(__('Total')); ?></strong></td>
                            <td class="text-primary">
                                <strong>$<?php echo e(number_format($financialData['totalBalance'], 2)); ?></strong></td>
                            <td>100%</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(!empty($financialData)): ?>
<script id="report-financial-data" type="application/json"><?php echo json_encode([
    'charts'=>[
        'balanceDistribution'=> isset($financialData['balanceDistribution']) && count($financialData['balanceDistribution']) ? [
                'labels'=>array_keys($financialData['balanceDistribution']),
                'values'=>array_values($financialData['balanceDistribution'])
        ]: null,
        'monthlyTrends'=> isset($financialData['monthlyTrends']) && count($financialData['monthlyTrends']) ? [
                'labels'=>array_keys($financialData['monthlyTrends']),
                'values'=>array_values($financialData['monthlyTrends']),
                'label'=>__('Monthly Financial Trends')
        ]: null,
    ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?></script>
<?php endif; ?>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/reports/financial.blade.php ENDPATH**/ ?>