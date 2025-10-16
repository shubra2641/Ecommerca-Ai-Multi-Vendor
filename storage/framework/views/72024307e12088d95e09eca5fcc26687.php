

<div class="row g-4" data-default-locale="<?php echo e($defaultLocale); ?>">
    <!-- Main Content -->
    <div class="col-xl-8 col-lg-7">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo e(__('Basic Information')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-12">
                        <div class="lang-tabs-wrapper" data-lang-tabs-variant="success">
                            <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-1" id="prodLangTabs" role="tablist">
                                <?php $activeLocale = $currentLocale ?? app()->getLocale(); ?>
                                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 <?php if($lang->code===$activeLocale): ?> active <?php endif; ?>" id="prod-tab-<?php echo e($lang->code); ?>"
                                        data-bs-toggle="tab" data-bs-target="#prod-panel-<?php echo e($lang->code); ?>" type="button"
                                        role="tab"><?php echo e(strtoupper($lang->code)); ?></button>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                        <div class="tab-content border rounded-bottom p-3">
                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tab-pane fade <?php if($lang->code===$activeLocale): ?> show active <?php endif; ?>" id="prod-panel-<?php echo e($lang->code); ?>"
                                role="tabpanel">
                <div class="mb-2">
                                    <label class="form-label"><?php echo e(__('Name')); ?></label>
                    <input name="name[<?php echo e($lang->code); ?>]" value="<?php echo e($pfLangMeta[$lang->code]['name_val']); ?>" class="form-control" <?php if($lang->code===$defaultLocale): ?> required <?php endif; ?> placeholder="<?php echo e($pfLangMeta[$lang->code]['ph_name']); ?>">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label"><?php echo e(__('Short Description')); ?></label>
                                        <textarea name="short_description[<?php echo e($lang->code); ?>]" class="form-control" rows="3" placeholder="<?php echo e($pfLangMeta[$lang->code]['ph_short']); ?>"><?php echo e($pfLangMeta[$lang->code]['short_val']); ?></textarea>
                                </div>
                                    <div class="mb-2">
                                        <label class="form-label d-flex justify-content-between align-items-center">
                                            <span><?php echo e(__('Description')); ?></span>
                                            <button type="button" class="btn btn-sm btn-outline-primary js-ai-generate" data-lang="<?php echo e($lang->code); ?>" data-loading="0">
                                                <i class="fas fa-magic me-1"></i><?php echo e(__('AI Generate')); ?>

                                            </button>
                                        </label>
                                        <textarea name="description[<?php echo e($lang->code); ?>]" class="form-control js-ai-description" rows="6" placeholder="<?php echo e($pfLangMeta[$lang->code]['ph_desc']); ?>"><?php echo e($pfLangMeta[$lang->code]['desc_val']); ?></textarea>
                                    </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Configuration -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    <?php echo e(__('Product Configuration')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('Type')); ?></label>
                        <select name="type" class="form-select" id="type-select">
                            <option value="simple" <?php if(old('type',$m?->type ??
                                'simple')==='simple'): echo 'selected'; endif; ?>><?php echo e(__('Simple')); ?></option>
                            <option value="variable" <?php if(old('type',$m?->type ??
                                '')==='variable'): echo 'selected'; endif; ?>><?php echo e(__('Variable')); ?></option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('Physical/Digital')); ?></label>
                        <select name="physical_type" class="form-select">
                            <option value="physical" <?php if(old('physical_type',$m?->physical_type ??
                                'physical')==='physical'): echo 'selected'; endif; ?>><?php echo e(__('Physical')); ?></option>
                            <option value="digital" <?php if(old('physical_type',$m?->physical_type ??
                                '')==='digital'): echo 'selected'; endif; ?>><?php echo e(__('Digital')); ?></option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('SKU')); ?></label>
                        <input name="sku" value="<?php echo e(old('sku',$m?->sku)); ?>" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('Category')); ?></label>
                        <select name="product_category_id" class="form-select">
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php if(old('product_category_id',$m?->product_category_id ??
                                '')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <!-- Simple Product Pricing -->
                <div class="row mt-4 g-3 simple-only">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('Price')); ?></label>
                        <input type="number" step="0.01" name="price" value="<?php echo e(old('price',$m?->price ?? 0)); ?>"
                            class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('Sale Price')); ?></label>
                        <input type="number" step="0.01" name="sale_price"
                            value="<?php echo e(old('sale_price',$m?->sale_price)); ?>" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('Sale Start')); ?></label>
                        <input type="datetime-local" name="sale_start"
                            value="<?php echo e(old('sale_start',optional($m?->sale_start)->format('Y-m-d\\TH:i'))); ?>"
                            class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label"><?php echo e(__('Sale End')); ?></label>
                        <input type="datetime-local" name="sale_end"
                            value="<?php echo e(old('sale_end',optional($m?->sale_end)->format('Y-m-d\\TH:i'))); ?>"
                            class="form-control">
                    </div>
                </div>

                <!-- Variable Product Variations -->
                <div class="variable-only mt-4 envato-hidden">
                    <h6 class="fw-semibold mb-3">
                        <i class="fas fa-layer-group me-1"></i>
                        <?php echo e(__('Variations')); ?>

                    </h6>
                    <?php if($errors->has('variations') ||
                    collect($errors->keys())->filter(fn($k)=>str_starts_with($k,'variations.'))->isNotEmpty()): ?>
                    <div class="alert alert-danger small">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(str_starts_with($e,'Duplicate attribute combination') || str_contains($e,'variations')): ?>
                            <li><?php echo e($e); ?></li>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $errors->keys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(str_starts_with($k,'variations.')): ?>
                            <?php $__currentLoopData = $errors->get($k); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($k); ?>: <?php echo e($msg); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Variation Attributes')); ?></label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <?php $__currentLoopData = $pfAttrData ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input used-attr-checkbox" type="checkbox" name="used_attributes[]" value="<?php echo e($a['slug']); ?>" <?php if(in_array($a['slug'],$pfUsedAttributes ?? [])): ?> checked <?php endif; ?>>
                                <span class="form-check-label"><?php echo e($a['name']); ?></span>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="form-text mb-3"><?php echo e(__('Select which attributes will be used to create variations. Unchecked attributes will not appear on each variation row.')); ?></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="variations-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center"><?php echo e(__('Active')); ?></th>
                                    <th><?php echo e(__('Attributes / Name')); ?></th>
                                    <th class="d-none d-md-table-cell"><?php echo e(__('SKU')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th class="d-none d-lg-table-cell"><?php echo e(__('Sale')); ?></th>
                                    <th class="d-none d-md-table-cell"><?php echo e(__('Stock')); ?></th>
                                    <th class="text-center"><?php echo e(__('Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-variation">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-sm-inline"><?php echo e(__('Add Variation')); ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- SEO Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>
                    <?php echo e(__('SEO Settings')); ?>

                </h5>
            </div>
            <div class="card-body">
                
                <div class="lang-tabs-wrapper mt-2" data-lang-tabs-variant="primary">
                    <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-1" role="tablist">
                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1 px-3 <?php if($lang->code===$activeLocale): ?> active <?php endif; ?>" data-bs-toggle="tab" data-bs-target="#seo-tab-<?php echo e($lang->code); ?>" type="button" role="tab"><?php echo e(strtoupper($lang->code)); ?></button>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <div class="tab-content border rounded-bottom p-3">
                    <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="tab-pane fade <?php if($lang->code===$activeLocale): ?> show active <?php endif; ?>" id="seo-tab-<?php echo e($lang->code); ?>" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('SEO Title')); ?></label>
                                <input name="seo_title[<?php echo e($lang->code); ?>]" value="<?php echo e($pfLangMeta[$lang->code]['seo_title']); ?>" class="form-control" placeholder="<?php echo e($pfLangMeta[$lang->code]['ph_seo_title']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(__('SEO Keywords')); ?></label>
                                <input name="seo_keywords[<?php echo e($lang->code); ?>]" value="<?php echo e($pfLangMeta[$lang->code]['seo_keywords']); ?>" class="form-control" placeholder="<?php echo e($pfLangMeta[$lang->code]['ph_seo_keywords']); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span><?php echo e(__('SEO Description')); ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary js-ai-generate-seo" data-lang="<?php echo e($lang->code); ?>" data-loading="0">
                                        <i class="fas fa-wand-magic-sparkles me-1"></i><?php echo e(__('AI Generate')); ?>

                                    </button>
                                </label>
                                <textarea name="seo_description[<?php echo e($lang->code); ?>]" class="form-control js-ai-seo-description" rows="3" placeholder="<?php echo e($pfLangMeta[$lang->code]['ph_seo_description']); ?>"><?php echo e($pfLangMeta[$lang->code]['seo_description']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-xl-4 col-lg-5">
        <!-- Media Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-images me-2"></i>
                    <?php echo e(__('Media')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('Main Image')); ?></label>
                    <div class="input-group">
                        <input name="main_image" value="<?php echo e(old('main_image',$m?->main_image)); ?>" class="form-control"
                            placeholder="/storage/...">
                        <button type="button" class="btn btn-outline-secondary" data-open-media="main_image">
                            <i class="fas fa-folder-open"></i>
                        </button>
                    </div>
                </div>

                <div id="product-variation-meta" data-existing='<?php echo e(e(json_encode($pfClientVariations, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'
                    data-attributes='<?php echo e(e(json_encode($pfAttrData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'
                    data-used='<?php echo e(e(json_encode($pfUsedAttributes, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))); ?>'></div>

                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('Gallery')); ?></label>
                    <div id="gallery-manager" class="d-flex flex-wrap gap-2 mb-2"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-gallery-image">
                        <i class="fas fa-plus"></i>
                        <?php echo e(__('Add Image')); ?>

                    </button>
                    <input type="hidden" name="gallery" id="gallery-input"
                        value="<?php echo e(old('gallery', json_encode($m?->gallery ?? []))); ?>">
                    <div class="form-text mt-2">
                        <?php echo e(__('You can leave it empty. Click Add Image multiple times to append more.')); ?>

                    </div>
                </div>

                <!-- Digital Download Settings (only shown for digital products) -->
                <div class="mb-3 digital-only envato-hidden">
                    <h6 class="mb-2"><?php echo e(__('Digital Download')); ?></h6>
                    <div class="mb-2">
                        <label class="form-label small"><?php echo e(__('Download File (path)')); ?></label>
                        <div class="input-group">
                            <input name="download_file" value="<?php echo e(old('download_file', $m?->download_file)); ?>"
                                class="form-control" placeholder="/storage/downloads/file.zip">
                            <button type="button" class="btn btn-outline-secondary" data-open-media="download_file">
                                <i class="fas fa-folder-open"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <?php echo e(__('If you upload a file ensure it is ZIP or PDF. Provide a storage path.')); ?></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small"><?php echo e(__('Download URL')); ?></label>
                        <input name="download_url" value="<?php echo e(old('download_url', $m?->download_url)); ?>"
                            class="form-control" placeholder="https://...">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="has_serials" value="1"
                            <?php if(old('has_serials', $m?->has_serials ?? false)): echo 'checked'; endif; ?> id="has_serials_checkbox">
                        <label
                            class="form-check-label"><?php echo e(__('This product uses serial codes (one per sale)')); ?></label>
                    </div>
                    <div class="mb-2 serials-only <?php echo e($pfHasSerials ? '' : 'envato-hidden'); ?>">
                        <label class="form-label small"><?php echo e(__('Serials (one per line)')); ?></label>
                        <textarea name="serials" class="form-control" rows="4"
                            placeholder="SERIAL1\nSERIAL2"><?php echo e(old('serials')); ?></textarea>
                        <div class="form-text">
                            <?php echo e(__('Add serial codes here to seed stock; one code per line. When sold each serial is marked sold and won\'t be reused.')); ?>

                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('Tags')); ?></label>
                    <select name="tag_ids[]" multiple class="form-select" size="6">
                        <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->id); ?>" <?php if(in_array($t->id,
                            old('tag_ids',$m?->tags->pluck('id')->toArray() ?? []))): echo 'selected'; endif; ?>><?php echo e($t->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Product Flags -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-flag me-2"></i>
                    <?php echo e(__('Product Flags')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                        <?php if(old('is_featured',$m?->is_featured ?? false)): echo 'checked'; endif; ?>>
                    <label class="form-check-label"><?php echo e(__('Featured Product')); ?></label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_best_seller" value="1"
                        <?php if(old('is_best_seller',$m?->is_best_seller ?? false)): echo 'checked'; endif; ?>>
                    <label class="form-check-label"><?php echo e(__('Best Seller')); ?></label>
                </div>
                <?php if($pfShowActive): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="active" value="1"
                        <?php if(old('active',$m?->active ?? true)): echo 'checked'; endif; ?>>
                    <label class="form-check-label"><?php echo e(__('Active')); ?></label>
                </div>
                <?php else: ?>
                    <input type="hidden" name="active" value="0">
                <?php endif; ?>
            </div>
        </div>

        <!-- Inventory Management -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    <?php echo e(__('Inventory')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="manage_stock" value="1"
                        <?php if(old('manage_stock',$m?->manage_stock ?? false)): echo 'checked'; endif; ?>>
                    <label class="form-check-label"><?php echo e(__('Manage Stock')); ?></label>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small"><?php echo e(__('Stock Qty')); ?></label>
                        <input type="number" name="stock_qty" value="<?php echo e(old('stock_qty',$m?->stock_qty ?? 0)); ?>"
                            class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label small"><?php echo e(__('Reserved')); ?></label>
                        <input type="number" name="reserved_qty"
                            value="<?php echo e(old('reserved_qty',$m?->reserved_qty ?? 0)); ?>" class="form-control">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small"><?php echo e(__('Refund Days')); ?></label>
                        <input type="number" min="0" name="refund_days"
                            value="<?php echo e(old('refund_days',$m?->refund_days ?? 0)); ?>" class="form-control">
                        <div class="form-text small">
                            <?php echo e(__('Number of days customers can request a refund; 0 = no refunds')); ?></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small"><?php echo e(__('Weight')); ?></label>
                        <input type="number" step="0.01" name="weight" value="<?php echo e(old('weight',$m?->weight)); ?>"
                            class="form-control">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label small"><?php echo e(__('Length')); ?></label>
                        <input type="number" step="0.01" name="length" value="<?php echo e(old('length',$m?->length)); ?>"
                            class="form-control">
                    </div>
                    <div class="col-4">
                        <label class="form-label small"><?php echo e(__('Width')); ?></label>
                        <input type="number" step="0.01" name="width" value="<?php echo e(old('width',$m?->width)); ?>"
                            class="form-control">
                    </div>
                    <div class="col-4">
                        <label class="form-label small"><?php echo e(__('Height')); ?></label>
                        <input type="number" step="0.01" name="height" value="<?php echo e(old('height',$m?->height)); ?>"
                            class="form-control">
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="backorder" value="1"
                        <?php if(old('backorder',$m?->backorder ?? false)): echo 'checked'; endif; ?>>
                    <label class="form-check-label"><?php echo e(__('Allow Backorder')); ?></label>
                </div>
            </div>
        </div>

        <!-- Variation Attributes Reference -->
    <div class="card modern-card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo e(__('Variation Attributes')); ?>

                </h6>
            </div>
            <div class="card-body">
                <div class="small text-muted">
                    <?php echo e(__('Use attribute dropdown inside each variation row for variable products.')); ?>

                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/products/products/_form.blade.php ENDPATH**/ ?>