<div class="form-toolbar sticky-top bg-body pb-3 mb-3 border-bottom z-6 shadow-sm toolbar-sticky-top">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="fw-semibold text-muted small"><?php echo e(__('Category Form')); ?></div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-collapse-all><?php echo e(__('Collapse All')); ?></button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-expand-all><?php echo e(__('Expand All')); ?></button>
        </div>
    </div>
    </div>
</div>
<div class="toolbar-spacer mb-3"></div>

<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted"><i class="fas fa-folder-open me-1 text-primary"></i><?php echo e(__('Basic Info')); ?></h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Parent')); ?></label>
                <select name="parent_id" class="form-select">
                    <option value="">-- <?php echo e(__('None')); ?> --</option>
                    <?php $__currentLoopData = $parents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php if(old('parent_id',$model->parent_id ?? null)==$p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Name (Default Locale)')); ?></label>
                <input name="name" value="<?php echo e(old('name',$model->name ?? '')); ?>" class="form-control">
                <div class="form-text small"><?php echo e(__('Base name used as fallback when translation missing.')); ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Slug')); ?></label>
                <input name="slug" value="<?php echo e(old('slug',$model->slug ?? '')); ?>" class="form-control" readonly>
                <div class="form-text small"><?php echo e(__('Slug is auto-generated from the name.')); ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Position')); ?></label>
                <input type="number" name="position" value="<?php echo e(old('position',$model->position ?? 0)); ?>" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span><?php echo e(__('Description (Base)')); ?></span>
                    <button type="button" class="btn btn-sm btn-outline-primary js-ai-generate-category" data-target-prefix="base" data-loading="0">
                        <i class="fas fa-magic me-1"></i><?php echo e(__('AI Generate')); ?>

                    </button>
                </label>
                <textarea name="description" rows="3" class="form-control js-cat-description-base"><?php echo e(old('description',$model->description ?? '')); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Commission Rate (%)')); ?></label>
                <input type="number" step="0.01" name="commission_rate" value="<?php echo e(old('commission_rate',$model->commission_rate ?? '')); ?>" class="form-control" placeholder="5">
                <div class="form-text small"><?php echo e(__('Leave empty to fallback to global rate.')); ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Image')); ?></label>
                <div class="input-group">
                    <input name="image" value="<?php echo e(old('image',$model->image ?? '')); ?>" class="form-control" placeholder="/storage/uploads/cat.jpg">
                    <button type="button" class="btn btn-outline-secondary" data-open-media="image"><i class="fas fa-folder-open"></i></button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Active')); ?></label>
                <select name="active" class="form-select">
                    <option value="1" <?php if(old('active',$model->active ?? 1)==1): echo 'selected'; endif; ?>><?php echo e(__('Yes')); ?></option>
                    <option value="0" <?php if(old('active',$model->active ?? 1)==0): echo 'selected'; endif; ?>><?php echo e(__('No')); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>

<?php ($langs = $activeLanguages ?? (\App\Models\Language::where('is_active',1)->orderByDesc('is_default')->get())); ?>
<div class="inner-section mt-4" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted"><i class="fas fa-language me-1 text-primary"></i><?php echo e(__('Translations')); ?></h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="lang-tabs-wrapper" data-lang-tabs-variant="primary">
            <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-1" role="tablist">
                <?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1 px-3 <?php if($i===0): ?> active <?php endif; ?>" data-bs-toggle="tab" data-bs-target="#pcat-lang-<?php echo e($lang->code); ?>" type="button" role="tab"><?php echo e(strtoupper($lang->code)); ?></button>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div class="tab-content border rounded-bottom p-3 mt-2">
            <?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="tab-pane fade <?php if($i===0): ?> show active <?php endif; ?>" id="pcat-lang-<?php echo e($lang->code); ?>" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small mb-1"><?php echo e(__('Name')); ?></label>
                            <input name="name_i18n[<?php echo e($lang->code); ?>]" value="<?php echo e(old('name_i18n.'.$lang->code, $model->name_translations[$lang->code] ?? '')); ?>" class="form-control form-control-sm" placeholder="<?php echo e(__('Translated name')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1 d-flex justify-content-between align-items-center">
                                <span><?php echo e(__('Description')); ?></span>
                                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-category-i18n" data-lang="<?php echo e($lang->code); ?>" data-loading="0"><i class="fas fa-magic"></i> AI</button>
                            </label>
                            <textarea name="description_i18n[<?php echo e($lang->code); ?>]" rows="2" class="form-control form-control-sm js-cat-description-i18n" placeholder="<?php echo e(__('Translated description')); ?>"><?php echo e(old('description_i18n.'.$lang->code, $model->description_translations[$lang->code] ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<div class="inner-section mt-4" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted"><i class="fas fa-search me-1 text-primary"></i><?php echo e(__('SEO')); ?></h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('SEO Title')); ?></label>
                <input name="seo_title" value="<?php echo e(old('seo_title',$model->seo_title ?? '')); ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('SEO Keywords (comma)')); ?></label>
                <input name="seo_keywords" value="<?php echo e(old('seo_keywords',$model->seo_keywords ?? '')); ?>" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span><?php echo e(__('SEO Description')); ?></span>
                    <button type="button" class="btn btn-sm btn-outline-primary js-ai-generate-category" data-target-prefix="seo" data-loading="0">
                        <i class="fas fa-magic me-1"></i><?php echo e(__('AI Generate')); ?>

                    </button>
                </label>
                <textarea name="seo_description" rows="3" class="form-control js-cat-description-seo"><?php echo e(old('seo_description',$model->seo_description ?? '')); ?></textarea>
            </div>
        </div>
    </div>
</div>


<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/lang-tabs.css')); ?>">
<?php $__env->stopPush(); ?>
<?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/categories/_form.blade.php ENDPATH**/ ?>