

<?php $__env->startSection('title', __('Order :id', ['id' => $order->id])); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Order')); ?> #<?php echo e($order->id); ?></h1>
        <p class="page-description"><?php echo e(__('Order details, payments and management')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <?php echo e(__('Back to Orders')); ?>

        </a>
        <form method="POST" action="<?php echo e(route('admin.orders.retry-assign', $order->id)); ?>" class="d-inline admin-form">
            <?php echo csrf_field(); ?>
            <button class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i>
                <?php echo e(__('Retry Serials')); ?>

            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-receipt text-primary"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Summary')); ?></h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted"><?php echo e(__('Order ID')); ?></dt>
                    <dd class="col-7">#<?php echo e($order->id); ?></dd>

                    <dt class="col-5 text-muted"><?php echo e(__('Placed')); ?></dt>
                    <dd class="col-7"><?php echo e($order->created_at->format('Y-m-d H:i')); ?></dd>

                    <dt class="col-5 text-muted"><?php echo e(__('Customer')); ?></dt>
                    <dd class="col-7">
                        <?php if($order->user): ?>
                        <div><strong><?php echo e($order->user->name ?? __('(No Name)')); ?></strong></div>
                        <div class="small text-muted"><?php echo e($order->user->email); ?></div>
                        <?php if($order->user->phone ?? false): ?>
                        <div class="small"><?php echo e($order->user->phone); ?></div>
                        <?php endif; ?>
                        <?php else: ?>
                        <?php echo e(__('Guest')); ?>

                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted"><?php echo e(__('Status')); ?></dt>
                    <dd class="col-7">
                        <span class="badge bg-info"><?php echo e(ucfirst($order->status)); ?></span>
                        <?php if(!empty($order->has_backorder)): ?>
                        <span class="badge bg-warning text-dark ms-1"><?php echo e(__('Backorder')); ?></span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted"><?php echo e(__('Payment Status')); ?></dt>
                    <dd class="col-7"><?php echo e($order->payment_status ?? __('Pending')); ?></dd>

                    <dt class="col-5 text-muted"><?php echo e(__('Shipping')); ?></dt>
                    <dd class="col-7"><?php echo e($order->shipping_method ?? __('N/A')); ?></dd>

                    <dt class="col-5 text-muted"><?php echo e(__('Total')); ?></dt>
                    <dd class="col-7"><span class="stats-number" data-countup data-target="<?php echo e($order->total); ?>"><?php echo e(number_format($order->total,2)); ?></span>
                        <span class="small text-muted"><?php echo e($order->currency ?? ''); ?></span>
                    </dd>
                </dl>

                <hr>
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn btn-outline-secondary"><?php echo e(__('Refresh')); ?></a>
                </div>
            </div>
        </div>

    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-history text-secondary"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Status History')); ?></h3>
            </div>
            <div class="card-body">
                <?php if($order->statusHistory->count()): ?>
                <ul class="list-unstyled">
                    <?php $__currentLoopData = $order->statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e(ucfirst($hist->status)); ?></strong>
                        <div class="small text-muted"><?php echo e($hist->created_at->format('Y-m-d H:i')); ?></div>
                        <?php if($hist->note): ?><div class="mt-1"><?php echo e($hist->note); ?></div><?php endif; ?>
                        <hr>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php else: ?>
                <div class="small text-muted"><?php echo e(__('No status changes yet.')); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2 justify-content-between">
                <div class="d-flex align-items-center gap-2"><i class="fas fa-box-open text-info"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Items')); ?></h3></div>
                <div class="card-actions">
                    <!-- actions could go here -->
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Product')); ?></th>
                            <th><?php echo e(__('Qty')); ?></th>
                            <th><?php echo e(__('Price')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php echo e($item->name); ?> <?php if(!empty($aovVariantLabels[$item->id])): ?><span class="text-muted small">—
                                    <?php echo e($aovVariantLabels[$item->id]); ?></span><?php endif; ?>
                                <?php if(!empty($item->is_backorder)): ?>
                                <div class="mt-1"><span class="badge bg-warning text-dark"><?php echo e(__('Backorder')); ?></span>
                                    <form method="POST"
                                        action="<?php echo e(route('admin.orders.cancelBackorderItem', ['order' => $order->id, 'item' => $item->id])); ?>"
                                        class="d-inline ms-2 admin-form">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-danger"
                                            type="submit"><?php echo e(__('Cancel Backorder')); ?></button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e($item->qty); ?><br>
                                <span class="badge bg-<?php echo e($item->committed? 'success':'secondary'); ?> small"
                                   ><?php echo e($item->committed? __('Committed'):__('Not Committed')); ?></span>
                                <?php if($item->restocked): ?>
                                <span class="badge bg-info text-dark small"
                                   ><?php echo e(__('Restocked')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(number_format($item->price,2)); ?></td>
                        </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

            <div class="card modern-card mt-3">
            <div class="card-header d-flex align-items-center gap-2">
                <h3 class="card-title mb-0"><?php echo e(__('Customer & Shipping')); ?></h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h5 class="mb-2"><?php echo e(__('Customer Details')); ?></h5>
                        <?php if($order->user): ?>
                        <div><strong><?php echo e($order->user->name ?? __('(No Name)')); ?></strong></div>
                        <div class="small text-muted"><?php echo e($order->user->email); ?></div>
                        <?php if($order->user->phone ?? false): ?>
                        <div class="small"><?php echo e($order->user->phone); ?></div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div><?php echo e(__('Guest')); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-2"><?php echo e(__('Shipping Address')); ?></h5>
                        <div class="small text-muted ws-pre-line"><?php echo e($aovAddressText ?: __('N/A')); ?></div>
                    </div>
                </div>

                <?php if(!empty($order->notes) || !empty($aovFirstPaymentNote)): ?>
                <hr>
                <div><strong><?php echo e(__('Notes')); ?></strong>
                    <div class="small text-muted"><?php echo e($order->notes ?? $aovFirstPaymentNote); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card modern-card mt-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-credit-card text-success"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Payments')); ?></h3>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $order->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="payment-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo e($payment->method); ?></strong>
                            <div class="small text-muted"><?php echo e(number_format($payment->amount,2)); ?>

                                <?php echo e($payment->currency ?? ''); ?>

                            </div>
                        </div>
                        <div>
                            <span
                                class="badge <?php echo e($payment->status === 'completed' ? 'bg-success' : 'bg-warning'); ?>"><?php echo e(ucfirst($payment->status)); ?></span>
                        </div>
                    </div>

                    <?php if($payment->attachments->count()): ?>
                    <div class="mt-2">
                        <strong><?php echo e(__('Attachments:')); ?></strong>
                        <?php $__currentLoopData = $payment->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(asset('storage/'.$att->path)); ?>" target="_blank"
                            class="d-block"><?php echo e($att->path); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>

                    <?php if($payment->status !== 'completed' && !empty($aovOfflinePayments[$payment->id])): ?>
                    <div class="mt-2">
                        <form method="POST" action="<?php echo e(route('admin.orders.payments.accept', $payment->id)); ?>"
                            class="d-inline admin-form">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-success"><?php echo e(__('Accept')); ?></button>
                        </form>
                        <form method="POST" action="<?php echo e(route('admin.orders.payments.reject', $payment->id)); ?>"
                            class="d-inline admin-form">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-warning"><?php echo e(__('Reject')); ?></button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="card modern-card mt-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-exchange-alt text-primary"></i>
                <h3 class="card-title mb-0"><?php echo e(__('Manage')); ?></h3>
                <div class="card-actions">
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.orders.updateStatus', $order->id)); ?>" class="admin-form">
                    <?php echo csrf_field(); ?>
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label class="form-label"><?php echo e(__('Change Status')); ?></label>
                            <select name="status" class="form-select">
                                <?php $__currentLoopData = ['pending','processing','completed','cancelled','on-hold','refunded']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s); ?>" <?php echo e($order->status === $s? 'selected':''); ?>><?php echo e(ucfirst($s)); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label"><?php echo e(__('Note (optional)')); ?></label>
                            <input type="text" name="note" class="form-control"
                                placeholder="<?php echo e(__('Provide optional note')); ?>">
                        </div>
                        <div class="col-auto self-end">
                            <button class="btn btn-primary"><?php echo e(__('Update')); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>