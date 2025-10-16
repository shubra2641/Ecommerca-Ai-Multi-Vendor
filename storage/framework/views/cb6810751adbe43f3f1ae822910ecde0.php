<?php $__env->startSection('title', __('Product Attributes Management')); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div class="page-header-content">
        <h1 class="page-title mb-1"><?php echo e(__('Product Attributes Management')); ?></h1>
        <p class="page-description mb-0 text-muted"><?php echo e(__('Define selectable product characteristics')); ?></p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-secondary" data-action="export-attributes">
            <i class="fas fa-download"></i>
            <span class="d-none d-sm-inline ms-1"><?php echo e(__('Export')); ?></span>
        </button>
        <a href="<?php echo e(route('admin.product-attributes.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span class="d-none d-sm-inline ms-1"><?php echo e(__('Add Attribute')); ?></span>
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($attributes->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Total Attributes')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($attributes->where('active', true)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Active Attributes')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card stats-card-warning h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($attributes->where('type', 'select')->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Select Attributes')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-list"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number"><?php echo e($attributes->where('type', 'color')->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Color Attributes')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-palette"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>
            <?php echo e(__('Filter & Search')); ?>

        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.product-attributes.index')); ?>">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label"><?php echo e(__('Search')); ?></label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="<?php echo e(__('Search attributes...')); ?>">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label"><?php echo e(__('Type')); ?></label>
                    <select name="type" class="form-select">
                        <option value=""><?php echo e(__('All Types')); ?></option>
                        <option value="select" <?php if(request('type') === 'select'): echo 'selected'; endif; ?>><?php echo e(__('Select')); ?></option>
                        <option value="color" <?php if(request('type') === 'color'): echo 'selected'; endif; ?>><?php echo e(__('Color')); ?></option>
                        <option value="text" <?php if(request('type') === 'text'): echo 'selected'; endif; ?>><?php echo e(__('Text')); ?></option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label"><?php echo e(__('Status')); ?></label>
                    <select name="status" class="form-select">
                        <option value=""><?php echo e(__('All Status')); ?></option>
                        <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option>
                        <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search"></i>
                            <span class="d-none d-sm-inline ms-1"><?php echo e(__('Filter')); ?></span>
                        </button>
                        <a href="<?php echo e(route('admin.product-attributes.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="card modern-card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <div>
            <h5 class="card-title mb-0"><?php echo e(__('Attributes List')); ?></h5>
            <small class="text-muted"><?php echo e(__('Browse and manage your product attributes')); ?></small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 small"><?php echo e(__('Per Page')); ?>:</label>
            <select class="form-select form-select-sm js-per-page-select" data-url-prefix="<?php echo e(route('admin.product-attributes.index')); ?>?per_page=" data-url-suffix="" >
                <option value="12" <?php if(request('per_page', 12) == 12): echo 'selected'; endif; ?>>12</option>
                <option value="24" <?php if(request('per_page') == 24): echo 'selected'; endif; ?>>24</option>
                <option value="48" <?php if(request('per_page') == 48): echo 'selected'; endif; ?>>48</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if($attributes->count() > 0): ?>
            <div class="row g-3">
                <?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="attribute-card card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="attribute-name card-title mb-0"><?php echo e($attr->name); ?></h6>
                                    <?php if($attr->active): ?>
                                        <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e(__('Inactive')); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="attribute-details mb-3 flex-grow-1">
                                    <div class="small text-muted mb-1">
                                        <strong><?php echo e(__('Slug')); ?>:</strong> <?php echo e($attr->slug); ?>

                                    </div>
                                    <div class="small text-muted mb-1">
                                        <strong><?php echo e(__('Type')); ?>:</strong> 
                                        <span class="badge bg-light text-dark"><?php echo e(ucfirst($attr->type ?? 'select')); ?></span>
                                    </div>
                                    <?php if($attr->values && $attr->values->count() > 0): ?>
                                        <div class="small text-muted">
                                            <strong><?php echo e(__('Values')); ?>:</strong> <?php echo e($attr->values->count()); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="attribute-actions mt-auto">
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo e(route('admin.product-attributes.edit', $attr)); ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-sm-inline ms-1"><?php echo e(__('Edit')); ?></span>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.product-attributes.destroy',$attr)); ?>" class="d-inline js-confirm" data-confirm="<?php echo e(__('Are you sure you want to delete this attribute?')); ?>"><?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <!-- Pagination -->
            <?php if($attributes instanceof \Illuminate\Pagination\LengthAwarePaginator && $attributes->hasPages()): ?>
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-4 gap-3">
                    <div class="pagination-info text-muted small">
                        <?php echo e(__('Showing')); ?> <?php echo e($attributes->firstItem()); ?> <?php echo e(__('to')); ?> <?php echo e($attributes->lastItem()); ?> <?php echo e(__('of')); ?> <?php echo e($attributes->total()); ?> <?php echo e(__('results')); ?>

                    </div>
                    <div class="pagination-links">
                        <?php echo e($attributes->links()); ?>

                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-3">
                    <i class="fas fa-tags fa-3x text-muted"></i>
                </div>
                <h4 class="empty-title"><?php echo e(__('No attributes found')); ?></h4>
                <p class="empty-description text-muted mb-4"><?php echo e(__('Start by creating your first product attribute to define selectable characteristics.')); ?></p>
                <a href="<?php echo e(route('admin.product-attributes.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    <?php echo e(__('Add First Attribute')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/attributes/index.blade.php ENDPATH**/ ?>