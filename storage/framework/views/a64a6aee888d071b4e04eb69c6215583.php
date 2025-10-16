<?php ($langs = $blogPostLanguages ?? ($blogPostLanguages = \App\Models\Language::where('is_active',1)->orderByDesc('is_default')->get())); ?>
<?php ($defaultLang = $langs->firstWhere('is_default',1) ?? $langs->first()); ?>
<input type="hidden" id="blog-post-default-lang" value="<?php echo e($defaultLang?->code); ?>">
<?php if($errors->any()): ?>
  <div class="alert alert-danger small"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($err); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
<?php endif; ?>
<div class="card mb-4">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h5 class="mb-0"><i class="fas fa-language me-2 text-primary"></i><?php echo e(__('Post Translations')); ?></h5>
    <div class="d-flex flex-wrap gap-2">
      <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-default><?php echo e(__('Copy Default to Empty')); ?></button>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="lang-tabs-wrapper border-bottom">
      <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-3" role="tablist">
        <?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link py-1 px-3 <?php if($i===0): ?> active <?php endif; ?>" data-bs-toggle="tab" data-bs-target="#blog-post-lang-<?php echo e($lang->code); ?>" type="button" role="tab">
              <?php echo e(strtoupper($lang->code)); ?> <?php if($lang->is_default): ?><span class="badge bg-primary ms-1"><?php echo e(__('Default')); ?></span><?php endif; ?>
            </button>
          </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
    <div class="tab-content p-3 border-top">
      <?php ($titleTr = isset($post)? ($post->title_translations ?? []) : []); ?>
      <?php ($slugTr = isset($post)? ($post->slug_translations ?? []) : []); ?>
      <?php ($excerptTr = isset($post)? ($post->excerpt_translations ?? []) : []); ?>
      <?php ($bodyTr = isset($post)? ($post->body_translations ?? []) : []); ?>
      <?php ($seoTitleTr = isset($post)? ($post->seo_title_translations ?? []) : []); ?>
      <?php ($seoDescTr = isset($post)? ($post->seo_description_translations ?? []) : []); ?>
      <?php ($seoTagsTr = isset($post)? ($post->seo_tags_translations ?? []) : []); ?>
      <?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php ($code = $lang->code); ?>
        <div class="tab-pane fade <?php if($i===0): ?> show active <?php endif; ?>" id="blog-post-lang-<?php echo e($code); ?>" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label small fw-semibold"><?php echo e(__('Title')); ?></label>
              <input name="title[<?php echo e($code); ?>]" value="<?php echo e(old('title.'.$code, $titleTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('title') : ''))); ?>" class="form-control form-control-sm" <?php if($lang->is_default): ?> required <?php endif; ?> placeholder="<?php echo e(__('Post title')); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold"><?php echo e(__('Slug')); ?></label>
              <input name="slug[<?php echo e($code); ?>]" value="<?php echo e(old('slug.'.$code, $slugTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('slug') : ''))); ?>" class="form-control form-control-sm" placeholder="auto" readonly>
              <?php if($lang->is_default): ?><div class="form-text small"><?php echo e(__('Auto from title')); ?></div><?php endif; ?>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                <span><?php echo e(__('Excerpt')); ?> <small class="text-muted" data-counter-display="excerpt-<?php echo e($code); ?>">0/300</small></span>
                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-post" data-lang="<?php echo e($code); ?>" data-target="excerpt" data-loading="0"><i class="fas fa-magic"></i> AI</button>
              </label>
              <textarea name="excerpt[<?php echo e($code); ?>]" rows="2" class="form-control form-control-sm js-post-excerpt" data-counter="excerpt-<?php echo e($code); ?>" data-max="300" placeholder="<?php echo e(__('Short summary (<=300 chars)')); ?>"><?php echo e(old('excerpt.'.$code, $excerptTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('excerpt') : ''))); ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                <span><?php echo e(__('Body')); ?></span>
                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-post" data-lang="<?php echo e($code); ?>" data-target="body" data-loading="0"><i class="fas fa-magic"></i> AI</button>
              </label>
              <textarea name="body[<?php echo e($code); ?>]" rows="10" class="form-control form-control-sm js-post-body" placeholder="<?php echo e(__('Main article content')); ?>"><?php echo e(old('body.'.$code, $bodyTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('body') : ''))); ?></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold"><?php echo e(__('SEO Title')); ?></label>
              <input name="seo_title[<?php echo e($code); ?>]" value="<?php echo e(old('seo_title.'.$code, $seoTitleTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('seo_title') : ''))); ?>" class="form-control form-control-sm" placeholder="<?php echo e(__('Optional SEO title')); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                <span><?php echo e(__('SEO Description')); ?> <small class="text-muted" data-counter-display="seodesc-<?php echo e($code); ?>">0/160</small></span>
                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-post-seo" data-lang="<?php echo e($code); ?>" data-loading="0"><i class="fas fa-magic"></i> AI</button>
              </label>
              <input name="seo_description[<?php echo e($code); ?>]" value="<?php echo e(old('seo_description.'.$code, $seoDescTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('seo_description') : ''))); ?>" class="form-control form-control-sm js-post-seo-description" data-counter="seodesc-<?php echo e($code); ?>" data-max="160" placeholder="<?php echo e(__('Meta description')); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold"><?php echo e(__('SEO Tags')); ?></label>
              <input name="seo_tags[<?php echo e($code); ?>]" value="<?php echo e(old('seo_tags.'.$code, $seoTagsTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('seo_tags') : ''))); ?>" class="form-control form-control-sm" placeholder="tag1,tag2">
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</div>
<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/lang-tabs.css')); ?>">
<?php $__env->stopPush(); ?>
<div class="card mb-4">
  <div class="card-header"><h5 class="mb-0"><i class="fas fa-cogs me-2 text-primary"></i><?php echo e(__('Post Settings')); ?></h5></div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label small fw-semibold"><?php echo e(__('Category')); ?></label>
        <select name="category_id" class="form-select form-select-sm">
          <option value="">--</option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if(old('category_id', isset($post)?$post->category_id:null)==$c->id): ?> selected <?php endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold"><?php echo e(__('Tags')); ?></label>
        <div class="border rounded p-2 h-120 overflow-auto small">
          <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($checked = in_array($t->id, old('tags', isset($post)? $post->tags->pluck('id')->all(): []))); ?>
            <label class="d-block"><input type="checkbox" name="tags[]" value="<?php echo e($t->id); ?>" <?php if($checked): ?> checked <?php endif; ?>> <?php echo e($t->name); ?></label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold"><?php echo e(__('Featured Image')); ?></label>
        <div class="border rounded p-2 text-center h-120 position-relative bg-light" id="featPreview">
          <?php if(isset($post) && $post->featured_image): ?>
            <img src="<?php echo e(asset('storage/'.$post->featured_image)); ?>" class="obj-cover w-100 h-100">
          <?php else: ?>
            <div class="small text-muted pt-4"><?php echo e(__('Choose Image')); ?></div>
          <?php endif; ?>
        </div>
        <input type="hidden" name="featured_image_path" id="featured_image_path" value="<?php echo e(isset($post)&&$post->featured_image ? asset('storage/'.$post->featured_image) : ''); ?>">
        <small class="text-muted d-block mt-1"><?php echo e(__('Click box to pick / upload')); ?></small>
      </div>
    </div>
  </div>
</div>
<?php if(isset($post)): ?>
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="fas fa-bolt me-2 text-primary"></i><?php echo e(__('Publishing')); ?></h6>
      <form method="POST" action="<?php echo e(route('admin.blog.posts.publish',$post)); ?>" class="d-inline"><?php echo csrf_field(); ?> <button type="submit" class="btn btn-sm btn-outline-secondary"><?php if($post->published): ?> <?php echo e(__('Unpublish')); ?> <?php else: ?> <?php echo e(__('Publish')); ?> <?php endif; ?></button></form>
    </div>
    <div class="card-body small text-muted">
      <?php if($post->published): ?>
        <?php echo e(__('Published at:')); ?> <?php echo e($post->published_at); ?>

      <?php else: ?>
        <?php echo e(__('Not published yet.')); ?>

      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/blog/posts/_form.blade.php ENDPATH**/ ?>