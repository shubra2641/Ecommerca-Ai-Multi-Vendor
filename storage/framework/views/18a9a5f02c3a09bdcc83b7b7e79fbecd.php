<?php $__env->startSection('title', __('Social Links')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title mb-1"><?php echo app('translator')->get('Social Links'); ?></h1>
        <p class="text-muted mb-0"><?php echo app('translator')->get('Manage social media links and their display order'); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.social.create')); ?>" class="btn btn-primary"><?php echo app('translator')->get('Add Link'); ?></a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="card modern-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo app('translator')->get('Social Links'); ?></h5>
        <a href="<?php echo e(route('admin.social.create')); ?>" class="btn btn-sm btn-primary"><?php echo app('translator')->get('Add Link'); ?></a>
    </div>
    <div class="card-body">
        <?php if(!$links->count()): ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-share-alt fa-3x mb-3 text-muted"></i>
                <p class="mb-3"><?php echo app('translator')->get('No social links yet. Click Add Link to create one.'); ?></p>
                <a href="<?php echo e(route('admin.social.create')); ?>" class="btn btn-primary"><?php echo app('translator')->get('Add First Link'); ?></a>
            </div>
        <?php else: ?>
        <form method="post" action="<?php echo e(route('admin.social.reorder')); ?>" id="reorder-form">
            <?php echo csrf_field(); ?>
            <div class="table-responsive">
                <table class="table table-striped" id="social-links-table">
            <thead>
                <tr>
                    <th class="w-40"></th>
                    <th><?php echo app('translator')->get('Platform'); ?></th>
                    <th><?php echo app('translator')->get('Label'); ?></th>
                    <th><?php echo app('translator')->get('URL'); ?></th>
                    <th><?php echo app('translator')->get('Icon'); ?></th>
                    <th><?php echo app('translator')->get('Active'); ?></th>
                    <th class="text-end w-160"><?php echo app('translator')->get('Actions'); ?></th>
                </tr>
            </thead>
            <tbody id="sortable-body">
                <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr data-id="<?php echo e($link->id); ?>">
                    <td class="text-muted cursor-move"><i class="fas fa-grip-vertical"></i></td>
                    <td><?php echo e($link->platform); ?></td>
                    <td><?php echo e($link->label); ?></td>
                    <td><a href="<?php echo e($link->url); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($link->url); ?></a></td>
                    <td><?php if($link->icon): ?><i class="<?php echo e($link->icon); ?>" title="<?php echo e($link->platform); ?>"></i><?php endif; ?></td>
                    <td>
                        <?php if($link->is_active): ?>
                            <span class="badge bg-success"><?php echo app('translator')->get('Yes'); ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?php echo app('translator')->get('No'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="<?php echo e(route('admin.social.edit', $link)); ?>" class="btn btn-outline-secondary"><?php echo app('translator')->get('Edit'); ?></a>
                            <form action="<?php echo e(route('admin.social.destroy', $link)); ?>" method="post" class="d-inline-block js-confirm" data-confirm="<?php echo app('translator')->get('Are you sure you want to delete this social link?'); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-outline-danger"><?php echo app('translator')->get('Delete'); ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-secondary" id="save-order" disabled><?php echo app('translator')->get('Save Order'); ?></button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/social-links.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/social/index.blade.php ENDPATH**/ ?>