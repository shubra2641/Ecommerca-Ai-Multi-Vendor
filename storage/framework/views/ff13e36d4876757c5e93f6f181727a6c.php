<?php $__env->startSection('title', __('User Balance')); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php echo $__env->make('admin.partials.page-header', [
        'title' => __('User Balance Management'),
        'subtitle' => __('Manage balance for') . ' ' . $user->name,
        'actions' => '<a href="'.route('admin.users.show', $user).'" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> '.e(__('Back to User')).'</a> <button type="button" class="btn btn-outline-info btn-refresh-balance"><i class="fas fa-sync me-1"></i> '.e(__('Refresh')).'</button> <button type="button" class="btn btn-primary btn-view-history"><i class="fas fa-history me-1"></i> '.e(__('View History')).'</button>'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row" data-user-id="<?php echo e($user->id); ?>">
        <div class="col-lg-8">
            <div class="card modern-card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1"><?php echo e(__('Current Balance')); ?></h5>
                            <div class="h2 mb-0 stats-number" data-countup data-target="<?php echo e($user->balance ?? 0); ?>"><?php echo e(number_format($user->balance ?? 0, 2)); ?> <?php echo e($defaultCurrency ? $defaultCurrency->symbol : 'USD'); ?></div>
                            <div class="text-muted small"><?php echo e(__('As of')); ?> <?php echo e($user->updated_at->format('M d, Y')); ?></div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block">
                                <button type="button" class="btn btn-outline-secondary btn-refresh-balance me-2"><i class="fas fa-sync me-1"></i> <?php echo e(__('Refresh')); ?></button>
                                <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-outline-secondary me-2"><i class="fas fa-arrow-left me-1"></i> <?php echo e(__('Back to User')); ?></a>
                                <button type="button" class="btn btn-primary btn-view-history"><i class="fas fa-history me-1"></i> <?php echo e(__('View History')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card modern-card stats-card stats-card-primary h-100">
                                <div class="stats-card-body d-flex align-items-center">
                                    <div class="stats-card-content flex-grow-1">
                                        <div class="stats-number" data-countup data-target="<?php echo e($balanceStats['total_added'] ?? 0); ?>"><?php echo e(number_format($balanceStats['total_added'] ?? 0, 2)); ?> <?php echo e($defaultCurrency ? $defaultCurrency->symbol : 'USD'); ?></div>
                                        <div class="stats-label"><?php echo e(__('Total Added')); ?></div>
                                    </div>
                                    <div class="stats-icon ms-3"><i class="fas fa-wallet"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card modern-card stats-card stats-card-info h-100">
                                <div class="stats-card-body d-flex align-items-center">
                                    <div class="stats-card-content flex-grow-1">
                                        <div class="stats-number" data-countup data-target="<?php echo e($balanceStats['total_deducted'] ?? 0); ?>"><?php echo e(number_format($balanceStats['total_deducted'] ?? 0, 2)); ?> <?php echo e($defaultCurrency ? $defaultCurrency->symbol : 'USD'); ?></div>
                                        <div class="stats-label"><?php echo e(__('Total Deducted')); ?></div>
                                    </div>
                                    <div class="stats-icon ms-3"><i class="fas fa-minus-circle"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card modern-card stats-card stats-card-success h-100">
                                <div class="stats-card-body d-flex align-items-center">
                                    <div class="stats-card-content flex-grow-1">
                                        <div class="stats-number" data-countup data-target="<?php echo e($balanceStats['net_balance_change'] ?? 0); ?>"><?php echo e(number_format($balanceStats['net_balance_change'] ?? 0, 2)); ?> <?php echo e($defaultCurrency ? $defaultCurrency->symbol : 'USD'); ?></div>
                                        <div class="stats-label"><?php echo e(__('Net Change')); ?></div>
                                    </div>
                                    <div class="stats-icon ms-3"><i class="fas fa-chart-line"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Preview -->
                <div class="card modern-card border-0 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2 justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-history text-info"></i>
                        <h5 class="card-title mb-0"><?php echo e(__('Recent Transactions')); ?></h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-view-history">
                        <?php echo e(__('View All')); ?>

                    </button>
                </div>
                <div class="card-body">
                    <div class="history-placeholder">
                        <div class="empty-state text-center">
                            <p><?php echo e(__('No transactions yet.')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- User Summary -->
            <div class="card modern-card border-0 shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-user text-primary"></i>
                    <h5 class="card-title mb-0"><?php echo e(__('User Summary')); ?></h5>
                </div>
                <div class="card-body">
                    <div class="user-profile">
                        <div class="user-avatar">
                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                        </div>
                        <div class="user-info">
                            <h4><?php echo e($user->name); ?></h4>
                            <p><?php echo e($user->email); ?></p>
                            <span
                                class="badge bg-<?php echo e($user->role === 'admin' ? 'primary' : ($user->role === 'vendor' ? 'success' : 'secondary')); ?>">
                                <?php echo e(ucfirst($user->role)); ?>

                            </span>
                        </div>
                    </div>

                    <div class="user-details">
                        <div class="detail-item">
                            <label><?php echo e(__('Phone')); ?></label>
                            <span><?php echo e($user->phone ?? __('Not provided')); ?></span>
                        </div>
                        <div class="detail-item">
                            <label><?php echo e(__('Registered')); ?></label>
                            <span><?php echo e($user->created_at->format('M d, Y')); ?></span>
                        </div>
                        <div class="detail-item">
                            <label><?php echo e(__('Last Updated')); ?></label>
                            <span><?php echo e($user->updated_at->diffForHumans()); ?></span>
                        </div>
                        <div class="detail-item">
                            <label><?php echo e(__('Status')); ?></label>
                            <span>
                                <?php if($user->approved_at): ?>
                                <span class="badge bg-success"><?php echo e(__('Approved')); ?></span>
                                <?php else: ?>
                                <span class="badge bg-warning"><?php echo e(__('Pending')); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card modern-card border-0 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-bolt text-warning"></i>
                    <h5 class="card-title mb-0"><?php echo e(__('Quick Actions')); ?></h5>
                </div>
                <div class="card-body quick-actions">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>
                            <?php echo e(__('Edit User')); ?>

                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-view-history">
                            <i class="fas fa-history me-1"></i>
                            <?php echo e(__('View Balance History')); ?>

                        </button>
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-list me-1"></i>
                            <?php echo e(__('All Users')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Balance Modal -->
<div class="modal fade" id="addBalanceModal" tabindex="-1" aria-labelledby="addBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBalanceModalLabel">
                    <i class="fas fa-plus-circle text-success me-2"></i>
                    <?php echo e(__('Add Balance')); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBalanceForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addAmount"><?php echo e(__('Amount')); ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" max="999999" class="form-control" id="addAmount"
                                name="amount" required placeholder="0.00">
                            <div class="input-group-append">
                                <span
                                    class="input-group-text"><?php echo e($defaultCurrency ? $defaultCurrency->symbol : 'USD'); ?></span>
                            </div>
                        </div>
                        <small class="form-text text-muted"><?php echo e(__('Enter the amount to add to user balance')); ?></small>
                    </div>
                    <div class="form-group">
                        <label for="addReason"><?php echo e(__('Reason')); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="addReason" name="note" rows="3" required
                            placeholder="<?php echo e(__('Enter reason for adding balance...')); ?>"></textarea>
                        <small class="form-text text-muted"><?php echo e(__('Minimum 3 characters required')); ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>
                        <?php echo e(__('Add Balance')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deduct Balance Modal -->
<div class="modal fade" id="deductBalanceModal" tabindex="-1" aria-labelledby="deductBalanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deductBalanceModalLabel">
                    <i class="fas fa-minus-circle text-warning me-2"></i>
                    <?php echo e(__('Deduct Balance')); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deductBalanceForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group alert alert-warning mb-3">
                        <?php echo e(__('Note: Deducting balance cannot be undone')); ?>

                    </div>
                    <div class="form-group">
                        <label for="deductAmount"><?php echo e(__('Amount')); ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" max="<?php echo e($user->balance ?? 0); ?>"
                                id="deductAmount" name="amount" class="form-control" required placeholder="0.00">
                            <div class="input-group-append">
                                <span
                                    class="input-group-text"><?php echo e($defaultCurrency ? $defaultCurrency->symbol : 'USD'); ?></span>
                            </div>
                        </div>
                        <small class="form-text text-muted"><?php echo e(__('Maximum amount you can deduct')); ?>:
                            <?php echo e(number_format($user->balance ?? 0, 2)); ?>

                            <?php echo e($defaultCurrency ? $defaultCurrency->symbol : 'USD'); ?></small>
                    </div>
                    <div class="form-group">
                        <label for="deductReason"><?php echo e(__('Reason')); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deductReason" name="note" rows="3" required
                            placeholder="<?php echo e(__('Enter reason for deducting balance...')); ?>"></textarea>
                        <small class="form-text text-muted"><?php echo e(__('Minimum 3 characters required')); ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-minus me-1"></i>
                        <?php echo e(__('Deduct Balance')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Balance History Modal -->
<div class="modal fade" id="balanceHistoryModal" tabindex="-1" aria-labelledby="balanceHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balanceHistoryModalLabel">
                    <i class="fas fa-history text-info me-2"></i>
                    <?php echo e(__('Balance History')); ?> - <?php echo e($user->name); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="balanceHistoryContainer">
                    <div class="text-center p-4">
                        <div class="loading-spinner mx-auto"></div>
                        <p class="mt-2"><?php echo e(__('Loading history...')); ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
            </div>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<template id="user-balance-config"><?php echo json_encode([
    'urls' => [
        'add' => route('admin.users.add-balance', $user),
        'deduct' => route('admin.users.deduct-balance', $user),
        'stats' => route('admin.users.balance.stats', $user),
        'history' => route('admin.users.balance.history', $user),
        'refresh' => route('admin.users.balance.refresh', $user),
    ],
    'currency' => [
        'code' => $defaultCurrency?->code ?? 'USD',
        'symbol' => $defaultCurrency?->symbol ?? '$'
    ],
    'i18n' => [
        'credit' => __('Credit'),
        'debit' => __('Debit'),
        'balance_added' => __('Balance added successfully'),
        'balance_deducted' => __('Balance deducted successfully'),
        'balance_invalid_add' => __('Please enter a valid amount and a reason'),
        'balance_invalid_deduct' => __('Please enter a valid amount and a reason'),
        'balance_exceeds' => __('Amount exceeds current balance'),
        'balance_refreshed' => __('Data refreshed successfully'),
        'loading_refresh' => __('Refreshing data...'),
        'loading_history' => __('Loading history...'),
        'error' => __('Error'),
        'error_add' => __('Error while adding balance'),
        'error_deduct' => __('Error while deducting balance'),
        'error_refresh' => __('Error while refreshing data'),
        'error_history' => __('Failed to load balance history'),
        'error_server' => __('Server communication error'),
        'no_history' => __('No history'),
        'no_history_desc' => __('No previous transactions found'),
        'not_specified' => __('Not specified'),
        'processing' => __('Processing...'),
    ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></template>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/users/balance.blade.php ENDPATH**/ ?>