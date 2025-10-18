<?php $__env->startSection('title','Blog'); ?>
<?php $__env->startSection('content'); ?>
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo e(route('home')); ?>" class="breadcrumb-item"><?php echo e(__('Home')); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active"><?php echo e(__('Blog')); ?></span>
        </nav>
        <h1 class="page-title"><?php echo e(__('Blog')); ?></h1>
        <p class="page-description"><?php echo e(__('Stay updated with our latest news and insights')); ?></p>
    </div>
</section>

<!-- Blog Posts Section -->
<section class="blog-section">
    <div class="container">
        <?php if($posts->count()): ?>
            <div class="blog-grid">
                <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="blog-card">
                        <?php echo $__env->make('front.components.post-card',['post'=>$post], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($posts->hasPages()): ?>
                <div class="pagination-wrapper"><?php echo e($posts->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <h3 class="empty-state-title"><?php echo e(__('No Blog Posts Yet')); ?></h3>
                <p class="empty-state-description"><?php echo e(__('Check back later for our latest updates and insights.')); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/blog/index.blade.php ENDPATH**/ ?>