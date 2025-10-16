<?php $__env->startSection('title', __('Footer Settings')); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
  <h1 class="h4 mb-3"><?php echo e($footerSettingsTitle ?? __('Footer Settings')); ?></h1>
  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if(session('info')): ?>
    <div class="alert alert-info"><?php echo e(session('info')); ?></div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
  <?php endif; ?>
  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <strong><?php echo e(__('Please fix the following errors:')); ?></strong>
      <ul class="mb-0 small">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($err); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>
  <form method="POST" action="<?php echo e(route('admin.footer-settings.update')); ?>" enctype="multipart/form-data" class="card p-3 shadow-sm">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <h5 class="mt-2"><?php echo e(__('Sections Visibility')); ?></h5>
    <div class="row g-2 mb-3">
      <?php $__currentLoopData = ['support_bar'=>'Support Bar','apps'=>'Apps / Downloads','social'=>'Social Links','pages'=>'Pages','payments'=>'Payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-md-3">
          <label class="form-check">
            <input type="checkbox" class="form-check-input" name="sections[<?php echo e($k); ?>]" value="1" <?php if($sections[$k]): echo 'checked'; endif; ?>>
            <span class="form-check-label"><?php echo e(__($lbl)); ?></span>
          </label>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <hr>
    <h5><?php echo e(__('Support Text (multilingual)')); ?></h5>
    <div class="accordion" id="supportTexts">
      <?php $__currentLoopData = $activeLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="accordion-item mb-2">
          <h2 class="accordion-header" id="heading-<?php echo e($lang->code); ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo e($lang->code); ?>"><?php echo e(strtoupper($lang->code)); ?></button>
          </h2>
          <div id="collapse-<?php echo e($lang->code); ?>" class="accordion-collapse collapse" data-bs-parent="#supportTexts">
            <div class="accordion-body">
              <div class="mb-2">
                <label class="form-label"><?php echo e(__('Support Heading')); ?></label>
                <input name="footer_support_heading[<?php echo e($lang->code); ?>]" value="<?php echo e(old('footer_support_heading.'.$lang->code, $setting->footer_support_heading[$lang->code] ?? '')); ?>" class="form-control" maxlength="120">
              </div>
              <div class="mb-2">
                <label class="form-label"><?php echo e(__('Support Subheading')); ?></label>
                <input name="footer_support_subheading[<?php echo e($lang->code); ?>]" value="<?php echo e(old('footer_support_subheading.'.$lang->code, $setting->footer_support_subheading[$lang->code] ?? '')); ?>" class="form-control" maxlength="180">
              </div>
              <div class="mb-2">
                <label class="form-label"><?php echo e(__('Rights Line')); ?></label>
                <input name="rights_i18n[<?php echo e($lang->code); ?>]" value="<?php echo e(old('rights_i18n.'.$lang->code, $setting->rights_i18n[$lang->code] ?? ($lang->is_default ? $setting->rights : ''))); ?>" class="form-control" maxlength="255">
              </div>
              <hr>
              <div class="row g-2">
                <div class="col-md-6 mb-2">
                  <label class="form-label"><?php echo e(__('Help Center Label')); ?></label>
                  <input name="footer_labels[help_center][<?php echo e($lang->code); ?>]" value="<?php echo e(old('footer_labels.help_center.'.$lang->code, $setting->footer_labels['help_center'][$lang->code] ?? '')); ?>" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label"><?php echo e(__('Email Support Label')); ?></label>
                  <input name="footer_labels[email_support][<?php echo e($lang->code); ?>]" value="<?php echo e(old('footer_labels.email_support.'.$lang->code, $setting->footer_labels['email_support'][$lang->code] ?? '')); ?>" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label"><?php echo e(__('Phone Support Label')); ?></label>
                  <input name="footer_labels[phone_support][<?php echo e($lang->code); ?>]" value="<?php echo e(old('footer_labels.phone_support.'.$lang->code, $setting->footer_labels['phone_support'][$lang->code] ?? '')); ?>" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label"><?php echo e(__('Apps Section Heading')); ?></label>
                  <input name="footer_labels[apps_heading][<?php echo e($lang->code); ?>]" value="<?php echo e(old('footer_labels.apps_heading.'.$lang->code, $setting->footer_labels['apps_heading'][$lang->code] ?? '')); ?>" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label"><?php echo e(__('Social Section Heading')); ?></label>
                  <input name="footer_labels[social_heading][<?php echo e($lang->code); ?>]" value="<?php echo e(old('footer_labels.social_heading.'.$lang->code, $setting->footer_labels['social_heading'][$lang->code] ?? '')); ?>" class="form-control" maxlength="120">
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <hr>
    <h5><?php echo e(__('App Download Badges')); ?></h5>
    <div class="row">
      <?php $__currentLoopData = $appLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform=>$link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4 mb-3">
          <div class="border rounded p-2 h-100">
            <h6 class="mb-2"><?php echo e(ucfirst($platform)); ?></h6>
            <label class="form-check mb-2">
              <input type="checkbox" class="form-check-input" name="app_links[<?php echo e($platform); ?>][enabled]" value="1" <?php if($link['enabled']): echo 'checked'; endif; ?>>
              <span class="form-check-label"><?php echo e(__('Enabled')); ?></span>
            </label>
            <div class="mb-2">
              <label class="form-label">URL</label>
              <input type="url" class="form-control" name="app_links[<?php echo e($platform); ?>][url]" value="<?php echo e($link['url']); ?>" placeholder="https://...">
            </div>
            <div class="mb-2">
              <label class="form-label"><?php echo e(__('Order')); ?></label>
              <input type="number" class="form-control" name="app_links[<?php echo e($platform); ?>][order]" value="<?php echo e($link['order']); ?>" min="0" max="50">
            </div>
            <div class="mb-2">
              <label class="form-label"><?php echo e(__('Badge Image (max 180x54 suggested)')); ?></label>
              <?php if(!empty($link['image'])): ?>
                <div class="mb-1"><img src="<?php echo e(asset('storage/'.$link['image'])); ?>" alt="badge" class="img-badge-thumb"></div>
                <input type="hidden" name="app_links[<?php echo e($platform); ?>][existing_image]" value="<?php echo e($link['image']); ?>">
              <?php endif; ?>
              <input type="file" class="form-control" name="app_links[<?php echo e($platform); ?>][image]" accept="image/*">
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <hr>
    <h5><?php echo e(__('Footer Pages')); ?></h5>
    <p class="text-muted small"><?php echo e(__('Select pages to display (max 8). Order will follow selection order.')); ?></p>
    <select name="footer_pages[]" class="form-select" multiple size="8">
      <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($p->id); ?>" <?php if(in_array($p->id, $setting->footer_pages ?? [])): echo 'selected'; endif; ?>><?php echo e($footerPageTitles[$p->id] ?? ('#'.$p->id)); ?> <?php if($p->identifier): ?> (<?php echo e($p->identifier); ?>) <?php endif; ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <hr>
    <h5><?php echo e(__('Payment Methods (one per line, max 6 shown)')); ?></h5>
    <textarea name="footer_payment_methods" class="form-control" rows="3" placeholder="VISA\nMC\nPAYPAL"><?php echo e(implode("\n", $setting->footer_payment_methods ?? [])); ?></textarea>

    <div class="mt-4">
      <button class="btn btn-primary"><?php echo e(__('Save Footer Settings')); ?></button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/footer/settings.blade.php ENDPATH**/ ?>