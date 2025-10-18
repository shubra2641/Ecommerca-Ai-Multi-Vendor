<?php $__env->startSection('title', $post->seo_title ?? $post->title); ?>
<?php $__env->startSection('meta'); ?>
<?php if($post->seo_description): ?>
<meta name="description" content="<?php echo e($post->seo_description); ?>"><?php endif; ?>
<?php if($post->seo_tags): ?>
<meta name="keywords" content="<?php echo e($post->seo_tags); ?>"><?php endif; ?>
<meta property="og:type" content="article">
<meta property="og:title" content="<?php echo e($post->seo_title ?? $post->title); ?>">
<?php if($post->seo_description): ?>
<meta property="og:description" content="<?php echo e($post->seo_description); ?>"><?php endif; ?>
<meta property="og:url" content="<?php echo e(url()->current()); ?>">
<?php if($post->featured_image): ?>
<meta property="og:image" content="<?php echo e(asset('storage/'.$post->featured_image)); ?>"><?php endif; ?>
<meta property="og:site_name" content="<?php echo e(config('app.name')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo e(route('home')); ?>" class="breadcrumb-item"><?php echo e(__('Home')); ?></a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?php echo e(route('blog.index')); ?>" class="breadcrumb-item"><?php echo e(__('Blog')); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active"><?php echo e($post->title); ?></span>
        </nav>
    <h1 class="page-title"><?php echo e($post->title); ?></h1>
    </div>
</section>

<!-- Blog Post Section -->
<section class="blog-post-section">
    <div class="container">
        <div class="blog-post-layout">
            <article class="blog-post">
                <div class="post-meta">
                    <span><?php echo e($post->published_at?->format('M d, Y')); ?></span>
                    <?php if($post->category): ?>
                        <a href="<?php echo e(route('blog.category',$post->category->slug)); ?>" class="link-dark"><?php echo e($post->category->name); ?></a>
                    <?php endif; ?>
                </div>
                <?php if($post->featured_image): ?>
                    <div class="post-featured-image">
                        <img src="<?php echo e(asset('storage/'.$post->featured_image)); ?>" alt="<?php echo e($post->title); ?>">
                    </div>
                <?php endif; ?>
                <div class="post-content content-style">
                    <?php echo e($post->body); ?>

                </div>
                <?php if($post->tags->count()): ?>
                    <div class="post-tags">
                        <?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('blog.tag',$tag->slug)); ?>">#<?php echo e($tag->name); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </article>
            <aside class="post-sidebar">
                <div>
                    <h3><?php echo e(__('Recent Posts')); ?></h3>
                    <ul>
                        <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a href="<?php echo e(route('blog.show',$r->slug)); ?>"><?php echo e($r->title); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/blog/show.blade.php ENDPATH**/ ?>