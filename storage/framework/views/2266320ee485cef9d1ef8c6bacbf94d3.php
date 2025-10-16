<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Send Notification')); ?></h1>
        <p class="page-description"><?php echo e(__('Send a notification to users or vendors')); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <h3 class="card-title mb-0"><?php echo e(__('Send Notification')); ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.notifications.send.store')); ?>" class="admin-form">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Target role')); ?></label>
                        <select name="role" class="form-select" required>
                            <option value="vendor"><?php echo e(__('Vendors')); ?></option>
                            <option value="user"><?php echo e(__('Users')); ?></option>
                        </select>
                    </div>

                    <?php ($langs = $languages); ?>
                    <ul class="nav nav-tabs" role="tablist">
                        <?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item" role="presentation"><button class="nav-link <?php echo e($i==0? 'active':''); ?>" id="tab-<?php echo e($lang->code); ?>" data-bs-toggle="tab" data-bs-target="#panel-<?php echo e($lang->code); ?>" type="button"><?php echo e($lang->code); ?></button></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <div class="tab-content mt-3">
                        <?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tab-pane fade <?php echo e($i==0? 'show active':''); ?>" id="panel-<?php echo e($lang->code); ?>" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo e(__('Title')); ?> (<?php echo e($lang->code); ?>)</label>
                                    <input type="text" name="title[<?php echo e($lang->code); ?>]" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo e(__('Message')); ?> (<?php echo e($lang->code); ?>)</label>
                                    <textarea name="message[<?php echo e($lang->code); ?>]" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Optional URL')); ?></label>
                        <input type="url" name="url" class="form-control" placeholder="https://..." />
                    </div>

                    <button class="btn btn-primary"><?php echo e(__('Send')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/notifications/send.blade.php ENDPATH**/ ?>