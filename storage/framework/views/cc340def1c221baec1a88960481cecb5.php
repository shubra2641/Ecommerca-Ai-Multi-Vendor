<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['post']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['post']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="card h-100 shadow-sm" data-glow data-anim>
  <?php if($post->featured_image): ?>
    <img src="<?php echo e(asset('storage/'.$post->featured_image)); ?>" class="card-img-top" alt="<?php echo e($post->title); ?>" loading="lazy" data-skel>
  <?php endif; ?>
  <div class="card-body d-flex flex-column">
    <h5 class="card-title mb-1"><a href="<?php echo e(route('blog.show',$post->slug)); ?>" class="text-decoration-none"><?php echo e($post->title); ?></a></h5>
    <div class="text-muted small mb-2"><?php echo e($post->published_at?->format('Y-m-d')); ?> <?php if($post->category): ?> • <?php echo e($post->category->name); ?> <?php endif; ?></div>
    <p class="card-text small flex-grow-1 mb-2"><?php echo e($post->excerpt); ?></p>
    <a href="<?php echo e(route('blog.show',$post->slug)); ?>" class="btn btn-sm btn-outline-primary mt-auto align-self-start"><?php echo e(__('Read More')); ?></a>
  </div>
</div><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/components/post-card.blade.php ENDPATH**/ ?>