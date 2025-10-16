<footer class="footer-new">

  <?php if($sections['support_bar']): ?>
  <div class="footer-support-bar">
      <div class="support-item"><strong><?php echo e($supportHeading); ?></strong><span><?php echo e($supportSub); ?></span></div>
  <div class="support-channel"><span class="icon">🛈</span><span class="label"><?php echo e($helpCenterLabel); ?></span><a href="#">help.<?php echo e(config('app.name')); ?>.com</a></div>
  <div class="support-channel"><span class="icon">✉️</span><span class="label"><?php echo e($emailSupportLabel); ?></span><a href="mailto:<?php echo e(config('mail.from.address','support@example.com')); ?>"><?php echo e(config('mail.from.address','support@example.com')); ?></a></div>
  <div class="support-channel"><span class="icon">📞</span><span class="label"><?php echo e($phoneSupportLabel); ?></span><span><?php echo e(config('app.phone','16358')); ?></span></div>
  </div>
  <?php endif; ?>

  <div class="footer-apps-social">
      <?php if($sections['apps']): ?>
      <div class="apps">
          <span class="apps-title"><?php echo e($appsHeading); ?></span>
          <div class="app-badges-row">
          <?php $__currentLoopData = $orderedApps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform=>$app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="app-badge" href="<?php echo e($app['url']); ?>" aria-label="<?php echo e(ucfirst($platform)); ?> Store">
                  <?php if(!empty($app['image'])): ?>
                    <img src="<?php echo e(asset('storage/'.$app['image'])); ?>" alt="<?php echo e(ucfirst($platform)); ?>" class="app-badge-img">
                  <?php else: ?>
                    <?php echo e(ucfirst($platform)); ?>

                  <?php endif; ?>
                </a>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
      </div>
      <?php endif; ?>
      <?php if($sections['social']): ?>
      <div class="social-connect">
          <span class="social-title"><?php echo e($socialHeading); ?></span>
          <div class="social-icons">
            <?php $__empty_1 = true; $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <a href="<?php echo e($link->url); ?>" aria-label="<?php echo e($link->label ?? ucfirst($link->platform)); ?>" target="_blank" rel="noopener" class="soc soc-<?php echo e($link->platform); ?>">
                <?php if($link->icon): ?><i class="<?php echo e($link->icon); ?>" aria-hidden="true"></i><?php else: ?> <?php echo e(strtoupper(substr($link->label ?? $link->platform,0,2))); ?> <?php endif; ?>
              </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <span class="no-social text-muted"><?php echo e(__('No social links')); ?></span>
            <?php endif; ?>
          </div>
      </div>
      <?php endif; ?>
  </div>

  <div class="footer-legal footer-legal-row">
      <div class="copyright legal-center">&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. <?php echo e($rightsLine); ?></div>
      <?php if($sections['payments']): ?>
      <div class="payments legal-right" aria-label="Payment Methods">
          <?php $__currentLoopData = array_slice($paymentList,0,6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="pm"><?php echo e(strtoupper($pm)); ?></span>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endif; ?>
  </div>
</footer>
<?php /**PATH D:\xampp\htdocs\easy\resources\views/front/partials/footer_extended.blade.php ENDPATH**/ ?>