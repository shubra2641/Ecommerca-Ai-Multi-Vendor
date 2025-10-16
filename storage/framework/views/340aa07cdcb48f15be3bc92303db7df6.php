<?php $__env->startSection('title', __('Product Tags')); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-8">
        <h1 class="page-title mb-1"><?php echo e(__('Product Tags')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Organize and filter products with tags')); ?></p>
    </div>
    <div class="col-12 col-md-4 mt-3 mt-md-0">
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
            <button class="btn btn-outline-secondary" title="<?php echo e(__('Export Tags')); ?>">
                <i class="fas fa-download me-1"></i>
                <span class="d-none d-sm-inline"><?php echo e(__('Export Tags')); ?></span>
                <span class="d-sm-none"><?php echo e(__('Export')); ?></span>
            </button>
            <a href="<?php echo e(route('admin.product-tags.create')); ?>" class="btn btn-primary" title="<?php echo e(__('Add Tag')); ?>">
                <i class="fas fa-plus me-1"></i>
                <span class="d-none d-sm-inline"><?php echo e(__('Add Tag')); ?></span>
                <span class="d-sm-none"><?php echo e(__('Add')); ?></span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-6 col-lg-3 mb-3">
    <div class="stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($tags->total()); ?></div>
                    <div class="stats-label"><?php echo e(__('Total Tags')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
    <div class="stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($tags->where('products_count', '>', 0)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Used Tags')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-box"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
    <div class="stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($tags->where('products_count', 0)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Unused Tags')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
            <div class="stats-card stats-card-warning h-100">
                <div class="card modern-card stats-card h-100">
                    <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($tags->where('created_at', '>=', now()->subDays(30))->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('New This Month')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-calendar-plus"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.product-tags.index')); ?>">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label"><?php echo e(__('Search Tags')); ?></label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('Search by name or slug...')); ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label for="usage" class="form-label"><?php echo e(__('Usage Status')); ?></label>
                    <select class="form-select" id="usage" name="usage">
                        <option value=""><?php echo e(__('All Tags')); ?></option>
                        <option value="used" <?php echo e(request('usage') == 'used' ? 'selected' : ''); ?>><?php echo e(__('Used Tags')); ?></option>
                        <option value="unused" <?php echo e(request('usage') == 'unused' ? 'selected' : ''); ?>><?php echo e(__('Unused Tags')); ?></option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="per_page" class="form-label"><?php echo e(__('Per Page')); ?></label>
                    <select class="form-select" id="per_page" name="per_page">
                        <option value="10" <?php echo e(request('per_page', 10) == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="25" <?php echo e(request('per_page', 10) == 25 ? 'selected' : ''); ?>>25</option>
                        <option value="50" <?php echo e(request('per_page', 10) == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e(request('per_page', 10) == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i><?php echo e(__('Filter')); ?>

                        </button>
                        <a href="<?php echo e(route('admin.product-tags.index')); ?>" class="btn btn-outline-secondary" title="<?php echo e(__('Clear')); ?>">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tags List -->
<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title mb-0"><?php echo e(__('All Tags')); ?></h3>
    </div>
    <div class="card-body">
        <?php if($tags->count() > 0): ?>
            <!-- Desktop Table View -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo e(__('Name')); ?></th>
                                <th><?php echo e(__('Slug')); ?></th>
                                <th class="text-center"><?php echo e(__('Products Count')); ?></th>
                                <th class="text-center" width="150"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($tag->name); ?></td>
                                <td class="text-muted small"><?php echo e($tag->slug); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo e($tag->products_count > 0 ? 'success' : 'secondary'); ?>">
                                        <?php echo e($tag->products_count ?? 0); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('admin.product-tags.edit', $tag)); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="<?php echo e(__('Edit')); ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.product-tags.destroy',$tag)); ?>" class="d-inline js-confirm" data-confirm="<?php echo e(__('Are you sure you want to delete this tag?')); ?>"><?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="<?php echo e(__('Delete')); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                <div class="row">
                    <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 mb-3">
                        <div class="card border">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold"><?php echo e($tag->name); ?></h6>
                                        <p class="text-muted small mb-1"><?php echo e($tag->slug); ?></p>
                                        <span class="badge bg-<?php echo e(($tag->products_count ?? 0) > 0 ? 'success' : 'secondary'); ?> small">
                                            <?php echo e($tag->products_count ?? 0); ?> <?php echo e(__('Products')); ?>

                                        </span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="dropdown" aria-expanded="false" title="<?php echo e(__('Actions')); ?>">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="<?php echo e(route('admin.product-tags.edit', $tag)); ?>">
                                                    <i class="fas fa-edit me-2"></i><?php echo e(__('Edit')); ?>

                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="<?php echo e(route('admin.product-tags.destroy',$tag)); ?>" class="js-confirm" data-confirm="<?php echo e(__('Are you sure you want to delete this tag?')); ?>"><?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i><?php echo e(__('Delete')); ?>

                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Pagination -->
            <?php if($tags->hasPages()): ?>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                    <div class="pagination-info text-muted small">
                        <?php echo e(__('Showing')); ?> <?php echo e($tags->firstItem()); ?> <?php echo e(__('to')); ?> <?php echo e($tags->lastItem()); ?> 
                        <?php echo e(__('of')); ?> <?php echo e($tags->total()); ?> <?php echo e(__('results')); ?>

                    </div>
                    <div class="pagination-wrapper">
                        <?php echo e($tags->appends(request()->query())->links()); ?>

                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-tags fa-3x mb-3"></i>
                <div class="h5"><?php echo e(__('No tags found')); ?></div>
                <p class="mb-3"><?php echo e(__('Start by creating your first tag')); ?></p>
                <a href="<?php echo e(route('admin.product-tags.create')); ?>" class="btn btn-primary" title="<?php echo e(__('Add Tag')); ?>">
                    <i class="fas fa-plus me-1"></i><?php echo e(__('Add Tag')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/tags/index.blade.php ENDPATH**/ ?>