<?php $__env->startSection('title', __('Users Management')); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', [
    'title' => __('Users Management'),
    'subtitle' => __('Manage all users and their permissions'),
    'actions' => '<a href="'.route('admin.users.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> '.e(__('Add New User')).'</a> <a href="'.route('admin.users.export').'" class="btn btn-secondary"><i class="fas fa-download"></i> '.e(__('Export')).'</a>'
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$users->total()); ?>"><?php echo e($users->total()); ?></div>
                    <div class="stats-label"><?php echo e(__('Total Users')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success"><?php echo e(__('from last month')); ?></span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$users->where('approved_at', '!=', null)->count()); ?>"><?php echo e($users->where('approved_at', '!=', null)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Approved')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+<?php echo e(number_format((($users->where('approved_at', '!=', null)->count() / max($users->total(), 1)) * 100), 1)); ?>%</span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-warning h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$users->where('approved_at', null)->count()); ?>"><?php echo e($users->where('approved_at', null)->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Pending')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-clock text-muted"></i>
                        <span class="text-muted"><?php echo e(__('Awaiting approval')); ?></span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-user-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-info h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="<?php echo e((int)$users->where('role', 'vendor')->count()); ?>"><?php echo e($users->where('role', 'vendor')->count()); ?></div>
                    <div class="stats-label"><?php echo e(__('Vendors')); ?></div>
                    <div class="stats-trend">
                        <i class="fas fa-chart-line text-success"></i>
                        <span class="text-success"><?php echo e(__('Active vendors')); ?></span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-store"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card modern-card">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="filters-form">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="form-group mb-3">
                        <label for="search" class="form-label"><?php echo e(__('Search')); ?></label>
                        <input type="text" id="search" name="search" value="<?php echo e(request('search')); ?>" 
                               class="form-control" placeholder="<?php echo e(__('Search by name, email...')); ?>">
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="form-group mb-3">
                        <label for="role" class="form-label"><?php echo e(__('Role')); ?></label>
                        <select id="role" name="role" class="form-select">
                            <option value=""><?php echo e(__('All Roles')); ?></option>
                            <option value="admin" <?php echo e(request('role') === 'admin' ? 'selected' : ''); ?>><?php echo e(__('Admin')); ?></option>
                            <option value="vendor" <?php echo e(request('role') === 'vendor' ? 'selected' : ''); ?>><?php echo e(__('Vendor')); ?></option>
                            <option value="customer" <?php echo e(request('role') === 'customer' ? 'selected' : ''); ?>><?php echo e(__('Customer')); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="form-group mb-3">
                        <label for="status" class="form-label"><?php echo e(__('Status')); ?></label>
                        <select id="status" name="status" class="form-select">
                            <option value=""><?php echo e(__('All Statuses')); ?></option>
                            <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>><?php echo e(__('Approved')); ?></option>
                            <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>><?php echo e(__('Pending')); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="form-group mb-3">
                        <label for="per_page" class="form-label"><?php echo e(__('Per Page')); ?></label>
                        <select id="per_page" name="per_page" class="form-select">
                            <option value="10" <?php echo e(request('per_page', 15) == 10 ? 'selected' : ''); ?>>10</option>
                            <option value="15" <?php echo e(request('per_page', 15) == 15 ? 'selected' : ''); ?>>15</option>
                            <option value="25" <?php echo e(request('per_page', 15) == 25 ? 'selected' : ''); ?>>25</option>
                            <option value="50" <?php echo e(request('per_page', 15) == 50 ? 'selected' : ''); ?>>50</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-9 col-lg-3">
                    <div class="form-group mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                                <i class="fas fa-search"></i>
                                <span class="d-none d-sm-inline"><?php echo e(__('Filter')); ?></span>
                            </button>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary w-100 w-sm-auto">
                                <i class="fas fa-times"></i>
                                <span class="d-none d-sm-inline"><?php echo e(__('Clear')); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card modern-card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <h3 class="card-title mb-0"><?php echo e(__('Users List')); ?></h3>
        <div class="card-actions">
            <div class="bulk-actions d-flex flex-column flex-sm-row gap-2" id="bulkActions">
                <span class="selected-count text-muted">0</span> <span class="text-muted d-none d-sm-inline"><?php echo e(__('selected')); ?></span>
                <button type="button" class="btn btn-sm btn-success" data-action="bulk-approve">
                    <i class="fas fa-check"></i>
                    <span class="d-none d-md-inline"><?php echo e(__('Approve')); ?></span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-action="bulk-delete">
                    <i class="fas fa-trash"></i>
                    <span class="d-none d-md-inline"><?php echo e(__('Delete')); ?></span>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if($users->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="select-all"></th>
                            <th><?php echo e(__('User')); ?></th>
                            <th class="d-none d-md-table-cell"><?php echo e(__('Role')); ?></th>
                            <th class="d-none d-lg-table-cell"><?php echo e(__('Status')); ?></th>
                            <th class="d-none d-lg-table-cell"><?php echo e(__('Balance')); ?></th>
                            <th class="d-none d-xl-table-cell"><?php echo e(__('Phone')); ?></th>
                            <th class="d-none d-lg-table-cell"><?php echo e(__('Joined')); ?></th>
                            <th width="150"><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="<?php echo e($user->id); ?>">
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                        </div>
                                        <div>
                                            <div class="user-name"><?php echo e($user->name); ?></div>
                                            <div class="user-email"><?php echo e($user->email); ?></div>
                                            <div class="d-md-none mt-1">
                                                <?php switch($user->role):
                                                    case ('admin'): ?>
                                                        <span class="badge bg-danger"><?php echo e(__('Admin')); ?></span>
                                                        <?php break; ?>
                                                    <?php case ('vendor'): ?>
                                                        <span class="badge bg-warning"><?php echo e(__('Vendor')); ?></span>
                                                        <?php break; ?>
                                                    <?php default: ?>
                                                        <span class="badge bg-secondary"><?php echo e(__('Customer')); ?></span>
                                                <?php endswitch; ?>
                                                <?php if($user->approved_at): ?>
                                                    <span class="badge bg-success ms-1">
                                                        <i class="fas fa-check"></i>
                                                        <?php echo e(__('Approved')); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning ms-1">
                                                        <i class="fas fa-clock"></i>
                                                        <?php echo e(__('Pending')); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?php switch($user->role):
                                        case ('admin'): ?>
                                            <span class="badge bg-danger"><?php echo e(__('Admin')); ?></span>
                                            <?php break; ?>
                                        <?php case ('vendor'): ?>
                                            <span class="badge bg-warning"><?php echo e(__('Vendor')); ?></span>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <span class="badge bg-secondary"><?php echo e(__('Customer')); ?></span>
                                    <?php endswitch; ?>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <?php if($user->approved_at): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i>
                                            <?php echo e(__('Approved')); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i>
                                            <?php echo e(__('Pending')); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="text-success">
                                        $<?php echo e(number_format($user->balance ?? 0, 2)); ?>

                                    </span>
                                </td>
                                <td class="d-none d-xl-table-cell"><?php echo e($user->phone ?? '-'); ?></td>
                                <td class="d-none d-lg-table-cell"><?php echo e($user->created_at->format('Y-m-d')); ?></td>
                                <td>
                                    <div class="btn-group d-flex flex-column flex-sm-row">
                                        <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-sm btn-outline-secondary mb-1 mb-sm-0" title="<?php echo e(__('View')); ?>">
                                            <i class="fas fa-eye"></i>
                                            <span class="d-sm-none ms-1"><?php echo e(__('View')); ?></span>
                                        </a>
                                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-sm btn-outline-secondary mb-1 mb-sm-0" title="<?php echo e(__('Edit')); ?>">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-sm-none ms-1"><?php echo e(__('Edit')); ?></span>
                                        </a>
                                        <?php if(!$user->approved_at): ?>
                                            <form method="POST" action="<?php echo e(route('admin.users.approve', $user)); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success mb-1 mb-sm-0" title="<?php echo e(__('Approve')); ?>">
                                                    <i class="fas fa-check"></i>
                                                    <span class="d-sm-none ms-1"><?php echo e(__('Approve')); ?></span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>" class="d-inline delete-form">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="<?php echo e(__('Delete this user?')); ?>" data-confirm="<?php echo e(__('Delete this user?')); ?>">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-sm-none ms-1"><?php echo e(__('Delete')); ?></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="pagination-info">
                    <?php echo e(__('Showing')); ?> <?php echo e($users->firstItem()); ?> <?php echo e(__('to')); ?> <?php echo e($users->lastItem()); ?> 
                    <?php echo e(__('of')); ?> <?php echo e($users->total()); ?> <?php echo e(__('results')); ?>

                </div>
                <?php echo e($users->links()); ?>

            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users fa-3x"></i>
                <h3><?php echo e(__('No Users Found')); ?></h3>
                <p><?php echo e(__('No users match your current filters. Try adjusting your search criteria.')); ?></p>
                <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <?php echo e(__('Add First User')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/users/index.blade.php ENDPATH**/ ?>