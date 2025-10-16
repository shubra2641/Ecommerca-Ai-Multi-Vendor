<?php $__env->startSection('title', __('Languages Management')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Languages Management')); ?></h1>
        <p class="page-description"><?php echo e(__('Manage system languages and translations')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.languages.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <?php echo e(__('Add New Language')); ?>

        </a>
        <button type="button" class="btn btn-secondary" data-action="refresh-translations">
            <i class="fas fa-sync-alt"></i>
            <?php echo e(__('Refresh Cache')); ?>

        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-language"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number"><?php echo e($languages->count()); ?></div>
                <div class="stats-label"><?php echo e(__('Total Languages')); ?></div>
            </div>
        </div>
    </div>
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number"><?php echo e($languages->where('active', true)->count()); ?></div>
                <div class="stats-label"><?php echo e(__('Active Languages')); ?></div>
            </div>
        </div>
    </div>
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number"><?php echo e($languages->where('is_default', true)->count()); ?></div>
                <div class="stats-label"><?php echo e(__('Default Language')); ?></div>
            </div>
        </div>
    </div>
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number"><?php echo e($totalTranslations ?? 0); ?></div>
                <div class="stats-label"><?php echo e(__('Total Translations')); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title"><?php echo e(__('All Languages')); ?></h3>
        <div class="card-actions">
            <div class="search-box">
                <input type="text" class="form-control table-search" placeholder="<?php echo e(__('Search languages...')); ?>">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php if($languages->count() > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="select-all">
                        </th>
                        <th><?php echo e(__('Language')); ?></th>
                        <th><?php echo e(__('Code')); ?></th>
                        <th><?php echo e(__('Flag')); ?></th>
                        <th><?php echo e(__('Status')); ?></th>
                        <th><?php echo e(__('Default')); ?></th>
                        <th><?php echo e(__('Direction')); ?></th>
                        <th><?php echo e(__('Translations')); ?></th>
                        <th width="200"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="row-checkbox" value="<?php echo e($language->id); ?>">
                        </td>
                        <td>
                            <div class="language-info">
                                <div class="language-name"><?php echo e($language->name); ?></div>
                                <div class="language-native"><?php echo e($language->native_name ?? $language->name); ?></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?php echo e(strtoupper($language->code)); ?></span>
                        </td>
                        <td>
                            <?php if($language->flag): ?>
                            <div class="language-flag">
                                <?php echo e($language->flag); ?>

                            </div>
                            <?php else: ?>
                            <span class="text-muted"><?php echo e(__('No Flag')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="status-badge">
                                <?php if($language->active): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i>
                                    <?php echo e(__('Active')); ?>

                                </span>
                                <?php else: ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-times"></i>
                                    <?php echo e(__('Inactive')); ?>

                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if($language->is_default): ?>
                            <span class="badge bg-warning">
                                <i class="fas fa-star"></i>
                                <?php echo e(__('Default')); ?>

                            </span>
                            <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-action="set-default"
                                data-language-id="<?php echo e($language->id); ?>">
                                <?php echo e(__('Set Default')); ?>

                            </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i
                                    class="fas fa-<?php echo e($language->direction === 'rtl' ? 'align-right' : 'align-left'); ?>"></i>
                                <?php echo e(strtoupper($language->direction ?? 'ltr')); ?>

                            </span>
                        </td>
                        <td>
                            <div class="translation-status">
                                <span class="translation-count"><?php echo e($language->translations_count ?? 0); ?></span>
                                <a href="<?php echo e(route('admin.languages.translations', $language)); ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                    <?php echo e(__('Manage')); ?>

                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo e(route('admin.languages.edit', $language)); ?>" class="btn btn-outline-secondary"
                                        title="<?php echo e(__('Edit')); ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="<?php echo e(route('admin.languages.translations', $language)); ?>"
                                        class="btn btn-outline-secondary" title="<?php echo e(__('Translations')); ?>">
                                        <i class="fas fa-language"></i>
                                    </a>

                                    <?php if(!$language->is_default): ?>
                                    <form action="<?php echo e(route('admin.languages.destroy', $language)); ?>" method="POST"
                                        class="delete-form d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-secondary" title="<?php echo e(__('Delete')); ?>"
                                            data-confirm="<?php echo e(__('Are you sure you want to delete this language?')); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <div class="bulk-actions-content">
                <span class="selected-text">
                    <?php echo e(__('Selected')); ?>: <span class="selected-count">0</span> <?php echo e(__('items')); ?>

                </span>
                <div class="bulk-buttons">
                    <button type="button" class="btn btn-sm btn-success" data-action="bulk-activate">
                        <i class="fas fa-check"></i>
                        <?php echo e(__('Activate')); ?>

                    </button>
                    <button type="button" class="btn btn-sm btn-warning" data-action="bulk-deactivate">
                        <i class="fas fa-times"></i>
                        <?php echo e(__('Deactivate')); ?>

                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-action="bulk-delete">
                        <i class="fas fa-trash"></i>
                        <?php echo e(__('Delete')); ?>

                    </button>
                </div>
            </div>
        </div>

        <?php if($languages->hasPages()): ?>
        <div class="pagination-wrapper">
            <?php echo e($languages->links()); ?>

        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-language fa-3x"></i>
            <h3><?php echo e(__('No Languages Found')); ?></h3>
            <p><?php echo e(__('Start by adding your first language to the system.')); ?></p>
            <a href="<?php echo e(route('admin.languages.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <?php echo e(__('Add Language')); ?>

            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/languages.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/languages.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/languages/index.blade.php ENDPATH**/ ?>