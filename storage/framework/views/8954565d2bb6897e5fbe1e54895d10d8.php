<?php $__env->startSection('content'); ?>
<div class="container section">
    <div class="row">
        <div class="col-md-3">
            <?php echo $__env->make('front.account._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-md-9">
            <h1 class="mb-4"><?php echo e(__('returns.title')); ?></h1>

            <?php if($items->isEmpty()): ?>
            <div class="empty-state p-4 text-center border rounded bg-light">
                <p class="mb-0"><?php echo e(__('returns.empty')); ?></p>
            </div>
            <?php endif; ?>

            <div class="row gy-3">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12">
                    <div class="card product-card shadow-sm">
                        <div class="card-body d-flex gap-3 align-items-start">
                            <div class="thumb thumb-fixed">
                                <?php if($item->product && $item->product->main_image): ?>
                                <img src="<?php echo e(storage_image_url($item->product->main_image)); ?>" class="img-fluid rounded"
                                    alt="<?php echo e($item->name); ?>">
                                <?php else: ?>
                                <div class="placeholder rounded bg-secondary text-white text-center"
                                    class="d-flex align-items-center justify-content-center h-100">
                                    <?php echo e(__('returns.no_image')); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="flex-fill">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1"><?php echo e($item->name); ?></h5>
                                        <div class="meta text-muted"><?php echo e(__('returns.order')); ?> <a
                                                href="<?php echo e(route('orders.show', $item->order)); ?>">#<?php echo e($item->order_id); ?></a>
                                            · <?php echo e($item->qty); ?> x <?php echo e(format_price($item->price ?? 0)); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted"><?php echo e($item->purchased_at?->toDateString() ?? '-'); ?>

                                        </div>
                                        <div class="small text-muted"><?php echo e(__('returns.return_until')); ?>

                                            <?php echo e($item->refund_expires_at?->toDateString() ?? __('No return')); ?></div>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    <?php if($item->isWithinReturnWindow() && ! $item->return_requested): ?>
                                    <button class="btn btn-outline-danger" data-bs-toggle="collapse"
                                        data-bs-target="#return-form-<?php echo e($item->id); ?>"><?php echo e(__('returns.request_button')); ?></button>
                                    <?php else: ?>
                                    <span
                                        class="badge bg-secondary"><?php echo e($item->return_requested ? __('returns.requested') : __('returns.return_expired')); ?></span>
                                    <?php endif; ?>
                                    <?php if($item->return_status): ?>
                                    <span class="badge bg-info text-dark"><?php echo e($item->return_status); ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if($item->isWithinReturnWindow() && ! $item->return_requested): ?>
                                <div class="collapse mt-3" id="return-form-<?php echo e($item->id); ?>">
                                    <form method="post" action="<?php echo e(route('user.returns.request', $item)); ?>"
                                        enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo e(__('returns.reason')); ?></label>
                                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo e(__('returns.attach_image_optional')); ?></label>
                                            <input type="file" name="image" accept="image/*" class="form-control">
                                        </div>
                                        <div>
                                            <button class="btn btn-danger"><?php echo e(__('returns.submit_request')); ?></button>
                                        </div>
                                    </form>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($item->meta['user_images']) || !empty($item->meta['admin_images'])): ?>
                                <div class="mt-3 small">
                                    <div class="row gy-2">
                                            <?php $__currentLoopData = array_merge($item->meta['user_images'] ?? [],
                                        $item->meta['admin_images'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-auto">
                            <a href="<?php echo e(storage_image_url($img)); ?>" target="_blank"><img
                                src="<?php echo e(storage_image_url($img)); ?>" class="img-fluid max-w-100 rounded shadow-sm"></a>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($item->meta['history'])): ?>
                                <div class="mt-3">
                                    <strong><?php echo e(__('History')); ?></strong>
                                    <ul class="small mb-0">
                                        <?php $__currentLoopData = $item->meta['history']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>[<?php echo e($h['when']); ?>] <strong><?php echo e(ucfirst($h['actor'])); ?></strong> —
                                            <?php echo e($h['action']); ?><?php echo e($h['note'] ? ': '.$h['note'] : ''); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator): ?>
                <div class="mt-4"><?php echo e($items->links()); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/returns/index.blade.php ENDPATH**/ ?>