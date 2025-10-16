<?php $__env->startSection('title', __('Admin Dashboard')); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item active"><?php echo e(__('Dashboard')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo e(__('Welcome to the Admin Panel')); ?></h1>
        <p class="page-description"><?php echo e(__('Overview of your system statistics and quick actions')); ?></p>
    </div>
    <div class="page-actions">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus"></i>
                    <span class="d-none d-sm-inline ms-1"><?php echo e(__('Quick Actions')); ?></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(route('admin.users.create')); ?>">
                            <i class="fas fa-user-plus"></i> <?php echo e(__('Add New User')); ?>

                        </a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('admin.currencies.create')); ?>">
                            <i class="fas fa-coins"></i> <?php echo e(__('Add Currency')); ?>

                        </a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('admin.languages.create')); ?>">
                            <i class="fas fa-language"></i> <?php echo e(__('Add Language')); ?>

                        </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Dashboard Data Bridge for Unified Charts -->
<script id="dashboard-data" type="application/json"><?php echo json_encode([
    'charts' => [
        'users' => [
            'labels' => $chartData['labels'] ?? [],
            'data'   => $chartData['data'] ?? [],
        ],
        'sales' => [
            'labels' => $salesChartData['labels'] ?? [],
            'orders' => $salesChartData['orders'] ?? [],
            'revenue'=> $salesChartData['revenue'] ?? [],
        ],
        'ordersStatus' => [
            'labels' => $orderStatusChartData['labels'] ?? [],
            'data'   => $orderStatusChartData['data'] ?? [],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-danger" data-stat="totalUsers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-users" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total registered users count')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['totalUsers'] ?? 0)); ?>">
                        <?php echo e(isset($stats['totalUsers']) ? number_format($stats['totalUsers']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Total Users')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+12%</span>
                        <small><?php echo e(__('from last month')); ?></small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Vendors -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-success" data-stat="totalVendors">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-vendors" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total registered vendors count')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['totalVendors'] ?? 0)); ?>">
                        <?php echo e(isset($stats['totalVendors']) ? number_format($stats['totalVendors']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Total Vendors')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+8%</span>
                        <small><?php echo e(__('from last month')); ?></small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['role' => 'vendor'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Users -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-primary" data-stat="pendingUsers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="pending-users" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Users waiting for approval')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['pendingUsers'] ?? 0)); ?>">
                        <?php echo e(isset($stats['pendingUsers']) ? number_format($stats['pendingUsers']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Pending Approvals')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-clock text-warning"></i>
                        <span class="text-muted"><?php echo e(__('Needs attention')); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.pending')); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Active Today -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-info" data-stat="activeToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="active-today" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Users active today')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['activeToday'] ?? 0)); ?>">
                        <?php echo e(isset($stats['activeToday']) ? number_format($stats['activeToday']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Active Today')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-chart-line text-info"></i>
                        <span class="text-info"><?php echo e(__('Real-time')); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['filter' => 'active_today'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics Row -->
<div class="row mb-4">
    <!-- Total Balance -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-success" data-stat="totalBalance">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-balance" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total balance in the system')); ?>" data-countup data-decimals="2"
                        data-target="<?php echo e(number_format($stats['totalBalance'] ?? 0, 2, '.', '')); ?>">
                        <?php echo e(isset($stats['totalBalance']) ? number_format($stats['totalBalance'], 2) : '0.00'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Total Balance')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-dollar-sign text-success"></i>
                        <span class="text-success"><?php echo e($defaultCurrency ? $defaultCurrency->code : ''); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="#" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- New Users Today -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-danger" data-stat="newUsersToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="new-users-today" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Users registered today')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['newUsersToday'] ?? 0)); ?>">
                        <?php echo e(isset($stats['newUsersToday']) ? number_format($stats['newUsersToday']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('New Users Today')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-user-plus text-primary"></i>
                        <span class="text-primary"><?php echo e(__('Today')); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['filter' => 'today'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Admins -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-danger" data-stat="totalAdmins">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-admins" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total administrators in the system')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['totalAdmins'] ?? 0)); ?>">
                        <?php echo e(isset($stats['totalAdmins']) ? number_format($stats['totalAdmins']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Total Admins')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-user-shield text-danger"></i>
                        <span class="text-danger"><?php echo e(__('Admins')); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['role' => 'admin'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-info" data-stat="totalCustomers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-customers" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total customers in the system')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['totalCustomers'] ?? 0)); ?>">
                        <?php echo e(isset($stats['totalCustomers']) ? number_format($stats['totalCustomers']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Total Customers')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-users text-info"></i>
                        <span class="text-info"><?php echo e(__('Customers')); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['role' => 'customer'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- New Users This Week -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-danger" data-stat="newUsersThisWeek">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="new-users-week" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Users registered this week')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['newUsersThisWeek'] ?? 0)); ?>">
                        <?php echo e(isset($stats['newUsersThisWeek']) ? number_format($stats['newUsersThisWeek']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('New Users This Week')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-calendar-week text-primary"></i>
                        <span class="text-primary"><?php echo e(__('This Week')); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['filter' => 'this_week'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- New Users This Month -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-success" data-stat="newUsersThisMonth">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="new-users-month" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Users registered this month')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['newUsersThisMonth'] ?? 0)); ?>">
                        <?php echo e(isset($stats['newUsersThisMonth']) ? number_format($stats['newUsersThisMonth']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('New Users This Month')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-calendar-alt text-success"></i>
                        <span class="text-success"><?php echo e(__('This Month')); ?></span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['filter' => 'this_month'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Approved Users -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-info" data-stat="approvedUsers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="approved-users" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total approved users')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['approvedUsers'] ?? 0)); ?>">
                        <?php echo e(isset($stats['approvedUsers']) ? number_format($stats['approvedUsers']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Approved Users')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-check-circle text-info"></i>
                        <span
                            class="text-info"><?php echo e(isset($topStats['approval_rate']) ? $topStats['approval_rate'] . '%' : '0%'); ?></span>
                        <small><?php echo e(__('approval rate')); ?></small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="<?php echo e(route('admin.users.index', ['status' => 'approved'])); ?>" class="stats-link">
                    <?php echo e(__('View Details')); ?>

                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Orders & Revenue Statistics Row -->
<div class="row mb-4">
    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-danger" data-stat="totalOrders">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-orders" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total Orders')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['totalOrders'] ?? 0)); ?>">
                        <?php echo e(isset($stats['totalOrders']) ? number_format($stats['totalOrders']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Total Orders')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-shopping-cart text-primary"></i>
                        <small class="text-muted"><?php echo e(__('All time')); ?></small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-success" data-stat="revenueTotal">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-revenue" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Total Revenue')); ?>" data-countup data-decimals="2"
                        data-target="<?php echo e(number_format($stats['revenueTotal'] ?? 0, 2, '.', '')); ?>">
                        <?php echo e(isset($stats['revenueTotal']) ? number_format($stats['revenueTotal'], 2) : '0.00'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Total Revenue')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-dollar-sign text-success"></i>
                        <small class="text-success"><?php echo e($defaultCurrency->code ?? ''); ?></small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Today -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-primary" data-stat="ordersToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="orders-today" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Orders Today')); ?>" data-countup
                        data-target="<?php echo e((int)($stats['ordersToday'] ?? 0)); ?>">
                        <?php echo e(isset($stats['ordersToday']) ? number_format($stats['ordersToday']) : '0'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Orders Today')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-calendar-day text-warning"></i>
                        <small class="text-muted"><?php echo e(__('Today')); ?></small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Today -->
    <div class="col-xl-3 col-md-6 mb-4">
    <div class="stats-card stats-card-info" data-stat="revenueToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="revenue-today" data-bs-toggle="tooltip"
                        title="<?php echo e(__('Revenue Today')); ?>" data-countup data-decimals="2"
                        data-target="<?php echo e(number_format($stats['revenueToday'] ?? 0, 2, '.', '')); ?>">
                        <?php echo e(isset($stats['revenueToday']) ? number_format($stats['revenueToday'], 2) : '0.00'); ?>

                    </div>
                    <div class="stats-label"><?php echo e(__('Revenue Today')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-chart-line text-info"></i>
                        <small class="text-info"><?php echo e($defaultCurrency->code ?? ''); ?></small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Chart Section -->
    <div class="col-lg-8 mb-4">
        <div class="modern-card">
            <div class="card-header">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('User Registration Trends')); ?></h5>
                        <small class="text-muted" id="chart-last-updated"><?php echo e(__('Last 6 months overview')); ?></small>
                    </div>
                    <div
                        class="chart-controls-wrapper d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 <?php if(app()->getLocale()==='ar'): ?> ms-sm-auto flex-sm-row-reverse <?php else: ?> ms-sm-auto <?php endif; ?>">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="auto-refresh-toggle">
                            <label class="form-check-label" for="auto-refresh-toggle">
                                <span class="d-none d-sm-inline"><?php echo e(__('Auto Refresh')); ?></span>
                                <span class="d-sm-none"><?php echo e(__('Auto')); ?></span>
                            </label>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" id="refresh-dashboard" data-bs-toggle="tooltip"
                            title="<?php echo e(__('Refresh dashboard data')); ?>">
                            <i class="fas fa-sync-alt"></i>
                            <span class="d-none d-md-inline ms-1"><?php echo e(__('Refresh')); ?></span>
                        </button>
                        <div class="btn-group btn-group-sm chart-period-buttons <?php if(app()->getLocale()==='ar'): ?> order-sm-first <?php endif; ?>"
                            role="group">
                            <button type="button" class="btn btn-outline-secondary chart-period-btn active"
                                data-period="6m">6M</button>
                            <button type="button" class="btn btn-outline-secondary chart-period-btn"
                                data-period="1y">1Y</button>
                            <button type="button" class="btn btn-outline-secondary chart-period-btn"
                                data-period="all"><?php echo e(__('All')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body position-relative">
                <!-- Chart Container -->
                <div class="chart-container h-400 pos-relative">
                    <canvas id="userChart" aria-describedby="userChartFallback"></canvas>
                    <noscript>
                        <ul id="userChartFallback" class="chart-fallback list-unstyled small mt-2">
                            <?php $__currentLoopData = ($chartData['labels'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($lbl); ?>: <?php echo e($chartData['data'][$i] ?? 0); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if(empty($chartData['labels'])): ?>
                            <li><?php echo e(__('No chart data available')); ?></li>
                            <?php endif; ?>
                        </ul>
                    </noscript>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-4 mb-4">
        <div class="modern-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('Recent Activity')); ?></h5>
                        <small class="text-muted"><?php echo e(__('Latest system activities')); ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#recentActivityCollapse" aria-expanded="true">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="collapse show d-lg-block" id="recentActivityCollapse">
                    <div class="activity-list" id="recent-activity">
                        <?php if(isset($recentActivity) && count($recentActivity) > 0): ?>
                        <?php $__currentLoopData = ($recentActivityWithGrad ?? $recentActivity); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="activity-item">
                            <div class="activity-icon <?php echo e($activity['gradient'] ?? 'bg-grad-warn'); ?>">
                                <i class="fas <?php echo e($activity['icon']); ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?php echo e($activity['title']); ?></div>
                                <div class="activity-description"><?php echo e($activity['description']); ?></div>
                                <div class="activity-time"><?php echo e($activity['time']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        <!-- Sample activity items -->
                        <div class="activity-item">
                            <div class="activity-icon bg-grad-approval">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?php echo e(__('New user registered')); ?></div>
                                <div class="activity-description"><?php echo e(__('Ahmed Mohamed joined the platform')); ?></div>
                                <div class="activity-time"><?php echo e(__('2 minutes ago')); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer text-center">
                        <a href="#" class="btn btn-sm btn-outline-primary"><?php echo e(__('View All Activities')); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales & Orders Charts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-chart-area me-2"></i><?php echo e(__('Sales & Revenue')); ?></h5>
                <small class="text-muted"><?php echo e(__('Last 30 days')); ?></small>
            </div>
            <div class="card-body h-380 pos-relative">
                <canvas id="salesChart" aria-describedby="salesChartFallback"></canvas>
                <noscript>
                    <ul id="salesChartFallback" class="chart-fallback list-unstyled small mt-2">
                        <?php ($labels = $salesChartData['labels'] ?? []); ?>
                        <?php $__currentLoopData = $labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($lbl); ?> — <?php echo e(__('Orders')); ?>: <?php echo e($salesChartData['orders'][$i] ?? 0); ?>,
                            <?php echo e(__('Revenue')); ?>: <?php echo e($salesChartData['revenue'][$i] ?? 0); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(empty($labels)): ?>
                        <li><?php echo e(__('No sales data available')); ?></li>
                        <?php endif; ?>
                    </ul>
                </noscript>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i><?php echo e(__('Order Status Distribution')); ?>

                </h5>
            </div>
            <div class="card-body h-380 pos-relative">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Additional Widgets -->
<div class="row">
    <!-- Quick Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card modern-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('System Overview')); ?></h5>
                        <small class="text-muted"><?php echo e(__('System health and performance')); ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#systemOverviewCollapse" aria-expanded="true">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse show d-lg-block" id="systemOverviewCollapse">
                    <?php if(isset($systemHealth)): ?>
                    <div class="progress-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="progress-label">
                                <i class="fas fa-database text-success me-1"></i>
                                <?php echo e(__('Database Connection')); ?>

                            </span>
                            <span class="progress-value">
                                <?php if($systemHealth['database']): ?>
                                <span class="text-success"><i class="fas fa-check-circle"></i>
                                    <?php echo e(__('Connected')); ?></span>
                                <?php else: ?>
                                <span class="text-danger"><i class="fas fa-times-circle"></i>
                                    <?php echo e(__('Disconnected')); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="progress">
                            <div
                                class="progress-bar <?php echo e($systemHealth['database'] ? 'bg-success w-100p' : 'bg-danger w-0p'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="progress-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="progress-label">
                                <i class="fas fa-memory text-success me-1"></i>
                                <?php echo e(__('Cache System')); ?>

                            </span>
                            <span class="progress-value">
                                <?php if($systemHealth['cache']): ?>
                                <span class="text-success"><i class="fas fa-check-circle"></i>
                                    <?php echo e(__('Working')); ?></span>
                                <?php else: ?>
                                <span class="text-warning"><i class="fas fa-exclamation-triangle"></i>
                                    <?php echo e(__('Issues')); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="progress">
                            <div
                                class="progress-bar <?php echo e($systemHealth['cache'] ? 'bg-success w-100p' : 'bg-warning w-50p'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="progress-label">
                                <i class="fas fa-hdd text-warning me-1"></i>
                                <?php echo e(__('Storage System')); ?>

                            </span>
                            <span class="progress-value">
                                <?php if($systemHealth['storage']): ?>
                                <span class="text-success"><i class="fas fa-check-circle"></i>
                                    <?php echo e(__('Available')); ?></span>
                                <?php else: ?>
                                <span class="text-danger"><i class="fas fa-times-circle"></i> <?php echo e(__('Issues')); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="progress">
                            <div
                                class="progress-bar <?php echo e($systemHealth['storage'] ? 'bg-success w-100p' : 'bg-danger w-0p'); ?>">
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <p class="text-muted mt-2"><?php echo e(__('System health data not available')); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Users -->
    <div class="col-lg-6 mb-4">
        <div class="card modern-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('Top Active Users')); ?></h5>
                        <small class="text-muted"><?php echo e(__('Most active users this week')); ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#topUsersCollapse" aria-expanded="true">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse show d-lg-block" id="topUsersCollapse">
                    <?php if(isset($topUsers) && count($topUsers) > 0): ?>
                    <div class="user-list">
                        <?php $__currentLoopData = $topUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="user-item">
                            <div class="user-avatar">
                                <?php if($user->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>">
                                <?php else: ?>
                                <div
                                    class="bg-primary text-white d-flex align-items-center justify-content-center h-100 rounded">
                                    <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?php echo e($user->name); ?></div>
                                <div class="user-activity"><?php echo e(__('Last active')); ?>:
                                    <?php echo e($user->last_activity ? $user->last_activity->diffForHumans() : __('Never')); ?>

                                </div>
                            </div>
                            <div class="user-badge">
                                <span class="badge badge-<?php echo e($user->is_active ? 'success' : 'warning'); ?>">
                                    <?php echo e($user->is_active ? __('Active') : __('Inactive')); ?>

                                </span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-users text-muted"></i>
                        <p class="text-muted mt-2"><?php echo e(__('No active users data available')); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>