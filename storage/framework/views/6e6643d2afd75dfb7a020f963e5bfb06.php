<?php $__env->startSection('title', __('My Account').' - '.config('app.name')); ?>
<?php $__env->startSection('content'); ?>

<section class="account-section">
    <div class="container account-grid">
        <?php echo $__env->make('front.account._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <main class="account-main">
            <div class="dashboard-page">
                <div class="dashboard-main">
                    <div class="top-intro">
                        <h1 class="page-title"><?php echo e(__('Overview')); ?></h1>
                    </div>

                    <div class="dashboard-overview">
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-blue">🏷</div>
                                <div class="dash-stats">
                                    <div class="big"><?php echo e($stats['orders_total']); ?></div>
                                    <div class="small muted"><?php echo e(__('Orders')); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-green">✓</div>
                                <div class="dash-stats">
                                    <div class="big"><?php echo e($stats['orders_completed']); ?></div>
                                    <div class="small muted"><?php echo e(__('Completed')); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-yellow">＋</div>
                                <div class="dash-stats">
                                    <div class="big"><?php echo e($stats['orders_pending']); ?></div>
                                    <div class="small muted"><?php echo e(__('In Progress')); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-blue">💳</div>
                                <div class="dash-stats">
                                    <div class="big"><?php echo e(number_format($stats['payments_total'])); ?></div>
                                    <div class="small muted"><?php echo e(__('Payments')); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-violet">🏦</div>
                                <div class="dash-stats">
                                    <div class="big"><?php echo e(auth()->user()->formatted_balance ?? auth()->user()->formatted_balance); ?></div>
                                    <div class="small muted"><?php echo e(__('Balance')); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-red">💸</div>
                                <div class="dash-stats">
                                    <div class="big"><?php echo e(number_format($stats['payments_completed'],2)); ?></div>
                                    <div class="small muted"><?php echo e(__('Spent')); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-violet">⚪</div>
                                <div class="dash-stats">
                                    <div class="big"><?php echo e($stats['profile_completion']); ?>%</div>
                                    <div class="small muted"><?php echo e(__('Profile')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-main-content">
                        <div class="panel">
                            <h4><?php echo e(__('Recent Orders')); ?></h4>
                            <?php if(!$recentOrders->count()): ?><div class="muted small"><?php echo e(__('No orders yet.')); ?></div><?php else: ?>
                            <div class="items-table recent-list">
                                <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row">
                                    <div class="label">#<?php echo e($o->id); ?> · <?php echo e($o->created_at->format('d M')); ?> ·
                                        <?php echo e($o->items->count()); ?> <?php echo e(__('items')); ?></div>
                                    <div class="value"><?php echo e(number_format($o->total,2)); ?> <?php echo e($o->currency); ?> <a
                                            class="btn btn-primary btn-place"
                                            href="<?php echo e(route('user.orders.show',$o)); ?>"><?php echo e(__('View')); ?></a></div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="panel">
                            <h4><?php echo e(__('Recent Payments')); ?></h4>
                            <?php if(!$recentPayments->count()): ?><div class="muted small"><?php echo e(__('No payments yet.')); ?></div>
                            <?php else: ?>
                            <div class="items-table recent-list">
                                <?php $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row">
                                    <div class="label">#<?php echo e($p->id); ?> · <?php echo e($p->created_at->format('d M')); ?></div>
                                    <div class="value"><?php echo e(number_format($p->amount,2)); ?> <?php echo e($p->currency); ?></div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/account/dashboard.blade.php ENDPATH**/ ?>