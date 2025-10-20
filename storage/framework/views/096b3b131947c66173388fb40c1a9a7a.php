<?php $__env->startSection('title', $category->seo_title ?: ($category->name.' - Shop')); ?>
<?php $__env->startPush('meta'); ?>
<?php if($category->seo_description): ?>
<meta name="description" content="<?php echo e($category->seo_description); ?>"><?php endif; ?>
<?php if($category->seo_keywords): ?>
<meta name="keywords" content="<?php echo e($category->seo_keywords); ?>"><?php endif; ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<section class="products-section">
    <div class="container container-wide">
        <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Products'), 'url' => route('products.index')],
            ['title' => $category->name, 'url' => '#']
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Products'), 'url' => route('products.index')],
            ['title' => $category->name, 'url' => '#']
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
    <h1 class="results-title"><?php echo e(method_exists($products, 'total') ? $products->total() : $products->count()); ?> <?php echo e(__('Results')); ?> - <?php echo e($category->name); ?></h1>
        <div class="catalog-layout">
            <?php echo $__env->make('front.products.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="catalog-main">
                <form method="GET" action="<?php echo e(route('products.index')); ?>" id="catalogFilters">
                    <div class="filters-row">
                        <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="<?php echo e(__('Search')); ?>"
                            class="filter-input" />
                        <select name="category" class="filter-select">
                            <option value=""><?php echo e(__('All Categories')); ?></option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->slug); ?>" <?php echo e(request('category')==$cat->slug?'selected':''); ?>>
                                <?php echo e($cat->name); ?></option>
                            <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($child->slug); ?>" <?php echo e(request('category')==$child->slug?'selected':''); ?>>—
                                <?php echo e($child->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="sort" class="filter-select">
                            <option value=""><?php echo e(__('Recommended')); ?></option>
                            <option value="price_asc" <?php echo e(request('sort')=='price_asc'?'selected':''); ?>>
                                <?php echo e(__('Price: Low to High')); ?></option>
                            <option value="price_desc" <?php echo e(request('sort')=='price_desc'?'selected':''); ?>>
                                <?php echo e(__('Price: High to Low')); ?></option>
                        </select>
                        <label class="flag"><input type="checkbox" name="featured" value="1"
                                <?php echo e(request('featured')?'checked':''); ?>> <span><?php echo e(__('Featured')); ?></span></label>
                        <label class="flag"><input type="checkbox" name="sale" value="1"
                                <?php echo e(request('sale')?'checked':''); ?>> <span><?php echo e(__('On Sale')); ?></span></label>
                        <label class="flag"><input type="checkbox" name="best" value="1"
                                <?php echo e(request('best')?'checked':''); ?>> <span><?php echo e(__('Best')); ?></span></label>
                        <button type="submit" class="btn btn-primary btn-compact"><?php echo e(__('Apply')); ?></button>
                        <?php if(request()->query()): ?>
                        <a href="<?php echo e(route('products.index')); ?>"
                            class="btn btn-outline btn-compact"><?php echo e(__('Reset')); ?></a>
                        <?php endif; ?>
                        <div class="results-badge"><?php echo e(method_exists($products, 'total') ? $products->total() : $products->count()); ?></div>
                    </div>
                </form>
                <div class="chips-row">
                    <?php if(request('category')): ?>
                    <div class="chip"><?php echo e(request('category')); ?> <a href="<?php echo e(route('products.index')); ?>">×</a></div>
                    <?php endif; ?>
                    <?php if(request('q')): ?>
                    <div class="chip"><?php echo e(__('Search')); ?>: "<?php echo e(request('q')); ?>" <a
                            href="<?php echo e(route('products.index')); ?>">×</a></div>
                    <?php endif; ?>
                </div>
                <div class="products-grid">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('front.products.partials.product-card', ['product' => $product, 'wishlistIds' =>
                    $wishlistIds ?? [], 'compareIds' => $compareIds ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php $__env->startComponent('front.components.empty-state', [
                        'title' => __('No products found'),
                        'message' => __('Try adjusting your filters or search terms to find what you\'re looking for.'),
                        'actionLabel' => __('Clear All Filters'),
                        'actionUrl' => route('products.index')
                    ]); ?><?php echo $__env->renderComponent(); ?>
                    <?php endif; ?>
                </div>
                <?php if($products->hasPages()): ?>
                <div class="pagination-wrapper"><?php echo e($products->appends(request()->query())->links()); ?></div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/products/category.blade.php ENDPATH**/ ?>