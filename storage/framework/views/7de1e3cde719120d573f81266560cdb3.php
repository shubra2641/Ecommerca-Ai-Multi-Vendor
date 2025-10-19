<aside class="catalog-sidebar">
    <div class="sidebar-block">
        <h4><?php echo e(__('Price')); ?></h4>
        <form method="GET" action="<?php echo e(request()->url()); ?>" class="filter-form">
            <?php $__currentLoopData = request()->except(['min_price', 'max_price']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_array($value)): ?>
                    <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="<?php echo e($key); ?>[]" value="<?php echo e($val); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="price-range">
                <div class="value-row">
                    <span><?php echo e(__('Min')); ?>: <strong><?php echo e(request('min_price') ?: 0); ?></strong></span>
                    <span><?php echo e(__('Max')); ?>: <strong><?php echo e(request('max_price') ?: 1000); ?></strong></span>
                </div>
                <div class="price-inputs">
                    <input type="number" name="min_price" min="0" max="1000" step="10" 
                           value="<?php echo e(request('min_price') ?: 0); ?>" placeholder="<?php echo e(__('Min')); ?>" class="price-input">
                    <span class="price-separator">-</span>
                    <input type="number" name="max_price" min="0" max="1000" step="10" 
                           value="<?php echo e(request('max_price') ?: 1000); ?>" placeholder="<?php echo e(__('Max')); ?>" class="price-input">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm filter-apply-btn"><?php echo e(__('Apply')); ?></button>
        </form>
    </div>
    <div class="sidebar-block">
        <h4><?php echo e(__('Category')); ?></h4>
        <nav class="category-list">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="cat-item">
                <a href="<?php echo e(route('products.category',$cat->slug)); ?>"><?php echo e($cat->name); ?></a>
                <?php if($cat->children->count()): ?>
                <div class="cat-children">
                    <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('products.category',$child->slug)); ?>"><?php echo e($child->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </div>
    <div class="sidebar-block">
        <h4><?php echo e(__('Brand')); ?></h4>
        <form method="GET" action="<?php echo e(request()->url()); ?>" class="filter-form">
            <?php $__currentLoopData = request()->except(['brand']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_array($value)): ?>
                    <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="<?php echo e($key); ?>[]" value="<?php echo e($val); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="brand-search">
                <input type="search" name="brand_search" placeholder="<?php echo e(__('Search')); ?>" value="<?php echo e(request('brand_search')); ?>">
            </div>
            <div class="brand-list">
                <?php if(isset($brandList) && $brandList->count()): ?>
                <?php $__currentLoopData = $brandList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="filter-brand-item">
                    <input type="checkbox" name="brand[]" value="<?php echo e($b->slug); ?>" <?php echo e(in_array($b->slug,$csSelectedBrands ?? [])?'checked':''); ?>>
                    <span class="filter-brand-name"><?php echo e($b->name); ?></span>
                    <span class="filter-brand-count"><?php echo e($b->products_count); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary btn-sm filter-apply-btn"><?php echo e(__('Apply')); ?></button>
        </form>
    </div>
</aside>
<?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/products/partials/sidebar.blade.php ENDPATH**/ ?>