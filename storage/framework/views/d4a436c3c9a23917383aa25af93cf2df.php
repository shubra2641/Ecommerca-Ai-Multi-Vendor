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
<script id="dashboard-data" type="application/json">
{
    "charts": {
        "users": {
            "labels": <?php echo json_encode($chartData['labels'] ?? [], 15, 512) ?>,
            "data": <?php echo json_encode($chartData['data'] ?? [], 15, 512) ?>
        },
        "sales": {
            "labels": <?php echo json_encode($salesChartData['labels'] ?? [], 15, 512) ?>,
            "orders": <?php echo json_encode($salesChartData['orders'] ?? [], 15, 512) ?>,
            "revenue": <?php echo json_encode($salesChartData['revenue'] ?? [], 15, 512) ?>
        },
        "ordersStatus": {
            "labels": <?php echo json_encode($orderStatusChartData['labels'] ?? [], 15, 512) ?>,
            "data": <?php echo json_encode($orderStatusChartData['data'] ?? [], 15, 512) ?>,
            "colors": <?php echo json_encode($orderStatusChartData['colors'] ?? [], 15, 512) ?>
        }
    }
}
</script>

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
                        <small class="text-muted" id="chart-last-updated">
                            <?php if($period === '6m'): ?>
                            <?php echo e(__('Last 6 months overview')); ?>

                            <?php elseif($period === '1y'): ?>
                            <?php echo e(__('Last 12 months overview')); ?>

                            <?php else: ?>
                            <?php echo e(__('All time overview')); ?>

                            <?php endif; ?>
                        </small>
                    </div>
                    <div
                        class="chart-controls-wrapper d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 <?php if(app()->getLocale()==='ar'): ?> ms-sm-auto flex-sm-row-reverse <?php else: ?> ms-sm-auto <?php endif; ?>">
                        <a href="<?php echo e(route('admin.dashboard', ['refresh' => '1'])); ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                            title="<?php echo e(__('Refresh dashboard data')); ?>">
                            <i class="fas fa-sync-alt"></i>
                            <span class="d-none d-md-inline ms-1"><?php echo e(__('Refresh')); ?></span>
                        </a>
                        <div class="btn-group btn-group-sm chart-period-buttons <?php if(app()->getLocale()==='ar'): ?> order-sm-first <?php endif; ?>"
                            role="group">
                            <a href="<?php echo e(route('admin.dashboard', ['period' => '6m'])); ?>"
                                class="btn btn-outline-secondary <?php echo e($period === '6m' ? 'active' : ''); ?>">6M</a>
                            <a href="<?php echo e(route('admin.dashboard', ['period' => '1y'])); ?>"
                                class="btn btn-outline-secondary <?php echo e($period === '1y' ? 'active' : ''); ?>">1Y</a>
                            <a href="<?php echo e(route('admin.dashboard', ['period' => 'all'])); ?>"
                                class="btn btn-outline-secondary <?php echo e($period === 'all' ? 'active' : ''); ?>"><?php echo e(__('All')); ?></a>
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

    <!-- Top Active Users -->
    <div class="col-lg-4 mb-4">
        <div class="card modern-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('Top Active Users')); ?></h5>
                        <small class="text-muted"><?php echo e(__('Most active users this week')); ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#topUsersCollapseNew" aria-expanded="true">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse show d-lg-block" id="topUsersCollapseNew">
                    <?php if(isset($topUsers) && count($topUsers) > 0): ?>
                    <div class="user-list">
                        <?php $__currentLoopData = $topUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="user-item">
                            <div class="user-avatar">
                                <div
                                    class="bg-primary text-white d-flex align-items-center justify-content-center h-100 rounded">
                                    <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                </div>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?php echo e($user->name); ?></div>
                                <div class="user-activity"><?php echo e(__('Last updated')); ?>:
                                    <?php echo e($user->updated_at->diffForHumans()); ?>

                                </div>
                            </div>
                            <div class="user-badge">
                                <span class="badge badge-success">
                                    <?php echo e(__('Active')); ?>

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
                            <?php echo e(__('Revenue')); ?>: <?php echo e($salesChartData['revenue'][$i] ?? 0); ?>

                        </li>
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


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>