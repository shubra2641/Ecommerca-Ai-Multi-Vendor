<?php $__env->startSection('title', __('Checkout').' - '.config('app.name')); ?>
<?php $__env->startSection('content'); ?>
<section class="products-section products-section--checkout">
    <div class="container container--wide">
        <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Cart'), 'url' => route('cart.index'), 'icon' => 'fas fa-shopping-cart'],
            ['title' => __('Checkout'), 'url' => '#']
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Cart'), 'url' => route('cart.index'), 'icon' => 'fas fa-shopping-cart'],
            ['title' => __('Checkout'), 'url' => '#']
        ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
    </div>
</section>
<section class="checkout-section">
    <div class="container container--wide">
        <form action="/checkout/submit" method="post" class="checkout-form" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php if($errors->any()): ?>
            <div class="alert alert-danger small">
                <ul class="list-reset">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php $__env->startPush('styles'); ?>
            <link rel="stylesheet" href="<?php echo e(asset('front/css/checkout.css')); ?>">
            <?php $__env->stopPush(); ?>
            <div class="checkout-row">
                <div class="checkout-left">
                    <div class="panel-card">
                        <h3 class="panel-title"><?php echo e(__('Shipping Address')); ?></h3>
                        <div class="address-card">
                            <div class="address-left">
                                <div class="address-title"><?php echo e(__('Deliver to')); ?></div>
                                <div class="small-muted">
                                    <?php echo e(__('Choose one of your saved addresses or enter a new one')); ?></div>
                            </div>
                            <div class="address-actions"><a href="#addresses-manage"
                                    class="btn btn-sm btn-outline"><?php echo e(__('Manage')); ?></a></div>
                        </div>

                        
                        <div id="addresses-list" class="mt-2">
                            <?php if(!empty($addresses) && $addresses->count()): ?>
                            <div class="addresses-grid">
                                <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="address-card-selectable" data-addr-id="<?php echo e($addr->id); ?>"
                                    data-country="<?php echo e($addr->country_id); ?>"
                                    data-governorate="<?php echo e($addr->governorate_id); ?>" data-city="<?php echo e($addr->city_id); ?>"
                                    data-line1="<?php echo e(e($addr->line1)); ?>" data-line2="<?php echo e(e($addr->line2)); ?>"
                                    data-phone="<?php echo e(e($addr->phone)); ?>">
                                    <input type="radio" name="selected_address" value="<?php echo e($addr->id); ?>"
                                        <?php echo e($addr->is_default ? 'checked' : ''); ?>>
                                    <div class="address-card-body">
                                        <div class="address-name"><?php echo e($addr->name ?? auth()->user()->name); ?></div>
                                        <div class="small-muted">
                                            <?php echo e($addr->line1); ?><?php echo e($addr->line2 ? ', ' . $addr->line2 : ''); ?></div>
                                        <div class="small-muted"><?php echo e($addr->phone); ?></div>
                                        <?php if($addr->is_default): ?><div class="badge small"><?php echo e(__('Default')); ?></div><?php endif; ?>
                                    </div>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php else: ?>
                            <div class="small-muted">
                                <?php echo e(__('No saved addresses. Please enter a delivery address below.')); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(__('Name')); ?></label>
                                    <input type="text" name="customer_name" class="form-control" required minlength="2"
                                        maxlength="120"
                                        value="<?php echo e(old('customer_name', $defaultAddress->name ?? auth()->user()->name ?? '')); ?>">
                                    <?php $__errorArgs = ['customer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(__('Email')); ?></label>
                                    <input type="email" name="customer_email" class="form-control" required
                                        value="<?php echo e(old('customer_email', auth()->user()->email ?? '')); ?>">
                                    <?php $__errorArgs = ['customer_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(__('Phone')); ?></label>
                                    <!-- Use data-pattern to avoid browsers compiling the regex before our sanitizer runs (CSP-safe) -->
                                    <input type="tel" name="customer_phone" class="form-control" required
                                        data-pattern="^[0-9+() \-]{6,20}$" title="<?php echo e(__('Valid phone required')); ?>"
                                        value="<?php echo e(old('customer_phone', $defaultAddress->phone ?? auth()->user()->phone ?? '')); ?>">
                                    <?php $__errorArgs = ['customer_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(__('Country')); ?></label>
                                    <select name="country" class="form-control" required onchange="this.form.submit()">
                                        <option value=""><?php echo e(__('Select Country')); ?></option>
                                        <?php $__currentLoopData = \App\Models\Country::where('active',1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c->id); ?>"
                                            <?php echo e((old('country', $defaultAddress->country_id ?? auth()->user()->country_id ?? '') == $c->id) ? 'selected' : ''); ?>>
                                            <?php echo e($c->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(__('Governorate')); ?></label>
                                    <select name="governorate" class="form-control" required onchange="this.form.submit()">
                                        <option value=""><?php echo e(__('Select Governorate')); ?></option>
                                        <?php if(old('country') || (!empty($defaultAddress) && $defaultAddress->country_id) || (auth()->user() && auth()->user()->country_id)): ?>
                                        <?php
                                            $selectedCountry = old('country', $defaultAddress->country_id ?? auth()->user()->country_id ?? '');
                                            $governorates = \App\Models\Governorate::where('country_id', $selectedCountry)->where('active', 1)->get();
                                            $selectedGovernorate = old('governorate', $defaultAddress->governorate_id ?? auth()->user()->governorate_id ?? '');
                                            
                                            // Auto-select first governorate if none selected and country is selected
                                            if (!$selectedGovernorate && $selectedCountry && $governorates->count() > 0) {
                                                $selectedGovernorate = $governorates->first()->id;
                                            }
                                        ?>
                                        <?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gov->id); ?>"
                                            <?php echo e(($selectedGovernorate == $gov->id) ? 'selected' : ''); ?>>
                                            <?php echo e($gov->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(__('City')); ?></label>
                                    <select name="city" class="form-control" required onchange="this.form.submit()">
                                        <option value=""><?php echo e(__('Select City')); ?></option>
                                        <?php if(old('governorate') || (!empty($defaultAddress) && $defaultAddress->governorate_id) || (auth()->user() && auth()->user()->governorate_id)): ?>
                                        <?php
                                            $selectedGovernorate = old('governorate', $defaultAddress->governorate_id ?? auth()->user()->governorate_id ?? '');
                                            $cities = \App\Models\City::where('governorate_id', $selectedGovernorate)->where('active', 1)->get();
                                            $selectedCity = old('city', $defaultAddress->city_id ?? auth()->user()->city_id ?? '');
                                            
                                            // Auto-select first city if none selected and governorate is selected
                                            if (!$selectedCity && $selectedGovernorate && $cities->count() > 0) {
                                                $selectedCity = $cities->first()->id;
                                            }
                                        ?>
                                        <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($city->id); ?>"
                                            <?php echo e(($selectedCity == $city->id) ? 'selected' : ''); ?>>
                                            <?php echo e($city->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Delivery Address')); ?></label>
                                <input type="text" name="customer_address" class="form-control" required minlength="5"
                                    maxlength="190"
                                    value="<?php echo e(old('customer_address', $defaultAddress->line1 ?? auth()->user()->address ?? '')); ?>">
                                <?php $__errorArgs = ['customer_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Notes')); ?></label>
                                <textarea name="notes" class="form-control" rows="12"><?php echo e(old('notes')); ?></textarea>
                                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small">
                                    <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <?php if(old('city') || (!empty($defaultAddress) && $defaultAddress->city_id) || (auth()->user() && auth()->user()->city_id)): ?>
                            <?php
                                $selectedCountry = old('country', $defaultAddress->country_id ?? auth()->user()->country_id ?? '');
                                $selectedGovernorate = old('governorate', $defaultAddress->governorate_id ?? auth()->user()->governorate_id ?? '');
                                $selectedCity = old('city', $defaultAddress->city_id ?? auth()->user()->city_id ?? '');
                                
                                // Auto-select first available options if not selected
                                if ($selectedCountry && !$selectedGovernorate) {
                                    $governorates = \App\Models\Governorate::where('country_id', $selectedCountry)->where('active', 1)->get();
                                    if ($governorates->count() > 0) {
                                        $selectedGovernorate = $governorates->first()->id;
                                    }
                                }
                                
                                if ($selectedGovernorate && !$selectedCity) {
                                    $cities = \App\Models\City::where('governorate_id', $selectedGovernorate)->where('active', 1)->get();
                                    if ($cities->count() > 0) {
                                        $selectedCity = $cities->first()->id;
                                    }
                                }
                                
                                // Find shipping rule for the selected location
                                $shippingRule = \App\Models\ShippingRule::where('active', 1)
                                    ->where(function($query) use ($selectedCity, $selectedGovernorate, $selectedCountry) {
                                        $query->where('city_id', $selectedCity)
                                              ->orWhere(function($q) use ($selectedGovernorate) {
                                                  $q->where('governorate_id', $selectedGovernorate)
                                                    ->whereNull('city_id');
                                              })
                                              ->orWhere(function($q) use ($selectedCountry) {
                                                  $q->where('country_id', $selectedCountry)
                                                    ->whereNull('governorate_id')
                                                    ->whereNull('city_id');
                                              });
                                    })
                                    ->orderBy('city_id', 'desc') // Prioritize city-specific rules
                                    ->orderBy('governorate_id', 'desc') // Then governorate-specific
                                    ->orderBy('country_id', 'desc') // Finally country-specific
                                    ->first();
                            ?>
                            
                            <?php if($shippingRule): ?>
                            <div class="shipping-info mt-3">
                                <div class="shipping-cost">
                                    <strong><?php echo e(__('Shipping Cost')); ?>: <?php echo e($currency_symbol ?? '$'); ?><?php echo e(number_format($shippingRule->price, 2)); ?></strong>
                                </div>
                                <?php if($shippingRule->estimated_days): ?>
                                <div class="shipping-days small text-muted">
                                    <?php echo e(__('Estimated delivery')); ?>: <?php echo e($shippingRule->estimated_days); ?> <?php echo e(__('days')); ?>

                                </div>
                                <?php endif; ?>
                                <input type="hidden" name="shipping_cost" value="<?php echo e($shippingRule->price); ?>">
                            </div>
                            <?php else: ?>
                            <div class="shipping-info mt-3">
                                <div class="alert alert-warning small">
                                    <?php echo e(__('Shipping cost will be calculated at checkout')); ?>

                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="panel-card">
                        <h3 class="panel-title"><?php echo e(__('Your Order')); ?></h3>
                        <?php $__currentLoopData = ($coItems ?? $items); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="order-item">
                            <img src="<?php echo e($it['display_image'] ?? ''); ?>" alt="">
                            <div class="order-item-details">
                                <div class="order-item-title"><?php echo e($it['product']->name); ?>

                                    <?php if(!empty($it['variant_label'])): ?>
                                    <small class="small-muted"><?php echo e($it['variant_label']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="small-muted"><?php echo e($it['qty']); ?> ×
                                    <?php echo e($currency_symbol ?? '$'); ?><?php echo e(number_format($it['product']->price,2)); ?></div>
                            </div>
                            <div class="order-item-price">
                                <?php echo e($currency_symbol ?? '$'); ?><?php echo e(number_format($it['lineTotal'],2)); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="mt-2 small text-muted">
                            <?php echo e(__('Get it soon based on shipping option')); ?></div>
                    </div>
                    <div class="panel-card">
                        <h3 class="panel-title"><?php echo e(__('Payment')); ?></h3>
                        <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="gateway-item">
                            <input type="radio" name="gateway" value="<?php echo e($gw->slug); ?>"
                                <?php echo e((old('gateway', ($loop->first ? $gw->slug : null)) == $gw->slug) ? 'checked' : ''); ?>>
                            <span class="gateway-name"><?php echo e($gw->name); ?>

                                <?php if($gw->driver==='offline'): ?><small>(<?php echo e(__('Offline')); ?>)</small><?php endif; ?></span>
                            <?php if($gw->driver === 'offline' && $gw->transfer_instructions): ?>
                            <div class="gateway-instructions small-muted"><?php echo \App\Services\HtmlSanitizer::sanitizeEmbed($gw->transfer_instructions); ?></div>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <div id="transfer-image-area" class="mt-2 envato-hidden"
                            data-requiring='<?php echo e(e(json_encode($gateways->filter(fn($g)=>$g->requires_transfer_image)->pluck('id'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'
                            slug'))'>
                            <label class="form-label"><?php echo e(__('Upload proof of transfer')); ?></label>
                            <div class="small-muted mb-1">
                                <?php echo e(__('If your chosen payment method requires a payment receipt, please upload an image here.')); ?>

                            </div>
                            <input type="file" name="transfer_image" id="transfer_image" accept="image/*"
                                class="form-control-file">
                            <?php $__errorArgs = ['transfer_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <?php if(!count($gateways)): ?>
                        <div class="alert alert-warning small">
                            <?php echo e(__('No payment gateways available')); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <aside class="checkout-right">
                    <div class="summary-box panel-card">
                        <h3 class="panel-title"><?php echo e(__('Order Summary')); ?></h3>
                        <ul class="summary-lines">
                            <?php $__currentLoopData = ($coItems ?? $items); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="summary-line">
                                <span class="order-item-title">
                                    <span class="title-row"><span class="title-text"><?php echo e($it['product']->name); ?></span>
                                        <span class="qty">× <?php echo e($it['qty']); ?></span></span>
                                    <?php if(!empty($it['variant_label'])): ?>
                                    <span class="product-meta"><?php echo e($it['variant_label']); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span>$<?php echo e(number_format($it['lineTotal'],2)); ?></span>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <li class="summary-line">
                                <span><?php echo e(__('Shipping Fee')); ?></span><span class="shipping-amount">-</span>
                            </li>
                            <?php if(isset($coupon) && $coupon): ?>
                            <li class="summary-line">
                                <span><?php echo e(__('Coupon')); ?> (<strong><?php echo e($coupon->code); ?></strong>)</span>
                                <span
                                    class="coupon-discount-amount">-<?php echo e($currency_symbol ?? '$'); ?><?php echo e(number_format($discount,2)); ?></span>
                            </li>
                            <?php endif; ?>
                            <li class="summary-total">
                                <span><?php echo e(__('Total Incl. VAT')); ?></span><span
                                    class="order-total">$<?php echo e(number_format($displayDiscountedTotal ?? $total,2)); ?></span>
                            </li>
                        </ul>
                        <div class="mt-2">
                            <input type="hidden" name="shipping_zone_id" id="input-shipping-zone-id"
                                value="<?php echo e(old('shipping_zone_id', '')); ?>">
                            <input type="hidden" name="shipping_price" id="input-shipping-price"
                                value="<?php echo e(old('shipping_price', '')); ?>">
                            <input type="hidden" name="shipping_country" id="input-shipping-country"
                                value="<?php echo e(old('shipping_country', old('country', $defaultAddress->country_id ?? optional(auth()->user())->country_id ?? ''))); ?>">
                            <input type="hidden" name="shipping_governorate" id="input-shipping-governorate"
                                value="<?php echo e(old('shipping_governorate', old('governorate', $defaultAddress->governorate_id ?? optional(auth()->user())->governorate_id ?? ''))); ?>">
                            <input type="hidden" name="shipping_city" id="input-shipping-city"
                                value="<?php echo e(old('shipping_city', old('city', $defaultAddress->city_id ?? optional(auth()->user())->city_id ?? ''))); ?>">
                            <input type="hidden" name="selected_address_id" id="selected-address-id"
                                value="<?php echo e(old('selected_address_id', '')); ?>">
                            <button class="btn btn-primary btn-place" type="submit"><?php echo e(__('Place Order')); ?></button>
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>
<div id="checkout-root" hidden data-config='<?php echo e(e(json_encode($checkoutConfigJson ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'></div>

<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/checkout/index.blade.php ENDPATH**/ ?>