

<?php $__env->startSection('title', __('System Settings')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php echo $__env->make('admin.partials.page-header', [
        'title' => __('System Settings'),
        'subtitle' => __('Manage system configuration and preferences'),
        'actions' => '<button type="button" class="btn btn-outline-primary js-refresh-system" data-action="refresh-system-info"><i class="fas fa-sync-alt me-1"></i> '.e(__('Refresh Info')).'</button>'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row">
        <!-- General Settings -->
        <div class="col-lg-8">
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-cog text-primary"></i>
                    <h5 class="card-title mb-0"><?php echo e(__('General Settings')); ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" class="settings-form"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label"><?php echo e(__('Site Name')); ?></label>
                                    <input type="text" id="site_name" name="site_name"
                                        class="form-control <?php $__errorArgs = ['site_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('site_name', $setting->site_name ?? '')); ?>">
                                    <?php $__errorArgs = ['site_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label"><?php echo e(__('Logo')); ?></label>
                                    <input type="file" id="logo" name="logo" class="form-control">
                                    <?php if(!empty($setting->logo)): ?>
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <img src="<?php echo e(asset('storage/'.$setting->logo)); ?>" alt="Logo"
                                            class="img-thumbnail" style="max-height: 60px;">
                                        <form action="<?php echo e(route('admin.settings.logo.delete')); ?>" method="POST" class="js-confirm" data-confirm="<?php echo e(__('Delete logo?')); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit"><i
                                                    class="fas fa-trash me-1"></i> <?php echo e(__('Delete Logo')); ?></button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                    <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="seo_description" class="form-label"><?php echo e(__('SEO Description')); ?></label>
                                    <textarea id="seo_description" name="seo_description" rows="3"
                                        class="form-control <?php $__errorArgs = ['seo_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('seo_description', $setting->seo_description ?? '')); ?></textarea>
                                    <?php $__errorArgs = ['seo_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label"><?php echo e(__('Contact Email')); ?></label>
                                    <input type="email" id="contact_email" name="contact_email"
                                        class="form-control <?php $__errorArgs = ['contact_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('contact_email', $setting->contact_email ?? '')); ?>">
                                    <?php $__errorArgs = ['contact_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label"><?php echo e(__('Contact Phone')); ?></label>
                                    <input type="text" id="contact_phone" name="contact_phone"
                                        class="form-control <?php $__errorArgs = ['contact_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('contact_phone', $setting->contact_phone ?? '')); ?>">
                                    <?php $__errorArgs = ['contact_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        <!-- Legacy social media fields removed. Manage links via Social Links section. -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="custom_css" class="form-label"><?php echo e(__('Custom CSS')); ?></label>
                                    <textarea id="custom_css" name="custom_css" rows="4"
                                        class="form-control <?php $__errorArgs = ['custom_css'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('custom_css', $setting->custom_css ?? '')); ?></textarea>
                                    <?php $__errorArgs = ['custom_css'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="custom_js" class="form-label"><?php echo e(__('Custom JS')); ?></label>
                                    <textarea id="custom_js" name="custom_js" rows="4"
                                        class="form-control <?php $__errorArgs = ['custom_js'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('custom_js', $setting->custom_js ?? '')); ?></textarea>
                                    <?php $__errorArgs = ['custom_js'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="rights" class="form-label"><?php echo e(__('Footer Rights Text')); ?></label>
                                    <input type="text" id="rights" name="rights" maxlength="255"
                                        class="form-control <?php $__errorArgs = ['rights'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('rights', $setting->rights ?? '')); ?>"
                                        placeholder="© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. <?php echo e(__('All rights reserved.')); ?>">
                                    <small class="form-text text-muted"><?php echo e(__('Shown in the site footer. Basic text only.')); ?></small>
                                    <?php $__errorArgs = ['rights'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="app_timezone" class="form-label"><?php echo e(__('Timezone')); ?></label>
                                    <select id="app_timezone" name="app_timezone"
                                        class="form-control <?php $__errorArgs = ['app_timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="UTC" <?php echo e(config('app.timezone') === 'UTC' ? 'selected' : ''); ?>>UTC
                                    </option>
                                    <option value="America/New_York"
                                        <?php echo e(config('app.timezone') === 'America/New_York' ? 'selected' : ''); ?>>Eastern
                                        Time (UTC-5)</option>
                                    <option value="America/Chicago"
                                        <?php echo e(config('app.timezone') === 'America/Chicago' ? 'selected' : ''); ?>>Central
                                        Time (UTC-6)</option>
                                    <option value="America/Los_Angeles"
                                        <?php echo e(config('app.timezone') === 'America/Los_Angeles' ? 'selected' : ''); ?>>Pacific
                                        Time (UTC-8)</option>
                                    <option value="Europe/London"
                                        <?php echo e(config('app.timezone') === 'Europe/London' ? 'selected' : ''); ?>>London
                                        (UTC+0)</option>
                                    <option value="Europe/Paris"
                                        <?php echo e(config('app.timezone') === 'Europe/Paris' ? 'selected' : ''); ?>>Paris (UTC+1)
                                    </option>
                                    <option value="Asia/Dubai"
                                        <?php echo e(config('app.timezone') === 'Asia/Dubai' ? 'selected' : ''); ?>>Dubai (UTC+4)
                                    </option>
                                    <option value="Asia/Riyadh"
                                        <?php echo e(config('app.timezone') === 'Asia/Riyadh' ? 'selected' : ''); ?>>Riyadh (UTC+3)
                                    </option>
                                    <option value="Asia/Cairo"
                                        <?php echo e(config('app.timezone') === 'Asia/Cairo' ? 'selected' : ''); ?>>Cairo (UTC+2)
                                    </option>
                                </select>
                                <?php $__errorArgs = ['app_timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="app_locale" class="form-label"><?php echo e(__('Default Language')); ?></label>
                                    <select id="app_locale" name="app_locale"
                                        class="form-control <?php $__errorArgs = ['app_locale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="en" <?php echo e(config('app.locale') === 'en' ? 'selected' : ''); ?>>
                                            <?php echo e(__('English')); ?>

                                        </option>
                                        <option value="ar" <?php echo e(config('app.locale') === 'ar' ? 'selected' : ''); ?>>
                                            <?php echo e(__('Arabic')); ?>

                                        </option>
                                    </select>
                                    <?php $__errorArgs = ['app_locale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="font_family" class="form-label"><?php echo e(__('Font Family')); ?></label>
                                    <select id="font_family" name="font_family" class="form-control js-preview-font <?php $__errorArgs = ['font_family'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <?php $__currentLoopData = ($profileAvailableFonts ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $font): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($font); ?>" <?php echo e(old('font_family', $setting->font_family ?? 'Inter') === $font ? 'selected' : ''); ?>><?php echo e($font); ?><?php if($font==='Inter'): ?> (<?php echo e(__('Default')); ?>)<?php endif; ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <small class="form-text text-muted"><?php echo e(__('Only locally bundled fonts are listed to ensure CSP & SRI compliance.')); ?></small>
                                    <input type="hidden" id="current_font_loaded"
                                        value="<?php echo e(old('font_family', $setting->font_family ?? 'Inter')); ?>">
                                    <?php $__errorArgs = ['font_family'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="font-preview-container envato-hidden">
                                <label class="form-label"><?php echo e(__('Font Preview')); ?></label>
                                <div class="font-preview-text" id="fontPreview">
                                    <p data-sample="label" class="mb-2 fs-16 fw-600">
                                        <?php echo e($setting->font_family ?? 'Inter'); ?> - <?php echo e(__('Font Preview')); ?></p>
                                    <p data-sample="latin" class="mb-1 fs-14">The quick brown fox
                                        jumps over the lazy dog 1234567890 !?&amp;*</p>
                                    <p data-sample="arabic" class="mb-0 fs-14">نص عربي للتجربة يظهر
                                        تنسيق الخط واختبار الحروف الموسعة</p>
                                </div>
                                <small
                                    class="text-muted d-block mt-2"><?php echo e(__('Preview only. Click Save Settings to apply site-wide.')); ?></small>
                            </div>
                        </div>
                    </div>

                    <hr />
                    <h4 class="mt-4"><?php echo e(__('AI Assistant')); ?></h4>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group form-check mt-2">
                                <input type="hidden" name="ai_enabled" value="0">
                                <input type="checkbox" id="ai_enabled" name="ai_enabled" value="1" class="form-check-input" <?php echo e(old('ai_enabled', $setting->ai_enabled ?? false) ? 'checked' : ''); ?>>
                                <label for="ai_enabled" class="form-check-label"><?php echo e(__('Enable AI')); ?></label>
                                <div class="form-text small"><?php echo e(__('Toggle product description & SEO generation.')); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ai_provider" class="form-label"><?php echo e(__('Provider')); ?></label>
                                <select id="ai_provider" name="ai_provider" class="form-select">
                                    <option value="" <?php if(old('ai_provider',$setting->ai_provider ?? '')===''): echo 'selected'; endif; ?>><?php echo e(__('Select')); ?></option>
                                    <option value="openai" <?php if(old('ai_provider',$setting->ai_provider ?? '')==='openai'): echo 'selected'; endif; ?>>OpenAI</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ai_openai_api_key" class="form-label"><?php echo e(__('OpenAI API Key')); ?></label>
                                <input type="text" id="ai_openai_api_key" name="ai_openai_api_key" class="form-control" value="<?php echo e(old('ai_openai_api_key', $setting->ai_openai_api_key ? '••••••••' : '')); ?>" placeholder="sk-...">
                                <div class="form-text small"><?php echo e(__('Stored encrypted. Never expose to vendors.')); ?></div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <h4 class="mt-3"><?php echo e(__('Withdrawal Settings')); ?></h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="min_withdrawal_amount" class="form-label"><?php echo e(__('Minimum Withdrawal Amount')); ?></label>
                                <input type="number" step="0.01" id="min_withdrawal_amount" name="min_withdrawal_amount" class="form-control" value="<?php echo e(old('min_withdrawal_amount', $setting->min_withdrawal_amount ?? 10)); ?>">
                                <small class="form-text text-muted"><?php echo e(__('Minimum amount vendors can request for withdrawal.')); ?></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="withdrawal_gateways" class="form-label"><?php echo e(__('Withdrawal Gateways (one per line)')); ?></label>

                                <textarea id="withdrawal_gateways" name="withdrawal_gateways" rows="3" class="form-control"><?php echo e(implode("\n", $setting->withdrawal_gateways ?? [])); ?> </textarea>
                                <div class="form-text"><?php echo e(__('Enter each method name on a separate line (e.g. Bank Transfer, PayPal, Wise). It will be saved automatically.')); ?></div>
                                <small class="form-text text-muted"><?php echo e(__('List simple gateway titles vendors can choose from.')); ?></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="hidden" name="withdrawal_commission_enabled" value="0">
                                <div class="form-check">
                                    <input type="checkbox" id="withdrawal_commission_enabled" name="withdrawal_commission_enabled" value="1" class="form-check-input" <?php echo e(old('withdrawal_commission_enabled', $setting->withdrawal_commission_enabled ?? false) ? 'checked' : ''); ?>>
                                    <label for="withdrawal_commission_enabled" class="form-check-label"><?php echo e(__('Enable Withdrawal Commission')); ?></label>
                                </div>
                                <div class="mt-2">
                                    <label for="withdrawal_commission_rate" class="form-label"><?php echo e(__('Commission Rate (%)')); ?></label>
                                    <input type="number" step="0.01" id="withdrawal_commission_rate" name="withdrawal_commission_rate" class="form-control" value="<?php echo e(old('withdrawal_commission_rate', $setting->withdrawal_commission_rate ?? 0)); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <h4 class="mt-4"><?php echo e(__('Sales Commission')); ?></h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="commission_mode" class="form-label"><?php echo e(__('Commission Mode')); ?></label>
                                <select id="commission_mode" name="commission_mode" class="form-select">
                                    <option value="flat" <?php echo e(old('commission_mode', $setting->commission_mode ?? 'flat')==='flat' ? 'selected' : ''); ?>><?php echo e(__('Flat (Global Rate)')); ?></option>
                                    <option value="category" <?php echo e(old('commission_mode', $setting->commission_mode ?? 'flat')==='category' ? 'selected' : ''); ?>><?php echo e(__('Per Category')); ?></option>
                                </select>
                                <div class="form-text small"><?php echo e(__('Choose how vendor commission is determined.')); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="commission_flat_rate" class="form-label"><?php echo e(__('Global Commission Rate (%)')); ?></label>
                                <input type="number" step="0.01" id="commission_flat_rate" name="commission_flat_rate" class="form-control" value="<?php echo e(old('commission_flat_rate', $setting->commission_flat_rate ?? '')); ?>">
                                <div class="form-text small"><?php echo e(__('Used when mode is Flat or when category has no override.')); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="alert alert-info py-2 px-3 w-100 mb-0 small">
                                <strong><?php echo e(__('Tip:')); ?></strong> <?php echo e(__('Set per-category rate in category form when mode = Per Category.')); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group form-check">
                                <input type="hidden" name="auto_publish_reviews" value="0">
                                <input type="checkbox" id="auto_publish_reviews" name="auto_publish_reviews" value="1"
                                    class="form-check-input"
                                    <?php echo e(old('auto_publish_reviews', $setting->auto_publish_reviews ?? 0) ? 'checked' : ''); ?>>
                                <label for="auto_publish_reviews"
                                    class="form-check-label"><?php echo e(__('Auto-publish product reviews')); ?></label>
                                <small
                                    class="form-text text-muted"><?php echo e(__('If enabled, reviews submitted by authenticated users will be published immediately. Otherwise admin approval is required.')); ?></small>
                            </div>
                        </div>
                    </div>

                    <hr />
                    <h4 class="mt-4"><?php echo e(__('External Payment Settings')); ?></h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-check">
                                <input type="hidden" name="enable_external_payment_redirect" value="0">
                                <input type="checkbox" id="enable_external_payment_redirect" name="enable_external_payment_redirect" value="1"
                                    class="form-check-input"
                                    <?php echo e(old('enable_external_payment_redirect', $setting->enable_external_payment_redirect ?? false) ? 'checked' : ''); ?>>
                                <label for="enable_external_payment_redirect"
                                    class="form-check-label"><?php echo e(__('Enable External Payment Redirect')); ?></label>
                                <small
                                    class="form-text text-muted"><?php echo e(__('When enabled, customers will be redirected directly to external payment gateways instead of using the internal payment handler.')); ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info py-2 px-3 w-100 mb-0 small">
                                <strong><?php echo e(__('Note:')); ?></strong> <?php echo e(__('This setting affects how payment gateways handle customer redirections. Enable for direct gateway integration.')); ?>

                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?php echo e(__('Save Settings')); ?>

                            </button>
                            <button type="button" class="btn btn-outline-secondary js-reset-form" data-action="reset-settings-form">
                                <i class="fas fa-undo me-1"></i>
                                <?php echo e(__('Reset')); ?>

                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- System Information -->
        <div class="col-lg-4">
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-info"></i>
                    <h5 class="card-title mb-0"><?php echo e(__('System Information')); ?></h5>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Laravel Version')); ?></span>
                            <span class="badge bg-primary"><?php echo e(app()->version()); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('PHP Version')); ?></span>
                            <span class="badge bg-info"><?php echo e(PHP_VERSION); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Environment')); ?></span>
                            <span class="badge bg-<?php echo e(app()->environment() === 'production' ? 'success' : 'warning'); ?>">
                                <?php echo e(ucfirst(app()->environment())); ?>

                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Debug Mode')); ?></span>
                            <span class="badge bg-<?php echo e(config('app.debug') ? 'danger' : 'success'); ?>">
                                <?php echo e(config('app.debug') ? __('Enabled') : __('Disabled')); ?>

                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Current Language')); ?></span>
                            <span class="badge bg-secondary"><?php echo e(app()->getLocale()); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Total Users')); ?></span>
                            <span class="badge bg-dark"><?php echo e(App\Models\User::count()); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Total Languages')); ?></span>
                            <span class="badge bg-secondary"><?php echo e(App\Models\Language::count()); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Total Currencies')); ?></span>
                            <span class="badge bg-warning"><?php echo e(App\Models\Currency::count()); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold"><?php echo e(__('Server Time')); ?></span>
                            <span class="text-muted" id="server-time"><?php echo e(now()->format('Y-m-d H:i:s')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Maintenance -->
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-tools text-warning"></i>
                    <h5 class="card-title mb-0"><?php echo e(__('System Maintenance')); ?></h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="<?php echo e(route('admin.cache.clear')); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-trash me-1"></i>
                                <?php echo e(__('Clear Cache')); ?>

                            </button>
                        </form>

                        <form action="<?php echo e(route('admin.logs.clear')); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-file-alt me-1"></i>
                                <?php echo e(__('Clear Logs')); ?>

                            </button>
                        </form>

                        <form action="<?php echo e(route('admin.optimize')); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-rocket me-1"></i>
                                <?php echo e(__('Optimize System')); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/profile/settings.blade.php ENDPATH**/ ?>