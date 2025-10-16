<?php $__env->startSection('title', __('Posts')); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
<li class="breadcrumb-item active"><?php echo e(__('Posts')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Posts')); ?></h1>
        <p class="page-description"><?php echo e(__('Manage blog posts and content')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.blog.posts.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <?php echo e(__('Create Post')); ?>

        </a>
    </div>
</div>

<!-- Filters -->
<form class="card card-body mb-3 p-3 shadow-sm" method="GET">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('Search')); ?></label>
            <input type="text" name="q" value="<?php echo e($q); ?>" class="form-control" placeholder="<?php echo e(__('Search')); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('Category')); ?></label>
            <select name="category_id" class="form-select">
                <option value=""><?php echo e(__('All')); ?></option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php if($categoryId==$c->id): ?> selected <?php endif; ?>><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('Published')); ?></label>
            <select name="published" class="form-select">
                <option value=""><?php echo e(__('All')); ?></option>
                <option value="1" <?php if($published==='1'): ?> selected <?php endif; ?>><?php echo e(__('Yes')); ?></option>
                <option value="0" <?php if($published==='0'): ?> selected <?php endif; ?>><?php echo e(__('No')); ?></option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div>
                <button class="btn btn-outline-primary me-1">
                    <i class="fas fa-search"></i> <?php echo e(__('Filter')); ?>

                </button>
                <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="btn btn-outline-secondary"><?php echo e(__('Reset')); ?></a>
            </div>
        </div>
    </div>
</form>

<!-- Posts Table -->
<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title"><?php echo e(__('Posts List')); ?></h3>
    </div>
    <div class="card-body">
        <?php if($posts->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Title')); ?></th>
                            <th><?php echo e(__('Category')); ?></th>
                            <th><?php echo e(__('Published')); ?></th>
                            <th><?php echo e(__('Updated')); ?></th>
                            <th width="150"><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?php echo e($post->title); ?></div>
                                <div class="text-muted small">/<?php echo e($post->slug); ?></div>
                            </td>
                            <td><?php echo e($post->category->name ?? '-'); ?></td>
                            <td>
                                <?php if($post->published): ?>
                                    <span class="badge bg-success"><?php echo e(__('Yes')); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(__('No')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($post->updated_at->diffForHumans()); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo e(route('admin.blog.posts.edit',$post)); ?>" class="btn btn-sm btn-outline-secondary" title="<?php echo e(__('Edit')); ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('admin.blog.posts.destroy',$post)); ?>" class="d-inline delete-form">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Delete?')); ?>">
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="pagination-info">
                    <?php echo e(__('Showing')); ?> <?php echo e($posts->firstItem()); ?> <?php echo e(__('to')); ?> <?php echo e($posts->lastItem()); ?> 
                    <?php echo e(__('of')); ?> <?php echo e($posts->total()); ?> <?php echo e(__('results')); ?>

                </div>
                <?php echo e($posts->withQueryString()->links()); ?>

            </div>
        <?php else: ?>
            <div class="empty-state text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h3><?php echo e(__('No Posts Found')); ?></h3>
                <p class="text-muted"><?php echo e(__('No posts match your current filters. Try adjusting your search criteria.')); ?></p>
                <a href="<?php echo e(route('admin.blog.posts.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <?php echo e(__('Add First Post')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/blog/posts/index.blade.php ENDPATH**/ ?>