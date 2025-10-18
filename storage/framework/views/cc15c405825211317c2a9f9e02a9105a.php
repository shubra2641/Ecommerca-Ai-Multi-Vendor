<!-- Sidebar -->
    <nav class="modern-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand">
                <?php if($logoPath && file_exists(public_path('storage/' . $logoPath))): ?>
                <img src="<?php echo e(asset('storage/' . $logoPath)); ?>" alt="<?php echo e($siteName); ?>" class="brand-logo">
                <?php else: ?>
                <i class="fas fa-cube brand-icon"></i>
                <?php endif; ?>
                <span class="brand-text"><?php echo e($siteName); ?></span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="sidebar-content">
            <div class="sidebar-search">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="<?php echo e(__('Search')); ?>..." id="sidebarQuickSearch">
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php if(auth()->check() && Gate::allows('access-admin')): ?>
                    <!-- Main Navigation -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Main')); ?></div>

                        <a href="<?php echo e(route('admin.dashboard')); ?>"
                            class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                            <div class="nav-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <span class="nav-text"><?php echo e(__('Dashboard')); ?></span>
                        </a>

                        <div class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.reports*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.reports*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <span class="nav-text"><?php echo e(__('Reports')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul class="dropdown-menu <?php echo e(request()->routeIs('admin.reports*') ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.reports.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.reports.index') ? 'active' : ''); ?>"><i
                                            class="fas fa-list"></i> <?php echo e(__('Reports')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.reports.inventory')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.reports.inventory') ? 'active' : ''); ?>"><i
                                            class="fas fa-warehouse"></i> <?php echo e(__('Inventory')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.reports.users')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.reports.users') ? 'active' : ''); ?>"><i
                                            class="fas fa-user"></i> <?php echo e(__('Users')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.reports.vendors')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.reports.vendors') ? 'active' : ''); ?>"><i
                                            class="fas fa-store"></i> <?php echo e(__('Vendors')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.reports.financial')); ?>" class="dropdown-item <?php echo e(request()->routeIs('admin.reports.financial') ? 'active' : ''); ?>"><i class="fas fa-file-invoice-dollar"></i> <?php echo e(__('Financial')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.reports.system')); ?>" class="dropdown-item <?php echo e(request()->routeIs('admin.reports.system') ? 'active' : ''); ?>"><i class="fas fa-server"></i> <?php echo e(__('System')); ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Users Management -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Users Management')); ?></div>

                        <div class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.users*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.users*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span class="nav-text"><?php echo e(__('Users')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>

                            <ul class="dropdown-menu <?php echo e(request()->routeIs('admin.users*') ? 'show' : ''); ?>">
                                <li>
                                    <a href="<?php echo e(route('admin.users.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.users.index') ? 'active' : ''); ?>">
                                        <i class="fas fa-list"></i>
                                        <?php echo e(__('All Users')); ?>

                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.users.create')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.users.create') ? 'active' : ''); ?>">
                                        <i class="fas fa-plus"></i>
                                        <?php echo e(__('Create User')); ?>

                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.users.pending')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.users.pending') ? 'active' : ''); ?>">
                                        <i class="fas fa-clock"></i>
                                        <?php echo e(__('Pending Users')); ?>

                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a href="<?php echo e(route('admin.balances.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.balances*') ? 'active' : ''); ?>">
                                        <i class="fas fa-wallet"></i>
                                        <?php echo e(__('User Balances')); ?>

                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.vendor.withdrawals.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.vendor.withdrawals*') ? 'active' : ''); ?>">
                                        <i class="fas fa-hand-holding-usd"></i>
                                        <?php echo e(__('Vendor Withdrawals')); ?>

                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Content Management -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Content')); ?></div>
                        <div
                            class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.blog.*')||request()->routeIs('admin.gallery*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.blog.*')||request()->routeIs('admin.gallery*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon"><i class="fas fa-folder-open"></i></div>
                                <span class="nav-text"><?php echo e(__('Content')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul
                                class="dropdown-menu <?php echo e(request()->routeIs('admin.blog.*')||request()->routeIs('admin.gallery*') ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.blog.posts.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.blog.posts*') ? 'active' : ''); ?>"><i
                                            class="fas fa-blog"></i> <?php echo e(__('Blog Posts')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.blog.categories.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.blog.categories*') ? 'active' : ''); ?>"><i
                                            class="fas fa-folder-tree"></i> <?php echo e(__('Categories')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.blog.tags.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.blog.tags*') ? 'active' : ''); ?>"><i
                                            class="fas fa-tags"></i> <?php echo e(__('Tags')); ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="<?php echo e(route('admin.gallery.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.gallery*') ? 'active' : ''); ?>"><i
                                            class="fas fa-images"></i> <?php echo e(__('Gallery')); ?></a></li>                                            
                            </ul>
                        </div>
                    </div>

                    <!-- Home & Footer Management -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Homepage')); ?></div>
                        <div class="nav-dropdown dropdown <?php echo e((request()->routeIs('admin.footer-settings.*') || request()->routeIs('admin.maintenance-settings.*') || request()->routeIs('admin.homepage.sections.*') || request()->routeIs('admin.homepage.slides.*') || request()->routeIs('admin.homepage.banners.*')) ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="<?php echo e((request()->routeIs('admin.footer-settings.*') || request()->routeIs('admin.maintenance-settings.*') || request()->routeIs('admin.homepage.sections.*') || request()->routeIs('admin.homepage.slides.*') || request()->routeIs('admin.homepage.banners.*')) ? 'true' : 'false'); ?>">
                                <div class="nav-icon"><i class="fas fa-layer-group"></i></div>
                                <span class="nav-text"><?php echo e(__('Homepage')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul class="dropdown-menu <?php echo e((request()->routeIs('admin.footer-settings.*')||request()->routeIs('admin.maintenance-settings.*')||request()->routeIs('admin.homepage.sections.*')||request()->routeIs('admin.homepage.slides.*')||request()->routeIs('admin.homepage.banners.*')) ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.homepage.sections.index')); ?>" class="dropdown-item <?php echo e(request()->routeIs('admin.homepage.sections.*') ? 'active' : ''); ?>"><i class="fas fa-list"></i> <?php echo e(__('Sections')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.homepage.slides.index')); ?>" class="dropdown-item <?php echo e(request()->routeIs('admin.homepage.slides.*') ? 'active' : ''); ?>"><i class="fas fa-images"></i> <?php echo e(__('Slides')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.homepage.banners.index')); ?>" class="dropdown-item <?php echo e(request()->routeIs('admin.homepage.banners.*') ? 'active' : ''); ?>"><i class="fas fa-ad"></i> <?php echo e(__('Banners')); ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="<?php echo e(route('admin.footer-settings.edit')); ?>" class="dropdown-item <?php echo e(request()->routeIs('admin.footer-settings.*') ? 'active' : ''); ?>"><i class="fas fa-shoe-prints"></i> <?php echo e(__('Footer Settings')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.maintenance-settings.edit')); ?>" class="dropdown-item <?php echo e(request()->routeIs('admin.maintenance-settings.*') ? 'active' : ''); ?>"><i class="fas fa-tools"></i> <?php echo e(__('Maintenance Mode')); ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Products Management -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Products')); ?></div>
                        <div
                            class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.product-categories*')||request()->routeIs('admin.product-attributes*')||request()->routeIs('admin.product-tags*')||request()->routeIs('admin.brands*')||request()->routeIs('admin.products*')||request()->routeIs('admin.reviews*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.product-categories*')||request()->routeIs('admin.product-attributes*')||request()->routeIs('admin.product-tags*')||request()->routeIs('admin.brands*')||request()->routeIs('admin.products*')||request()->routeIs('admin.reviews*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon"><i class="fas fa-box"></i></div>
                                <span class="nav-text"><?php echo e(__('Products')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul
                                class="dropdown-menu <?php echo e(request()->routeIs('admin.product-categories*')||request()->routeIs('admin.product-attributes*')||request()->routeIs('admin.product-tags*')||request()->routeIs('admin.brands*')||request()->routeIs('admin.products*')||request()->routeIs('admin.reviews*') ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.product-categories.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.product-categories*')? 'active':''); ?>"><i
                                            class="fas fa-folder-open"></i> <?php echo e(__('Categories')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.product-attributes.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.product-attributes*')? 'active':''); ?>"><i
                                            class="fas fa-shapes"></i> <?php echo e(__('Attributes')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.product-tags.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.product-tags*')? 'active':''); ?>"><i
                                            class="fas fa-tags"></i> <?php echo e(__('Tags')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.brands.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.brands*')? 'active':''); ?>"><i
                                            class="fas fa-industry"></i> <?php echo e(__('Brands')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.products.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.products*')? 'active':''); ?>"><i
                                            class="fas fa-box"></i> <?php echo e(__('Products')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.products.pending')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.products.pending')? 'active':''); ?>"><i
                                            class="fas fa-clock"></i> <?php echo e(__('pending_vendor_products')); ?>

                                            <?php if(!empty($pendingVendorProductsCount) && $pendingVendorProductsCount > 0): ?>
                                                <span class="badge bg-danger ms-2"><?php echo e($pendingVendorProductsCount); ?></span>
                                            <?php endif; ?>
                                    </a></li>
                                <li><a href="<?php echo e(route('admin.reviews.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.reviews*')? 'active':''); ?>"><i
                                            class="fas fa-star"></i> <?php echo e(__('Reviews')); ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Orders Management -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Orders')); ?></div>
                        <div class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.orders*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.orders*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon"><i class="fas fa-shopping-cart"></i></div>
                                <span class="nav-text"><?php echo e(__('Orders')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul class="dropdown-menu <?php echo e(request()->routeIs('admin.orders*') ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.orders.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.orders.index')? 'active':''); ?>"><i
                                            class="fas fa-list"></i> <?php echo e(__('All Orders')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.orders.payments')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.orders.payments')? 'active':''); ?>"><i
                                            class="fas fa-credit-card"></i> <?php echo e(__('Payments')); ?></a></li>
                                <li>
                                    <a href="<?php echo e(route('admin.returns.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.returns*')? 'active':''); ?>">
                                        <i class="fas fa-undo"></i> <?php echo e(__('Returns / Warranty')); ?>

                                        <?php if(!empty($pendingReturnsCount) && $pendingReturnsCount > 0): ?>
                                            <span class="badge bg-danger ms-2"><?php echo e($pendingReturnsCount); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Product Interest / Notifications -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Interests')); ?></div>
                        <div class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.notify.*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.notify.*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon"><i class="fas fa-bell"></i></div>
                                <span class="nav-text"><?php echo e(__('Product Notifications')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul class="dropdown-menu <?php echo e(request()->routeIs('admin.notify.*') ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.notify.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.notify.index')? 'active':''); ?>"><i
                                            class="fas fa-list"></i> <?php echo e(__('All Interests')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.notify.topProducts')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.notify.topProducts')? 'active':''); ?>"><i
                                            class="fas fa-chart-bar"></i> <?php echo e(__('Top Products')); ?></a></li>
                            </ul>
                        </div>
                        <a href="<?php echo e(route('admin.notifications.index')); ?>" class="nav-item mt-1 <?php echo e(request()->routeIs('admin.notifications.*') ? 'active' : ''); ?>">
                            <div class="nav-icon"><i class="fas fa-bell"></i></div>
                            <span class="nav-text"><?php echo e(__('Notifications')); ?></span>
                        </a>
                    </div>

                    <!-- Shipping / Locations -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Shipping')); ?></div>
                        <div
                            class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.shipping-zones*')||request()->routeIs('admin.countries*')||request()->routeIs('admin.governorates*')||request()->routeIs('admin.cities*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.shipping-zones*')||request()->routeIs('admin.countries*')||request()->routeIs('admin.governorates*')||request()->routeIs('admin.cities*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon"><i class="fas fa-shipping-fast"></i></div>
                                <span class="nav-text"><?php echo e(__('Shipping')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul
                                class="dropdown-menu <?php echo e(request()->routeIs('admin.shipping-zones*')||request()->routeIs('admin.countries*')||request()->routeIs('admin.governorates*')||request()->routeIs('admin.cities*') ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.shipping-zones.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.shipping-zones*') ? 'active' : ''); ?>"><i
                                            class="fas fa-box"></i> <?php echo e(__('Shipping Zones')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.countries.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.countries*') ? 'active' : ''); ?>"><i
                                            class="fas fa-flag"></i> <?php echo e(__('Countries')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.governorates.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.governorates*') ? 'active' : ''); ?>"><i
                                            class="fas fa-map-marker-alt"></i> <?php echo e(__('Governorates')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.cities.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.cities*') ? 'active' : ''); ?>"><i
                                            class="fas fa-city"></i> <?php echo e(__('Cities')); ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Settings / Configuration -->
                    <div class="nav-section">
                        <div class="nav-section-title"><?php echo e(__('Settings')); ?></div>
                        <div
                            class="nav-dropdown dropdown <?php echo e(request()->routeIs('admin.settings*')||request()->routeIs('admin.payment-gateways*')||request()->routeIs('admin.coupons*')||request()->routeIs('admin.languages*')||request()->routeIs('admin.currencies*')||request()->routeIs('admin.social*') ? 'show' : ''); ?>">
                            <a href="#" class="nav-item dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="<?php echo e(request()->routeIs('admin.settings*')||request()->routeIs('admin.payment-gateways*')||request()->routeIs('admin.coupons*')||request()->routeIs('admin.languages*')||request()->routeIs('admin.currencies*')||request()->routeIs('admin.social*') ? 'true' : 'false'); ?>">
                                <div class="nav-icon"><i class="fas fa-sliders-h"></i></div>
                                <span class="nav-text"><?php echo e(__('Settings')); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul
                                class="dropdown-menu <?php echo e(request()->routeIs('admin.settings*')||request()->routeIs('admin.payment-gateways*')||request()->routeIs('admin.coupons*')||request()->routeIs('admin.languages*')||request()->routeIs('admin.currencies*')||request()->routeIs('admin.social*') ? 'show' : ''); ?>">
                                <li><a href="<?php echo e(route('admin.settings.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.settings*') ? 'active' : ''); ?>"><i
                                            class="fas fa-sliders-h"></i> <?php echo e(__('Settings')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.payment-gateways.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.payment-gateways.index') || request()->routeIs('admin.payment-gateways.create') || request()->routeIs('admin.payment-gateways.edit') ? 'active' : ''); ?>"><i
                                            class="fas fa-credit-card"></i> <?php echo e(__('Payment Gateways')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.payment-gateways-management.dashboard')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.payment-gateways-management*') ? 'active' : ''); ?>"><i
                                            class="fas fa-chart-line"></i> <?php echo e(__('Gateway Management')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.coupons.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.coupons*') ? 'active' : ''); ?>"><i
                                            class="fas fa-ticket-alt"></i> <?php echo e(__('Coupons')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.languages.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.languages*') ? 'active' : ''); ?>"><i
                                            class="fas fa-language"></i> <?php echo e(__('Languages')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.currencies.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.currencies*') ? 'active' : ''); ?>"><i
                                            class="fas fa-coins"></i> <?php echo e(__('Currencies')); ?></a></li>
                                <li><a href="<?php echo e(route('admin.social.index')); ?>"
                                        class="dropdown-item <?php echo e(request()->routeIs('admin.social*') ? 'active' : ''); ?>"><i
                                            class="fas fa-share-alt"></i> <?php echo e(__('Social Links')); ?></a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </nav>
        </div>

        <div class="sidebar-footer">
            <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="nav-item logout-btn">
                    <div class="nav-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <span class="nav-text"><?php echo e(__('Logout')); ?></span>
                </button>
            </form>
        </div>
    </nav><?php /**PATH D:\xampp1\htdocs\easy\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>