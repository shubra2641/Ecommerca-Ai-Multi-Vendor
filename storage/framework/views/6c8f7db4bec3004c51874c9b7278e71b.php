<aside class="account-sidebar">
    <div class="account-user-card">
        <div class="avatar-circle"><?php echo e(strtoupper(substr($user->name ?? 'U',0,2))); ?></div>
        <div class="meta">
            <div class="greet"><?php echo e(__('Hala :name!', ['name'=>strtok($user->name,' ')])); ?></div>
            <div class="email"><?php echo e($user->email); ?></div>
            <div class="progress-wrapper">
                <div class="progress-bar"><span data-progress="<?php echo e($completion); ?>"></span></div>
                <div class="progress-label"><?php echo e(__('Profile Completion')); ?> <strong><?php echo e($completion); ?>%</strong></div>
            </div>
        </div>
    </div>
    <nav class="account-nav-groups">
        <div class="nav-group">
            <div class="group-title"><?php echo e(__('MAIN')); ?></div>
            <a href="<?php echo e(url('/account')); ?>"
                class="nav-link <?php echo e(request()->is('account')? 'active':''); ?>"><?php echo e(__('Dashboard')); ?></a>
            <a href="<?php echo e(route('user.orders')); ?>"
                class="nav-link <?php echo e(request()->routeIs('user.orders*')? 'active':''); ?>"><?php echo e(__('Orders')); ?></a>
            <a href="<?php echo e(route('user.returns.index')); ?>"
                class="nav-link <?php echo e(request()->routeIs('user.returns*')? 'active':''); ?>"><?php echo e(__('returns.title')); ?></a>
            <a href="<?php echo e(route('wishlist.page')); ?>" class="nav-link nav-link-badge"> <span><?php echo e(__('Wishlist')); ?></span>
                <span class="badge yellow"><?php echo e($wishlistCount); ?></span></a>
            <a href="<?php echo e(route('compare.page')); ?>" class="nav-link nav-link-badge"> <span><?php echo e(__('Compare')); ?></span>
                <span class="badge muted"><?php echo e($compareCount); ?></span></a>
        </div>
        <div class="nav-group">
            <div class="group-title"><?php echo e(__('MY ACCOUNT')); ?></div>
            <a href="<?php echo e(route('user.profile')); ?>"
                class="nav-link <?php echo e(request()->routeIs('user.profile')? 'active':''); ?>"><?php echo e(__('Profile')); ?></a>
            <a href="<?php echo e(route('user.addresses')); ?>"
                class="nav-link <?php echo e(request()->routeIs('user.addresses*')? 'active':''); ?>"><?php echo e(__('Addresses')); ?></a>
            <a href="<?php echo e(route('user.invoices')); ?>"
                class="nav-link <?php echo e(request()->routeIs('user.invoices')? 'active':''); ?>"><?php echo e(__('Payments')); ?></a>
        </div>
        <div class="nav-group">
            <div class="group-title"><?php echo e(__('OTHERS')); ?></div>
            <a href="<?php echo e(route('logout')); ?>" class="nav-link"
                data-logout="logout-form"><?php echo e(__('Logout')); ?></a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
        </div>
    </nav>
</aside><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/account/_sidebar.blade.php ENDPATH**/ ?>