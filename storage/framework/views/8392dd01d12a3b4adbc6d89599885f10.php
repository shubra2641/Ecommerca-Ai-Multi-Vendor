<?php $__env->startSection('title', __('Products Management')); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', [
'title' => __('Products Management'),
'subtitle' => __('Manage all products in the catalog'),
'actions' => '<a href="'.route('admin.products.export').'" class="btn btn-outline-secondary d-none d-sm-inline-block"><i
        class="fas fa-download"></i> '.e(__('Export')).'</a> <a href="'.route('admin.products.export').'"
    class="btn btn-outline-secondary d-sm-none"><i class="fas fa-download"></i></a> <a
    href="'.route('admin.products.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> <span
        class="d-none d-sm-inline">'.e(__('Add Product')).'</span></a>'
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Stats Cards -->
<div class="row mb-4 g-3">
    <div class="col-6 col-lg-3">
    <div class="stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$products->total()); ?>"><?php echo e($products->total()); ?></div>
                    <div class="stats-label"><?php echo e(__('Total Products')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-box"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
    <div class="stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$products->where('active', true)->count()); ?>"><?php echo e($products->where('active', true)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Active Products')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
    <div class="stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$products->where('is_featured', true)->count()); ?>"><?php echo e($products->where('is_featured', true)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Featured Products')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card stats-card-warning h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$products->where('is_best_seller', true)->count()); ?>"><?php echo e($products->where('is_best_seller', true)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Best Sellers')); ?></div>
                </div>
                <div class="stats-icon"><i class="fas fa-trophy"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="modern-card">
    <div
        class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h5 class="card-title mb-0"><?php echo e(__('Products List')); ?></h5>
            <small class="text-muted"><?php echo e(__('Browse and manage your product catalog')); ?></small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <select name="per_page" class="form-select form-select-sm js-per-page-select" data-url-prefix="<?php echo e(request()->url()); ?>?per_page=" data-url-suffix="&<?php echo e(http_build_query(request()->except('per_page'))); ?>">
                <option value="10" <?php if(request('per_page', 10)==10): echo 'selected'; endif; ?>>10 <?php echo e(__('per page')); ?></option>
                <option value="25" <?php if(request('per_page', 10)==25): echo 'selected'; endif; ?>>25 <?php echo e(__('per page')); ?></option>
                <option value="50" <?php if(request('per_page', 10)==50): echo 'selected'; endif; ?>>50 <?php echo e(__('per page')); ?></option>
                <option value="100" <?php if(request('per_page', 10)==100): echo 'selected'; endif; ?>>100 <?php echo e(__('per page')); ?></option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4 small align-items-end" autocomplete="off">
            <div class="col-12 col-md-3">
                <label class="form-label mb-1"><?php echo e(__('Search')); ?></label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control form-control-sm"
                    placeholder="<?php echo e(__('Name / SKU')); ?>">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1"><?php echo e(__('Category')); ?></label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">-- <?php echo e(__('All')); ?> --</option>
                    <?php $__currentLoopData = \App\Models\ProductCategory::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php if(request('category')==$cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1"><?php echo e(__('Type')); ?></label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">-- <?php echo e(__('All')); ?> --</option>
                    <option value="simple" <?php if(request('type')==='simple' ): echo 'selected'; endif; ?>><?php echo e(__('Simple')); ?></option>
                    <option value="variable" <?php if(request('type')==='variable' ): echo 'selected'; endif; ?>><?php echo e(__('Variable')); ?></option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1"><?php echo e(__('Stock Status')); ?></label>
                <select name="stock" class="form-select form-select-sm">
                    <option value="">-- <?php echo e(__('All')); ?> --</option>
                    <option value="low" <?php if(request('stock')==='low' ): echo 'selected'; endif; ?>><?php echo e(__('Low')); ?></option>
                    <option value="soon" <?php if(request('stock')==='soon' ): echo 'selected'; endif; ?>><?php echo e(__('Soon')); ?></option>
                    <option value="in" <?php if(request('stock')==='in' ): echo 'selected'; endif; ?>><?php echo e(__('In Stock')); ?></option>
                    <option value="na" <?php if(request('stock')==='na' ): echo 'selected'; endif; ?>><?php echo e(__('N/A')); ?></option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1"><?php echo e(__('Flags')); ?></label>
                <select name="flag" class="form-select form-select-sm">
                    <option value="">-- <?php echo e(__('All')); ?> --</option>
                    <option value="featured" <?php if(request('flag')==='featured' ): echo 'selected'; endif; ?>><?php echo e(__('Featured')); ?></option>
                    <option value="best" <?php if(request('flag')==='best' ): echo 'selected'; endif; ?>><?php echo e(__('Best Seller')); ?></option>
                    <option value="inactive" <?php if(request('flag')==='inactive' ): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option>
                </select>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                <button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> <span
                        class="d-none d-sm-inline"><?php echo e(__('Filter')); ?></span></button>
                <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-sm btn-outline-secondary"
                    title="<?php echo e(__('Clear')); ?>">×</a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th><?php echo e(__('Product')); ?></th>
                        <th class="d-none d-md-table-cell"><?php echo e(__('Type')); ?></th>
                        <th class="d-none d-lg-table-cell"><?php echo e(__('Category')); ?></th>
                        <th><?php echo e(__('Pricing')); ?></th>
                        <th class="d-none d-md-table-cell"><?php echo e(__('Flags')); ?></th>
                        <th class="d-none d-lg-table-cell"><?php echo e(__('Stock')); ?></th>
                        <th width="120"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?php echo e($p->name); ?></div>
                            <div class="text-muted small">SKU: <?php echo e($p->sku ?: '-'); ?></div>
                            <div class="d-md-none mt-1">
                                <span class="badge bg-secondary text-capitalize me-1"><?php echo e($p->type); ?></span>
                                <?php if($p->category): ?><span
                                    class="badge bg-light text-dark"><?php echo e($p->category->name); ?></span><?php endif; ?>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <span class="badge bg-secondary text-capitalize"><?php echo e($p->type); ?></span>
                            <span class="badge bg-info text-capitalize"><?php echo e($p->physical_type); ?></span>
                        </td>
                        <td class="d-none d-lg-table-cell"><?php echo e($p->category->name ?? '-'); ?></td>
                        <td>
                            <div class="fw-semibold"><?php echo e(number_format($p->price,2)); ?></div>
                            <?php if($p->isOnSale()): ?>
                            <div class="small"><span class="badge bg-success"><?php echo e(__('Sale')); ?></span>
                                <?php echo e(number_format($p->sale_price,2)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <div class="d-flex flex-wrap gap-1">
                                <?php if($p->is_featured): ?><span
                                    class="badge bg-warning text-dark"><?php echo e(__('Featured')); ?></span><?php endif; ?>
                                <?php if($p->is_best_seller): ?><span class="badge bg-primary"><?php echo e(__('Best')); ?></span><?php endif; ?>
                                <?php if(!$p->active): ?><span class="badge bg-danger"><?php echo e(__('Inactive')); ?></span><?php endif; ?>
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <?php if($p->type === 'variable' && $p->variations->isNotEmpty()): ?>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                                data-bs-target="#variations-<?php echo e($p->id); ?>" aria-expanded="false"
                                aria-controls="variations-<?php echo e($p->id); ?>">
                                <?php echo e(__('Show Variations')); ?>

                            </button>
                            <div class="collapse mt-2" id="variations-<?php echo e($p->id); ?>">
                                <div class="card card-body p-2">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th><?php echo e(__('SKU')); ?></th>
                                                <th><?php echo e(__('Name')); ?></th>
                                                <th><?php echo e(__('Manage Stock')); ?></th>
                                                <th><?php echo e(__('Available')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $p->variations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="small"><?php echo e($v->sku ?: '-'); ?></td>
                                                <td class="small"><?php echo e($v->name ?? '-'); ?></td>
                                                <td class="small">
                                                    <?php if($v->manage_stock): ?><?php echo e(__('Yes')); ?><?php else: ?><?php echo e(__('No')); ?><?php endif; ?></td>
                                                <td class="small">
                                                    <?php if($v->manage_stock): ?>
                                                    <span class="fw-semibold <?php echo e($apiStockVariations[$v->id]['class'] ?? ''); ?>"><?php echo e($apiStockVariations[$v->id]['available'] ?? (($v->stock_qty ?? 0)-($v->reserved_qty ?? 0))); ?></span>
                                                    <span class="text-muted small">/<?php echo e($apiStockVariations[$v->id]['stock_qty'] ?? ($v->stock_qty ?? 0)); ?></span>
                                                    <?php if(($apiStockVariations[$v->id]['badge'] ?? null)==='low'): ?> <span class="badge bg-danger"><?php echo e(__('Low')); ?></span>
                                                    <?php elseif(($apiStockVariations[$v->id]['badge'] ?? null)==='soon'): ?> <span class="badge bg-warning text-dark"><?php echo e(__('Soon')); ?></span><?php endif; ?>
                                                            <?php else: ?>
                                                            <span class="text-muted small"><?php echo e(__('N/A')); ?></span>
                                                            <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php elseif($p->manage_stock): ?>
                            <div>
                                <span class="fw-semibold <?php echo e($apiStockProducts[$p->id]['class'] ?? ''); ?>"><?php echo e($apiStockProducts[$p->id]['available'] ?? $p->availableStock()); ?></span>
                                <span class="text-muted small">/<?php echo e($apiStockProducts[$p->id]['stock_qty'] ?? ($p->stock_qty ?? 0)); ?></span>
                            </div>
                            <?php if(($apiStockProducts[$p->id]['badge'] ?? null)==='low'): ?> <span class="badge bg-danger"><?php echo e(__('Low')); ?></span>
                            <?php elseif(($apiStockProducts[$p->id]['badge'] ?? null)==='soon'): ?> <span class="badge bg-warning text-dark"><?php echo e(__('Soon')); ?></span><?php endif; ?>
                            <?php if(!empty($apiStockProducts[$p->id]['backorder'])): ?><span class="badge bg-outline-secondary border">BO</span><?php endif; ?>
                                    <?php else: ?>
                                    <span class="text-muted small"><?php echo e(__('N/A')); ?></span>
                                    <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?php echo e(route('admin.products.edit',$p)); ?>" class="btn btn-sm btn-outline-primary"
                                    title="<?php echo e(__('Edit')); ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('admin.products.destroy',$p)); ?>"
                                    class="d-inline delete-form">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Delete this product?')); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="d-lg-none mt-2">
                                <?php if($p->type === 'variable' && $p->variations->isNotEmpty()): ?>
                                <div class="small">
                                    <strong><?php echo e(__('Variations')); ?>:</strong>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php $__currentLoopData = $p->variations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="mb-1">
                                            <span class="fw-semibold"><?php echo e($v->sku ?: '-'); ?></span>
                                            —
                                            <?php if($v->manage_stock): ?>
                                            <span class="<?php echo e($apiStockVariations[$v->id]['class'] ?? ''); ?>"><?php echo e($apiStockVariations[$v->id]['available'] ?? (($v->stock_qty ?? 0)-($v->reserved_qty ?? 0))); ?></span>
                                            <small class="text-muted">/<?php echo e($apiStockVariations[$v->id]['stock_qty'] ?? ($v->stock_qty ?? 0)); ?></small>
                                            <?php else: ?>
                                            <small class="text-muted"><?php echo e(__('N/A')); ?></small>
                                            <?php endif; ?>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                                <?php else: ?>
                                <?php if($p->manage_stock): ?>
                                <small class="text-muted"><?php echo e(__('Stock')); ?>:
                                    <span class="fw-semibold <?php echo e($apiStockProducts[$p->id]['class'] ?? ''); ?>"><?php echo e($apiStockProducts[$p->id]['available'] ?? $p->availableStock()); ?></span>
                                </small>
                                <?php endif; ?>
                                <div class="d-md-none mt-1">
                                    <?php if($p->is_featured): ?><span
                                        class="badge bg-warning text-dark me-1"><?php echo e(__('Featured')); ?></span><?php endif; ?>
                                    <?php if($p->is_best_seller): ?><span
                                        class="badge bg-primary me-1"><?php echo e(__('Best')); ?></span><?php endif; ?>
                                    <?php if(!$p->active): ?><span class="badge bg-danger"><?php echo e(__('Inactive')); ?></span><?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <h5><?php echo e(__('No products found.')); ?></h5>
                                <p class="mb-3"><?php echo e(__('Start by adding your first product.')); ?></p>
                                <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> <?php echo e(__('Add Product')); ?>

                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($products->hasPages()): ?>
    <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div class="text-muted small"><?php echo e(__('Showing')); ?> <?php echo e($products->firstItem()); ?> - <?php echo e($products->lastItem()); ?>

            <?php echo e(__('of')); ?> <?php echo e($products->total()); ?></div>
        <div class="pagination-links"><?php echo e($products->links()); ?></div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/products/products/index.blade.php ENDPATH**/ ?>