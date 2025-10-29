<!-- Sidebar -->
<nav class="modern-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">
            @if($logoPath && file_exists(public_path('storage/' . $logoPath)))
            <img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($logoPath) }}" alt="{{ $siteName }}" class="brand-logo">
            @else
            <i class="fas fa-cube brand-icon"></i>
            @endif
            <span class="brand-text">{{ $siteName }}</span>
        </div>
    </div>

    <div class="sidebar-content">
        <div class="sidebar-search">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="{{ __('Search') }}..." id="sidebarQuickSearch">
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->check() && Gate::allows('access-admin'))
            <!-- Main Navigation -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Main') }}</div>

                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <span class="nav-text">{{ __('Dashboard') }}</span>
                </a>

                <div class="nav-dropdown dropdown {{ request()->routeIs('admin.reports*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.reports*') ? 'true' : 'false' }}">
                        <div class="nav-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="nav-text">{{ __('Reports') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu {{ request()->routeIs('admin.reports*') ? 'show' : '' }}">
                        <li><a href="{{ route('admin.reports.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}"><i
                                    class="fas fa-list"></i> {{ __('Reports') }}</a></li>
                        <li><a href="{{ route('admin.reports.inventory') }}"
                                class="dropdown-item {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}"><i
                                    class="fas fa-warehouse"></i> {{ __('Inventory') }}</a></li>
                        <li><a href="{{ route('admin.reports.vendors') }}"
                                class="dropdown-item {{ request()->routeIs('admin.reports.vendors') ? 'active' : '' }}"><i
                                    class="fas fa-store"></i> {{ __('Vendors') }}</a></li>
                        <li><a href="{{ route('admin.reports.financial') }}" class="dropdown-item {{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}"><i class="fas fa-file-invoice-dollar"></i> {{ __('Financial') }}</a></li>
                        <li><a href="{{ route('admin.reports.system') }}" class="dropdown-item {{ request()->routeIs('admin.reports.system') ? 'active' : '' }}"><i class="fas fa-server"></i> {{ __('System') }}</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Users Management -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Users Management') }}</div>

                <div class="nav-dropdown dropdown {{ request()->routeIs('admin.users*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.users*') ? 'true' : 'false' }}">
                        <div class="nav-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="nav-text">{{ __('Users') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>

                    <ul class="dropdown-menu {{ request()->routeIs('admin.users*') ? 'show' : '' }}">
                        <li>
                            <a href="{{ route('admin.users.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>
                                {{ __('All Users') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.create') }}"
                                class="dropdown-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                                <i class="fas fa-plus"></i>
                                {{ __('Create User') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.pending') }}"
                                class="dropdown-item {{ request()->routeIs('admin.users.pending') ? 'active' : '' }}">
                                <i class="fas fa-clock"></i>
                                {{ __('Pending Users') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a href="{{ route('admin.vendor.withdrawals.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.vendor.withdrawals*') ? 'active' : '' }}">
                                <i class="fas fa-hand-holding-usd"></i>
                                {{ __('Vendor Withdrawals') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Content Management -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Content') }}</div>
                <div
                    class="nav-dropdown dropdown {{ request()->routeIs('admin.blog.*')||request()->routeIs('admin.gallery*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.blog.*')||request()->routeIs('admin.gallery*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-folder-open"></i></div>
                        <span class="nav-text">{{ __('Content') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul
                        class="dropdown-menu {{ request()->routeIs('admin.blog.*')||request()->routeIs('admin.gallery*') ? 'show' : '' }}">
                        <li><a href="{{ route('admin.blog.posts.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.blog.posts*') ? 'active' : '' }}"><i
                                    class="fas fa-blog"></i> {{ __('Blog Posts') }}</a></li>
                        <li><a href="{{ route('admin.blog.categories.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.blog.categories*') ? 'active' : '' }}"><i
                                    class="fas fa-folder-tree"></i> {{ __('Categories') }}</a></li>
                        <li><a href="{{ route('admin.blog.tags.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.blog.tags*') ? 'active' : '' }}"><i
                                    class="fas fa-tags"></i> {{ __('Tags') }}</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="{{ route('admin.gallery.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.gallery*') ? 'active' : '' }}"><i
                                    class="fas fa-images"></i> {{ __('Gallery') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Home & Footer Management -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Homepage') }}</div>
                <div class="nav-dropdown dropdown {{ (request()->routeIs('admin.footer-settings.*') || request()->routeIs('admin.homepage.sections.*') || request()->routeIs('admin.homepage.slides.*') || request()->routeIs('admin.homepage.banners.*')) ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle" aria-expanded="{{ (request()->routeIs('admin.footer-settings.*') || request()->routeIs('admin.homepage.sections.*') || request()->routeIs('admin.homepage.slides.*') || request()->routeIs('admin.homepage.banners.*')) ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-layer-group"></i></div>
                        <span class="nav-text">{{ __('Homepage') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu {{ (request()->routeIs('admin.footer-settings.*')||request()->routeIs('admin.homepage.sections.*')||request()->routeIs('admin.homepage.slides.*')||request()->routeIs('admin.homepage.banners.*')) ? 'show' : '' }}">
                        <li><a href="{{ route('admin.homepage.sections.index') }}" class="dropdown-item {{ request()->routeIs('admin.homepage.sections.*') ? 'active' : '' }}"><i class="fas fa-list"></i> {{ __('Sections') }}</a></li>
                        <li><a href="{{ route('admin.homepage.slides.index') }}" class="dropdown-item {{ request()->routeIs('admin.homepage.slides.*') ? 'active' : '' }}"><i class="fas fa-images"></i> {{ __('Slides') }}</a></li>
                        <li><a href="{{ route('admin.homepage.banners.index') }}" class="dropdown-item {{ request()->routeIs('admin.homepage.banners.*') ? 'active' : '' }}"><i class="fas fa-ad"></i> {{ __('Banners') }}</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="{{ route('admin.footer-settings.edit') }}" class="dropdown-item {{ request()->routeIs('admin.footer-settings.*') ? 'active' : '' }}"><i class="fas fa-shoe-prints"></i> {{ __('Footer Settings') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Products Management -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Products') }}</div>
                <div
                    class="nav-dropdown dropdown {{ request()->routeIs('admin.product-categories*')||request()->routeIs('admin.product-attributes*')||request()->routeIs('admin.product-tags*')||request()->routeIs('admin.brands*')||request()->routeIs('admin.products*')||request()->routeIs('admin.reviews*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.product-categories*')||request()->routeIs('admin.product-attributes*')||request()->routeIs('admin.product-tags*')||request()->routeIs('admin.brands*')||request()->routeIs('admin.products*')||request()->routeIs('admin.reviews*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-box"></i></div>
                        <span class="nav-text">{{ __('Products') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul
                        class="dropdown-menu {{ request()->routeIs('admin.product-categories*')||request()->routeIs('admin.product-attributes*')||request()->routeIs('admin.product-tags*')||request()->routeIs('admin.brands*')||request()->routeIs('admin.products*')||request()->routeIs('admin.reviews*') ? 'show' : '' }}">
                        <li><a href="{{ route('admin.product-categories.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.product-categories*')? 'active':'' }}"><i
                                    class="fas fa-folder-open"></i> {{ __('Categories') }}</a></li>
                        <li><a href="{{ route('admin.product-attributes.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.product-attributes*')? 'active':'' }}"><i
                                    class="fas fa-shapes"></i> {{ __('Attributes') }}</a></li>
                        <li><a href="{{ route('admin.product-tags.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.product-tags*')? 'active':'' }}"><i
                                    class="fas fa-tags"></i> {{ __('Tags') }}</a></li>
                        <li><a href="{{ route('admin.brands.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.brands*')? 'active':'' }}"><i
                                    class="fas fa-industry"></i> {{ __('Brands') }}</a></li>
                        <li><a href="{{ route('admin.products.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.products*')? 'active':'' }}"><i
                                    class="fas fa-box"></i> {{ __('Products') }}</a></li>
                        <li><a href="{{ route('admin.products.pending') }}"
                                class="dropdown-item {{ request()->routeIs('admin.products.pending')? 'active':'' }}"><i
                                    class="fas fa-clock"></i> {{ __('pending_vendor_products') }}
                                @if(!empty($pendingVendorProductsCount) && $pendingVendorProductsCount > 0)
                                <span class="badge bg-danger ms-2">{{ $pendingVendorProductsCount }}</span>
                                @endif
                            </a></li>
                        <li><a href="{{ route('admin.reviews.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.reviews*')? 'active':'' }}"><i
                                    class="fas fa-star"></i> {{ __('Reviews') }}</a></li>
                        <li><a href="{{ route('admin.products.serials.all') }}" class="dropdown-item"><i class="fas fa-list-ol"></i> {{ __('Serials') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Orders Management -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Orders') }}</div>
                <div class="nav-dropdown dropdown {{ request()->routeIs('admin.orders*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.orders*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-shopping-cart"></i></div>
                        <span class="nav-text">{{ __('Orders') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu {{ request()->routeIs('admin.orders*') ? 'show' : '' }}">
                        <li><a href="{{ route('admin.orders.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.orders.index')? 'active':'' }}"><i
                                    class="fas fa-list"></i> {{ __('All Orders') }}</a></li>
                        <li><a href="{{ route('admin.orders.payments') }}"
                                class="dropdown-item {{ request()->routeIs('admin.orders.payments')? 'active':'' }}"><i
                                    class="fas fa-credit-card"></i> {{ __('Payments') }}</a></li>
                        <li>
                            <a href="{{ route('admin.returns.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.returns*')? 'active':'' }}">
                                <i class="fas fa-undo"></i> {{ __('Returns / Warranty') }}
                                @if(!empty($pendingReturnsCount) && $pendingReturnsCount > 0)
                                <span class="badge bg-danger ms-2">{{ $pendingReturnsCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Product Interest / Notifications -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Interests') }}</div>
                <div class="nav-dropdown dropdown {{ request()->routeIs('admin.notify.*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.notify.*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-bell"></i></div>
                        <span class="nav-text">{{ __('Product Notifications') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu {{ request()->routeIs('admin.notify.*') ? 'show' : '' }}">
                        <li><a href="{{ route('admin.notify.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.notify.index')? 'active':'' }}"><i
                                    class="fas fa-list"></i> {{ __('All Interests') }}</a></li>
                        <li><a href="{{ route('admin.notify.topProducts') }}"
                                class="dropdown-item {{ request()->routeIs('admin.notify.topProducts')? 'active':'' }}"><i
                                    class="fas fa-chart-bar"></i> {{ __('Top Products') }}</a></li>
                    </ul>
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="nav-item mt-1 {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <div class="nav-icon"><i class="fas fa-bell"></i></div>
                    <span class="nav-text">{{ __('Notifications') }}</span>
                </a>
            </div>

            <!-- Shipping / Locations -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Shipping') }}</div>
                <div
                    class="nav-dropdown dropdown {{ request()->routeIs('admin.shipping-zones*')||request()->routeIs('admin.countries*')||request()->routeIs('admin.governorates*')||request()->routeIs('admin.cities*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.shipping-zones*')||request()->routeIs('admin.countries*')||request()->routeIs('admin.governorates*')||request()->routeIs('admin.cities*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-shipping-fast"></i></div>
                        <span class="nav-text">{{ __('Shipping') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul
                        class="dropdown-menu {{ request()->routeIs('admin.shipping-zones*')||request()->routeIs('admin.countries*')||request()->routeIs('admin.governorates*')||request()->routeIs('admin.cities*') ? 'show' : '' }}">
                        <li><a href="{{ route('admin.shipping-zones.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.shipping-zones*') ? 'active' : '' }}"><i
                                    class="fas fa-box"></i> {{ __('Shipping Zones') }}</a></li>
                        <li><a href="{{ route('admin.countries.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.countries*') ? 'active' : '' }}"><i
                                    class="fas fa-flag"></i> {{ __('Countries') }}</a></li>
                        <li><a href="{{ route('admin.governorates.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.governorates*') ? 'active' : '' }}"><i
                                    class="fas fa-map-marker-alt"></i> {{ __('Governorates') }}</a></li>
                        <li><a href="{{ route('admin.cities.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.cities*') ? 'active' : '' }}"><i
                                    class="fas fa-city"></i> {{ __('Cities') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Settings / Configuration -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Settings') }}</div>
                <div
                    class="nav-dropdown dropdown {{ request()->routeIs('admin.settings*')||request()->routeIs('admin.payment-gateways*')||request()->routeIs('admin.coupons*')||request()->routeIs('admin.languages*')||request()->routeIs('admin.currencies*')||request()->routeIs('admin.social*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('admin.settings*')||request()->routeIs('admin.payment-gateways*')||request()->routeIs('admin.coupons*')||request()->routeIs('admin.languages*')||request()->routeIs('admin.currencies*')||request()->routeIs('admin.social*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-sliders-h"></i></div>
                        <span class="nav-text">{{ __('Settings') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul
                        class="dropdown-menu {{ request()->routeIs('admin.settings*')||request()->routeIs('admin.payment-gateways*')||request()->routeIs('admin.maintenance-settings.*')||request()->routeIs('admin.coupons*')||request()->routeIs('admin.languages*')||request()->routeIs('admin.currencies*')||request()->routeIs('admin.social*') ? 'show' : '' }}">
                        <li><a href="{{ route('admin.settings.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><i
                                    class="fas fa-sliders-h"></i> {{ __('Settings') }}</a></li>
                        <li><a href="{{ route('admin.coupons.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}"><i
                                    class="fas fa-ticket-alt"></i> {{ __('Coupons') }}</a></li>
                        <li><a href="{{ route('admin.languages.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.languages*') ? 'active' : '' }}"><i
                                    class="fas fa-language"></i> {{ __('Languages') }}</a></li>
                        <li><a href="{{ route('admin.currencies.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.currencies*') ? 'active' : '' }}"><i
                                    class="fas fa-coins"></i> {{ __('Currencies') }}</a></li>
                        <li><a href="{{ route('admin.social.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.social*') ? 'active' : '' }}"><i
                                    class="fas fa-share-alt"></i> {{ __('Social Links') }}</a></li>
                        <li><a href="{{ route('admin.maintenance-settings.edit') }}" class="dropdown-item {{ request()->routeIs('admin.maintenance-settings.*') ? 'active' : '' }}"><i class="fas fa-tools"></i> {{ __('Maintenance Mode') }}</a></li>

                    </ul>
                </div>
            </div>
            @endif
        </nav>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="nav-item logout-btn">
                <div class="nav-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <span class="nav-text">{{ __('Logout') }}</span>
            </button>
        </form>
    </div>
</nav>