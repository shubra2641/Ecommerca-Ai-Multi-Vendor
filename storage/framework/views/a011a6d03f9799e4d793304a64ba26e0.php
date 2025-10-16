<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h3><?php echo e(__('pending_products_heading')); ?>

        <?php if(isset($totalFiltered)): ?>
            <span class="badge bg-primary ms-2"><?php echo e($totalFiltered); ?> / <?php echo e($totalOverall); ?></span>
        <?php endif; ?>
    </h3>
    <p class="text-muted"><?php echo e(__('pending_products_subtitle')); ?></p>
    <form method="get" class="row g-2 align-items-end mt-2">
        <div class="col-md-3">
            <label class="form-label mb-0 small"><?php echo e(__('pending_products_filter_vendor')); ?></label>
            <select name="vendor_id" class="form-select" data-placeholder="<?php echo e(__('pending_products_filter_vendor')); ?>">
                <option value="">-- <?php echo e(__('pending_products_filter_vendor')); ?> --</option>
                <?php $__currentLoopData = $vendors ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($v->id); ?>" <?php if((string)$v->id === (string)($selectedVendorId ?? '')): echo 'selected'; endif; ?>><?php echo e($v->name); ?> (#<?php echo e($v->id); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label mb-0 small"><?php echo e(__('pending_products_filter_search')); ?></label>
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="<?php echo e(__('pending_products_filter_search_ph')); ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-sm btn-primary mt-3"><?php echo e(__('pending_products_filter_apply')); ?></button>
            <a href="<?php echo e(route('admin.products.pending')); ?>" class="btn btn-sm btn-outline-secondary mt-3"><?php echo e(__('pending_products_filter_reset')); ?></a>
        </div>
    </form>
    <div class="card modern-card mt-3">
        <div class="card-header d-flex align-items-center gap-2">
            <h5 class="card-title mb-0"><?php echo e(__('Pending Products')); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-striped mb-0">
        <thead><tr>
            <th><?php echo e(__('pending_products_id')); ?></th>
            <th><?php echo e(__('pending_products_vendor')); ?></th>
            <th><?php echo e(__('pending_products_name')); ?></th>
            <th><?php echo e(__('pending_products_created_at')); ?></th>
            <th><?php echo e(__('pending_products_approved_at')); ?></th>
            <th><?php echo e(__('pending_products_rejection_reason')); ?></th>
            <th><?php echo e(__('pending_products_actions')); ?></th>
        </tr></thead>
            <tbody>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="<?php echo \Illuminate\Support\Arr::toCssClasses(['table-warning'=> (bool)$p->rejection_reason]); ?>">
                    <td><?php echo e($p->id); ?></td>
                    <td><?php echo e($p->vendor?->name); ?> (#<?php echo e($p->vendor_id); ?>)</td>
                    <td><?php echo e($p->name); ?></td>
            <td><?php echo e($p->created_at); ?></td>
            <td><?php echo e(optional($p->approved_at)->format('Y-m-d H:i')); ?></td>
            <td class="max-w-260 ws-pre-line"><?php echo e($p->rejection_reason); ?></td>
                    <td>
                        <form method="post" action="<?php echo e(route('admin.products.approve', $p->id)); ?>" class="d-inline-block"><?php echo csrf_field(); ?><button class="btn btn-sm btn-success"><?php echo e(__('pending_products_approve')); ?></button></form>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo e($p->id); ?>"><?php echo e(__('pending_products_reject_delete')); ?></button>
                        <!-- reject modal -->
                        <div class="modal fade" id="rejectModal<?php echo e($p->id); ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" action="<?php echo e(route('admin.products.reject', $p->id)); ?>" class="reject-delete-form"><?php echo csrf_field(); ?>
                                    <div class="modal-header"><h5 class="modal-title"><?php echo e(__('pending_products_modal_title')); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><?php echo e(__('pending_products_action_label')); ?></label>
                                            <select name="mode" class="form-select action-mode" data-target="rejectFields<?php echo e($p->id); ?>">
                                                <option value="reject"><?php echo e(__('pending_products_action_reject_keep')); ?></option>
                                                <option value="delete"><?php echo e(__('pending_products_action_delete')); ?></option>
                                            </select>
                                        </div>
                                        <div id="rejectFields<?php echo e($p->id); ?>" class="reject-fields">
                                            <label class="form-label"><?php echo e(__('pending_products_reason_label')); ?> <span class="text-danger">*</span></label>
                                            <textarea name="reason" class="form-control" rows="3" placeholder="<?php echo e(__('pending_products_reason_placeholder')); ?>" required></textarea>
                                            <small class="text-muted d-block mt-1"><?php echo e(__('pending_products_reason_help')); ?></small>
                                        </div>
                                        <div class="alert alert-warning d-none mt-3 delete-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i> <?php echo e(__('pending_products_delete_warning')); ?>

                                        </div>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('pending_products_cancel')); ?></button><button class="btn btn-danger submit-action"><?php echo e(__('pending_products_submit')); ?></button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer">
            <?php echo e($products->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/pending.blade.php ENDPATH**/ ?>