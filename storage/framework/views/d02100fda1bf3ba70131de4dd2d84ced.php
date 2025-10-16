<?php $__env->startSection('title', $gateway->exists ? __('Edit Gateway') : __('Create Gateway')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="h4 mb-0"><?php echo e($gateway->exists ? __('Edit Gateway') : __('Create Gateway')); ?></h1>
    <a href="<?php echo e(route('admin.payment-gateways.index')); ?>" class="btn btn-secondary"><?php echo e(__('Back')); ?></a>
</div>

<form method="POST"
    action="<?php echo e($gateway->exists ? route('admin.payment-gateways.update', $gateway) : route('admin.payment-gateways.store')); ?>"
    class="card p-3">
    <?php echo csrf_field(); ?>
    <?php if($gateway->exists): ?>
    <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label"><?php echo e(__('Name')); ?></label>
            <input name="name" value="<?php echo e(old('name', $gateway->name)); ?>"
                class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo e(__('Slug')); ?></label>
            <input name="slug" value="<?php echo e(old('slug', $gateway->slug)); ?>"
                class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo e(__('Driver')); ?></label>
            <?php ($availableDrivers = []); ?>
            <?php ($existingConfig = $gateway->getCredentials() ?? ($gateway->config ?? [])); ?>
            <select name="driver" id="driver" class="form-control <?php $__errorArgs = ['driver'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                <?php echo e($gateway->exists ? 'disabled' : ''); ?> required
                data-config-base-url="<?php echo e(url('admin/payment-gateways-management/config-fields')); ?>"
                data-existing-config='<?php echo e(e(json_encode($existingConfig, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'>
                <option value="">-- <?php echo e(__('Choose')); ?> --</option>
                
                <option value="stripe" <?php echo e(old('driver', $gateway->driver) === 'stripe' ? 'selected' : ''); ?>>Stripe
                </option>
                <option value="offline" <?php echo e(old('driver', $gateway->driver) === 'offline' ? 'selected' : ''); ?>>
                    <?php echo e(__('Offline / Bank Transfer')); ?></option>
                <option value="paytabs" <?php echo e(old('driver', $gateway->driver) === 'paytabs' ? 'selected' : ''); ?>>PayTabs</option>
                <option value="tap" <?php echo e(old('driver', $gateway->driver) === 'tap' ? 'selected' : ''); ?>>Tap</option>
                <option value="weaccept" <?php echo e(old('driver', $gateway->driver) === 'weaccept' ? 'selected' : ''); ?>>WeAccept</option>
                <option value="paypal" <?php echo e(old('driver', $gateway->driver) === 'paypal' ? 'selected' : ''); ?>>PayPal</option>
                <option value="payeer" <?php echo e(old('driver', $gateway->driver) === 'payeer' ? 'selected' : ''); ?>>Payeer</option>
                
                
            </select>
            <?php if($gateway->exists): ?>
            <input type="hidden" name="driver" value="<?php echo e($gateway->driver); ?>">
            <?php endif; ?>
            <?php $__errorArgs = ['driver'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-2 d-flex align-items-center mt-4">
            <div class="form-check">
                <input type="checkbox" name="enabled" value="1" id="enabled" class="form-check-input"
                    <?php echo e(old('enabled', $gateway->enabled) ? 'checked' : ''); ?>>
                <label for="enabled" class="form-check-label"><?php echo e(__('Enabled')); ?></label>
            </div>
        </div>
    </div>

    

    <hr>

    <!-- Stripe Fields -->
    <div id="driver-stripe" class="driver-fields envato-hidden">
        <h5 class="mt-2">Stripe</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Publishable Key')); ?></label>
                <?php ($stripeCfg = $gateway->getStripeConfig()); ?>
                <input name="stripe_publishable_key"
                    value="<?php echo e(old('stripe_publishable_key', $stripeCfg['publishable_key'] ?? '')); ?>"
                    class="form-control <?php $__errorArgs = ['stripe_publishable_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['stripe_publishable_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Secret Key')); ?></label>
                <input name="stripe_secret_key" value=""
                    placeholder="<?php echo e(($gateway->exists && !empty($stripeCfg['secret_key'])) ? '********' : ''); ?>"
                    class="form-control <?php $__errorArgs = ['stripe_secret_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['stripe_secret_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__('Webhook Secret')); ?></label>
                <input name="stripe_webhook_secret" value=""
                    placeholder="<?php echo e(($gateway->exists && !empty($stripeCfg['webhook_secret'])) ? '********' : ''); ?>"
                    class="form-control <?php $__errorArgs = ['stripe_webhook_secret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['stripe_webhook_secret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo e(__('Mode')); ?></label>
                <select name="stripe_mode" class="form-control <?php $__errorArgs = ['stripe_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="test"
                        <?php echo e(old('stripe_mode', $stripeCfg['mode'] ?? 'test')==='test' ? 'selected' : ''); ?>>
                        <?php echo e(__('Test')); ?></option>
                    <option value="live"
                        <?php echo e(old('stripe_mode', $stripeCfg['mode'] ?? null) === 'live' ? 'selected' : ''); ?>>
                        <?php echo e(__('Live')); ?></option>
                </select>
                <?php $__errorArgs = ['stripe_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>

    <!-- Offline Fields -->
    <div id="driver-offline" class="driver-fields envato-hidden">
        <h5 class="mt-2"><?php echo e(__('Offline / Bank Transfer')); ?></h5>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label"><?php echo e(__('Transfer Instructions (HTML allowed)')); ?></label>
                <textarea name="transfer_instructions" rows="5"
                    class="form-control <?php $__errorArgs = ['transfer_instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('transfer_instructions', $gateway->transfer_instructions)); ?></textarea>
                <?php $__errorArgs = ['transfer_instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input type="checkbox" name="requires_transfer_image" value="1" id="requires_transfer_image"
                        class="form-check-input"
                        <?php echo e(old('requires_transfer_image', $gateway->requires_transfer_image) ? 'checked' : ''); ?>>
                    <label for="requires_transfer_image"
                        class="form-check-label"><?php echo e(__('Require transfer image')); ?></label>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic driver config -->
    <div id="dynamic-driver-config" class="mt-3">
        <h5 class="mt-2"><?php echo e(__('Gateway Configuration')); ?></h5>
        <div id="dynamic-config-fields">
            
        </div>
        
        <!-- PayTabs Fields -->
        <div id="driver-paytabs" class="driver-fields envato-hidden">
            <h5 class="mt-2">PayTabs</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><?php echo e(__('Profile ID')); ?></label>
                    <input name="paytabs_profile_id" value="<?php echo e(old('paytabs_profile_id', $gateway->config['paytabs_profile_id'] ?? '')); ?>" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label"><?php echo e(__('Server Key')); ?></label>
                    <input name="paytabs_server_key" value="" placeholder="<?php echo e(!empty(($gateway->config['paytabs_server_key'] ?? null)) ? '********' : ''); ?>" class="form-control">
                </div>
            </div>
        </div>

        <!-- Tap Fields -->
        <div id="driver-tap" class="driver-fields envato-hidden">
            <h5 class="mt-2">Tap</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Secret Key')); ?></label>
                    <input name="tap_secret_key" value="" placeholder="<?php echo e(!empty(($gateway->config['tap_secret_key'] ?? null)) ? '********' : ''); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Public Key')); ?></label>
                    <input name="tap_public_key" value="<?php echo e(old('tap_public_key', $gateway->config['tap_public_key'] ?? '')); ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?php echo e(__('Currency')); ?></label>
                    <input name="tap_currency" value="<?php echo e(old('tap_currency', $gateway->config['tap_currency'] ?? 'USD')); ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?php echo e(__('Language Code')); ?></label>
                    <input name="tap_lang" value="<?php echo e(old('tap_lang', $gateway->config['tap_lang'] ?? 'en')); ?>" class="form-control">
                </div>
            </div>
        </div>

        <!-- WeAccept Fields -->
        <div id="driver-weaccept" class="driver-fields envato-hidden">
            <h5 class="mt-2">WeAccept (Accept / PayMob)</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('API Key')); ?></label>
                    <input name="weaccept_api_key" value="" placeholder="<?php echo e(!empty(($gateway->config['weaccept_api_key'] ?? null)) ? '********' : ''); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('HMAC Secret')); ?></label>
                    <input name="weaccept_hmac_secret" value="" placeholder="<?php echo e(!empty(($gateway->config['weaccept_hmac_secret'] ?? null)) ? '********' : ''); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Integration ID')); ?></label>
                    <input name="weaccept_integration_id" value="<?php echo e(old('weaccept_integration_id', $gateway->config['weaccept_integration_id'] ?? '')); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Iframe ID')); ?></label>
                    <input name="weaccept_iframe_id" value="<?php echo e(old('weaccept_iframe_id', $gateway->config['weaccept_iframe_id'] ?? ($gateway->config['iframe_id'] ?? ''))); ?>" class="form-control" placeholder="e.g. 371273">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?php echo e(__('Currency')); ?></label>
                    <input name="weaccept_currency" value="<?php echo e(old('weaccept_currency', $gateway->config['weaccept_currency'] ?? ($gateway->config['paymob_currency'] ?? 'EGP'))); ?>" class="form-control" placeholder="EGP">
                </div>
                <div class="col-md-8">
                    <label class="form-label"><?php echo e(__('API Base (optional)')); ?></label>
                    <input name="weaccept_api_base" value="<?php echo e(old('weaccept_api_base', $gateway->config['api_base'] ?? '')); ?>" class="form-control" placeholder="https://accept.paymob.com">
                    <small class="text-muted"><?php echo e(__('Leave empty to use default https://accept.paymob.com')); ?></small>
                </div>
            </div>
        </div>

        <!-- PayPal Fields -->
        <div id="driver-paypal" class="driver-fields envato-hidden">
            <h5 class="mt-2">PayPal</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Client ID')); ?></label>
                    <input name="paypal_client_id" value="<?php echo e(old('paypal_client_id', $gateway->config['paypal_client_id'] ?? '')); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Secret')); ?></label>
                    <input name="paypal_secret" value="" placeholder="<?php echo e(!empty(($gateway->config['paypal_secret'] ?? null)) ? '********' : ''); ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?php echo e(__('Mode')); ?></label>
                    <select name="paypal_mode" class="form-control">
                        <option value="sandbox" <?php echo e(old('paypal_mode', $gateway->config['paypal_mode'] ?? 'sandbox')==='sandbox' ? 'selected' : ''); ?>>Sandbox</option>
                        <option value="live" <?php echo e(old('paypal_mode', $gateway->config['paypal_mode'] ?? '')==='live' ? 'selected' : ''); ?>>Live</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Payeer Fields -->
        <div id="driver-payeer" class="driver-fields envato-hidden">
            <h5 class="mt-2">Payeer</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Merchant ID')); ?></label>
                    <input name="payeer_merchant_id" value="<?php echo e(old('payeer_merchant_id', $gateway->config['payeer_merchant_id'] ?? '')); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(__('Secret Key')); ?></label>
                    <input name="payeer_secret_key" value="" placeholder="<?php echo e(!empty(($gateway->config['payeer_secret_key'] ?? null)) ? '********' : ''); ?>" class="form-control">
                </div>
            </div>
        </div>

    
         
         <div id="custom-rows" class="mt-2">
            
        </div>
        <div class="mt-2">
            <button type="button" id="add-custom"
                class="btn btn-sm btn-outline-secondary"><?php echo e(__('Add custom key')); ?></button>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary"><?php echo e($gateway->exists ? __('Update') : __('Create')); ?></button>
    </div>
</form>

<script src="<?php echo e(asset('admin/js/payment-gateway-form.js')); ?>" defer></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/payment_gateways/form.blade.php ENDPATH**/ ?>