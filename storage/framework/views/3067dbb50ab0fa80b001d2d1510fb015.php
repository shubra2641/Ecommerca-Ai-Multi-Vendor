<?php $__env->startSection('title', __('Gallery')); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Gallery')); ?></h1>
        <p class="page-description"><?php echo e(__('Manage images, SEO data, tags and logo usage')); ?></p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('admin.gallery.create')); ?>" class="btn btn-primary">
            <i class="fas fa-upload"></i>
            <?php echo e(__('Upload')); ?>

        </a>
        <button type="button" class="btn btn-outline-secondary" id="multiUploadBtn">
            <i class="fas fa-layer-group"></i>
            <?php echo e(__('Multi Upload')); ?>

        </button>
    </div>
</div>

<form method="GET" action="<?php echo e(route('admin.gallery.index')); ?>" class="card card-body mb-3 p-3 shadow-sm">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label"><?php echo app('translator')->get('Search'); ?></label>
            <input type="text" name="q" value="<?php echo e($q ?? ''); ?>" class="form-control" placeholder="<?php echo app('translator')->get('Title / description / tag'); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label"><?php echo app('translator')->get('Tag'); ?></label>
            <select name="tag" class="form-select">
                <option value=""><?php echo app('translator')->get('All'); ?></option>
                <?php $__currentLoopData = ($distinctTags ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($t); ?>" <?php if(($tag ?? '') === $t): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div>
                <button class="btn btn-outline-primary me-1"><i class="fas fa-search"></i> <?php echo app('translator')->get('Filter'); ?></button>
                <a href="<?php echo e(route('admin.gallery.index')); ?>" class="btn btn-outline-secondary"><?php echo app('translator')->get('Reset'); ?></a>
            </div>
        </div>
    </div>
</form>
<?php if(!$images->count()): ?>
    <div class="alert alert-info mb-0"><?php echo app('translator')->get('No images yet.'); ?></div>
<?php else: ?>
<div class="row g-3">
<?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-3 col-sm-4 col-6">
        <div class="card h-100 shadow-sm">
            <div class="ratio ratio-1x1 bg-light">
                <img src="<?php echo e($img->thumbnail_path ? asset('storage/'.$img->thumbnail_path) : ($img->webp_path ? asset('storage/'.$img->webp_path) : asset('storage/'.$img->original_path))); ?>" alt="<?php echo e($img->alt); ?>" class="img-fluid rounded-top obj-cover">
            </div>
            <div class="card-body p-2">
                <div class="small fw-semibold text-truncate" title="<?php echo e($img->title); ?>"><?php echo e($img->title ?? __('(No title)')); ?></div>
                <div class="text-muted small text-truncate" title="<?php echo e($img->description); ?>"><?php echo e($img->description ? Str::limit($img->description, 40) : ''); ?></div>
                <?php if($img->tagsList()): ?>
                <div class="mt-1 d-flex flex-wrap gap-1">
                    <?php $__currentLoopData = $img->tagsList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-light text-secondary border fw-normal small-badge"><?php echo e($tg); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer d-flex justify-content-between gap-1 p-2">
                <div class="btn-group" role="group">
                    <a href="<?php echo e(route('admin.gallery.edit', $img)); ?>" class="btn btn-sm btn-outline-primary" title="<?php echo app('translator')->get('Edit'); ?>"><i class="fas fa-edit"></i></a>
                    <form action="<?php echo e(route('admin.gallery.use-as-logo', $img)); ?>" method="POST" class="js-confirm" data-confirm="<?php echo app('translator')->get('Use this as logo?'); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-outline-success" title="<?php echo app('translator')->get('Use as Logo'); ?>"><i class="fas fa-check-circle"></i></button>
                    </form>
                    <?php if(!($gallerySettingLogo && ($gallerySettingLogo === $img->webp_path || $gallerySettingLogo === $img->original_path))): ?>
                        <form action="<?php echo e(route('admin.gallery.destroy', $img)); ?>" method="POST" class="js-confirm" data-confirm="<?php echo app('translator')->get('Delete image?'); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger" title="<?php echo app('translator')->get('Delete'); ?>"><i class="fas fa-trash"></i></button>
                        </form>
                    <?php else: ?>
                        <form action="<?php echo e(route('admin.gallery.destroy', [$img, 'force' => 1])); ?>" method="POST" class="js-confirm" data-confirm="<?php echo app('translator')->get('This image is used as logo. Delete anyway?'); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger" title="<?php echo app('translator')->get('Force Delete Logo Image'); ?>"><i class="fas fa-exclamation-triangle"></i></button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="mt-3"><?php echo e($images->links()); ?></div>
<?php endif; ?>
<!-- Multi Upload Modal -->
<div class="modal fade" id="multiUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i><?php echo app('translator')->get('Multi Upload'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.gallery.store')); ?>" method="POST" enctype="multipart/form-data" id="multiUploadForm">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="mb-3">
                        <label class="form-label"><?php echo app('translator')->get('Images'); ?></label>
                        <div id="dropzone" class="border border-2 border-dashed rounded p-4 text-center bg-light cursor-pointer">
                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                <p class="mb-1 fw-semibold"><?php echo app('translator')->get('Drag & Drop or Click to Select'); ?></p>
                                <p class="text-muted small mb-0"><?php echo app('translator')->get('Up to 15 images, each max 4MB'); ?></p>
                                <input type="file" name="images[]" id="imagesInput" multiple accept="image/*" class="d-none">
                        </div>
                        <div id="previewList" class="row g-2 mt-3"></div>
                </div>
                <div class="row g-2">
                        <div class="col-md-4">
                                <label class="form-label"><?php echo app('translator')->get('SEO Title (applied to all)'); ?></label>
                                <input type="text" name="title" class="form-control" maxlength="150">
                        </div>
                        <div class="col-md-4">
                                <label class="form-label"><?php echo app('translator')->get('Tags'); ?></label>
                                <input type="text" name="tags" class="form-control" maxlength="255" placeholder="tag1, tag2">
                        </div>
                        <div class="col-md-4">
                                <label class="form-label"><?php echo app('translator')->get('ALT'); ?></label>
                                <input type="text" name="alt" class="form-control" maxlength="150">
                        </div>
                </div>
                <div class="mt-3">
                        <label class="form-label"><?php echo app('translator')->get('SEO Description (applied to all)'); ?></label>
                        <textarea name="description" class="form-control" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                <button type="submit" class="btn btn-primary" id="uploadBtn" disabled><?php echo app('translator')->get('Upload'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/gallery/index.blade.php ENDPATH**/ ?>