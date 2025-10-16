<?php $__env->startSection('title', __('Payment Gateway Management')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('Payment Gateway Management')); ?></h1>
            <p class="mb-0 text-muted"><?php echo e(__('Monitor and manage your payment gateways')); ?></p>
        </div>
        <div class="btn-group ms-md-3">
            <button type="button" class="btn btn-primary" data-action="sync-gateways">
                <i class="fas fa-sync-alt me-2"></i>
                <span class="d-none d-sm-inline"><?php echo e(__('Sync Gateways')); ?></span>
                <span class="d-inline d-sm-none"><?php echo e(__('Sync')); ?></span>
            </button>
            <a href="<?php echo e(route('admin.payment-gateways.index')); ?>" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-list"></i>
                <span class="d-none d-sm-inline"><?php echo e(__('Gateway List')); ?></span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <?php echo e(__('Total Gateways')); ?>

                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_gateways']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <?php echo e(__('Active Gateways')); ?>

                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['enabled_gateways']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <?php echo e(__('Transactions (30d)')); ?>

                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e(number_format($stats['total_transactions'])); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <?php echo e(__('Revenue (30d)')); ?>

                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo e(number_format($stats['total_revenue'], 2)); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gateway Performance -->
    <div class="row mb-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Gateway Performance')); ?></h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header"><?php echo e(__('Actions')); ?>:</div>
                            <a class="dropdown-item" href="#"
                                data-action="refresh-performance-data"><?php echo e(__('Refresh Data')); ?></a>
                            <a class="dropdown-item" href="#"
                                data-action="export-performance-report"><?php echo e(__('Export Report')); ?></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="performanceTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('Gateway')); ?></th>
                                    <th><?php echo e(__('Success Rate')); ?></th>
                                    <th><?php echo e(__('Transactions')); ?></th>
                                    <th class="d-none d-md-table-cell"><?php echo e(__('Avg Response Time')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $performanceMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($metric['gateway']); ?></td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar <?php echo e($metric['success_rate'] >= 95 ? 'bg-success' : ($metric['success_rate'] >= 80 ? 'bg-warning' : 'bg-danger')); ?>"
                                                role="progressbar" data-progress="<?php echo e($metric['success_rate']); ?>"
                                                aria-valuenow="<?php echo e($metric['success_rate']); ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <?php echo e($metric['success_rate']); ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo e(number_format($metric['total_transactions'])); ?></td>
                                    <td class="d-none d-md-table-cell"><?php echo e($metric['avg_response_time']); ?>ms</td>
                                    <td>
                                        <?php if($metric['success_rate'] >= 95): ?>
                                        <span class="badge badge-success"><?php echo e(__('Excellent')); ?></span>
                                        <?php elseif($metric['success_rate'] >= 80): ?>
                                        <span class="badge badge-warning"><?php echo e(__('Good')); ?></span>
                                        <?php else: ?>
                                        <span class="badge badge-danger"><?php echo e(__('Poor')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-action="test-gateway"
                                            data-gateway="<?php echo e($metric['gateway']); ?>">
                                            <i class="fas fa-vial"></i> <?php echo e(__('Test')); ?>

                                        </button>
                                        <button class="btn btn-sm btn-outline-info" data-action="view-analytics"
                                            data-gateway="<?php echo e($metric['gateway']); ?>">
                                            <i class="fas fa-chart-line"></i> <?php echo e(__('Analytics')); ?>

                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <!-- Gateway Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Gateway Status')); ?></h6>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <?php if($gateway->enabled): ?>
                            <div class="icon-circle bg-success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <?php else: ?>
                            <div class="icon-circle bg-secondary">
                                <i class="fas fa-times text-white"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-gray-500"><?php echo e($gateway->name); ?></div>
                            <div class="font-weight-bold">
                                <?php if($gateway->enabled): ?>
                                <?php echo e(__('Active')); ?>

                                <?php else: ?>
                                <?php echo e(__('Inactive')); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" data-action="toggle-gateway"
                                data-id="<?php echo e($gateway->id); ?>">
                                <?php if($gateway->enabled): ?>
                                <i class="fas fa-pause"></i>
                                <?php else: ?>
                                <i class="fas fa-play"></i>
                                <?php endif; ?>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Quick Actions')); ?></h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action" data-action="test-all-gateways">
                            <i class="fas fa-vial text-primary"></i>
                            <?php echo e(__('Test All Gateways')); ?>

                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-action="generate-report">
                            <i class="fas fa-file-alt text-info"></i>
                            <?php echo e(__('Generate Report')); ?>

                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-action="view-logs">
                            <i class="fas fa-list text-warning"></i>
                            <?php echo e(__('View Logs')); ?>

                        </a>
                        <a href="<?php echo e(route('admin.payment-gateways.create')); ?>"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-plus text-success"></i>
                            <?php echo e(__('Add Gateway')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Recent Transactions')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="transactionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('ID')); ?></th>
                            <th><?php echo e(__('Gateway')); ?></th>
                            <th><?php echo e(__('Amount')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                            <th class="d-none d-md-table-cell"><?php echo e(__('Customer')); ?></th>
                            <th><?php echo e(__('Date')); ?></th>
                            <th><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>#<?php echo e($transaction->id); ?></td>
                            <td><?php echo e($transaction->paymentGateway->name ?? 'N/A'); ?></td>
                            <td>$<?php echo e(number_format($transaction->amount, 2)); ?></td>
                            <td>
                                <?php switch($transaction->status):
                                case ('completed'): ?>
                                <span class="badge badge-success"><?php echo e(__('Completed')); ?></span>
                                <?php break; ?>
                                <?php case ('pending'): ?>
                                <span class="badge badge-warning"><?php echo e(__('Pending')); ?></span>
                                <?php break; ?>
                                <?php case ('failed'): ?>
                                <span class="badge badge-danger"><?php echo e(__('Failed')); ?></span>
                                <?php break; ?>
                                <?php default: ?>
                                <span class="badge badge-secondary"><?php echo e(ucfirst($transaction->status)); ?></span>
                                <?php endswitch; ?>
                            </td>
                            <td class="d-none d-md-table-cell"><?php echo e($transaction->order->user->name ?? 'Guest'); ?></td>
                            <td><?php echo e($transaction->created_at->format('M d, Y H:i')); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-action="view-transaction"
                                    data-id="<?php echo e($transaction->id); ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Test Gateway Modal -->
<div class="modal fade" id="testGatewayModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('Test Gateway Connection')); ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testGatewayForm">
                    <div class="form-group">
                        <label><?php echo e(__('Test Amount')); ?></label>
                        <input type="number" id="testAmount" name="amount" class="form-control" value="1.00"
                            step="0.01" />
                    </div>
                </form>
                <div id="testResults" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                <button type="button" class="btn btn-primary"
                    data-action="run-gateway-test"><?php echo e(__('Run Test')); ?></button>
            </div>
        </div>
    </div>
</div>


<div id="pgMgmtRoot" class="d-none" data-gateways='<?php echo json_encode($gateways->pluck("id", "name"), 512) ?>'
    data-sync-url='<?php echo e(route("admin.payment-gateways-management.sync")); ?>'
    data-test-base='<?php echo e(url("admin/payment-gateways-management")); ?>'
    data-toggle-base='<?php echo e(route("admin.payment-gateways.index")); ?>'
    data-translate-testing='<?php echo e(addslashes(__('Testing connection...'))); ?>'
    data-translate-test-success='<?php echo e(addslashes(__('Test Successful'))); ?>'
    data-translate-test-failed='<?php echo e(addslashes(__('Test Failed'))); ?>'
    data-translate-gateway-not-found='<?php echo e(addslashes(__('Gateway not found'))); ?>'
    data-translate-sync-failed='<?php echo e(addslashes(__('Failed to sync gateways'))); ?>'></div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/payment_gateways/dashboard.blade.php ENDPATH**/ ?>