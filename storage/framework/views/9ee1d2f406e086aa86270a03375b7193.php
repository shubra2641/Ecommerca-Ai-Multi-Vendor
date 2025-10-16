<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Notifications')); ?></h1>
        <p class="page-description"><?php echo e(__('Manage and send system notifications')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.notifications.send.create')); ?>" class="btn btn-primary"><?php echo e(__('Send notification')); ?></a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <h3 class="card-title mb-0"><?php echo e(__('Notifications')); ?></h3>
            </div>
            <div class="card-body">
                <?php if($notifications->count()): ?>
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?php echo e($n->data['title'] ?? ($n->data['type'] ?? __('Notification'))); ?></strong>
                                    <div class="small text-muted"><?php echo e($n->created_at->diffForHumans()); ?></div>
                                    <div class="mt-1"><?php echo e($n->data['message'] ?? ''); ?></div>
                                </div>
                                <div class="text-end">
                                    <?php if(!$n->read_at): ?>
                                        <form method="POST" action="<?php echo e(route('admin.notifications.read', $n->id)); ?>" class="admin-form">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-sm btn-primary"><?php echo e(__('Mark read')); ?></button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e(__('Read')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <div class="mt-3"><?php echo e($notifications->links()); ?></div>
                <?php else: ?>
                    <div class="text-muted"><?php echo e(__('No notifications')); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>