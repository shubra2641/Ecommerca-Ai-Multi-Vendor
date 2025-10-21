<!-- Sidebar -->
<nav class="modern-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">
            @if($logoPath && file_exists(public_path('storage/' . $logoPath)))
            <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $siteName }}" class="brand-logo">
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
            @if(auth()->check() && Gate::allows('access-vendor'))
            <!-- Main Navigation -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Main') }}</div>

                <a href="{{ Route::has('vendor.dashboard') ? route('vendor.dashboard') : '#' }}"
                    class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <span class="nav-text">{{ __('Dashboard') }}</span>
                </a>
                @if(Route::has('vendor.performance.index'))
                <a href="{{ route('vendor.performance.index') }}" class="nav-item {{ request()->routeIs('vendor.performance.*') ? 'active' : '' }}">
                    <div class="nav-icon"><i class="fas fa-gauge-high"></i></div>
                    <span class="nav-text">{{ __('Performance') }}</span>
                </a>
                @endif
            </div>

            <!-- Products Management -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Products') }}</div>
                <div class="nav-dropdown dropdown {{ request()->routeIs('vendor.products*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('vendor.products*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-box"></i></div>
                        <span class="nav-text">{{ __('Products') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu {{ request()->routeIs('vendor.products*') ? 'show' : '' }}">
                        <li><a href="{{ Route::has('vendor.products.index') ? route('vendor.products.index') : '#' }}"
                                class="dropdown-item {{ request()->routeIs('vendor.products.index')? 'active':'' }}"><i
                                    class="fas fa-list"></i> {{ __('All Products') }}</a></li>
                        <li><a href="{{ Route::has('vendor.products.create') ? route('vendor.products.create') : '#' }}"
                                class="dropdown-item {{ request()->routeIs('vendor.products.create')? 'active':'' }}"><i
                                    class="fas fa-plus"></i> {{ __('Add Product') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Orders Management -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Orders') }}</div>
                <div class="nav-dropdown dropdown {{ request()->routeIs('vendor.orders*') ? 'show' : '' }}">
                    <a href="#" class="nav-item dropdown-toggle"
                        aria-expanded="{{ request()->routeIs('vendor.orders*') ? 'true' : 'false' }}">
                        <div class="nav-icon"><i class="fas fa-shopping-cart"></i></div>
                        <span class="nav-text">{{ __('Orders') }}</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu {{ request()->routeIs('vendor.orders*') ? 'show' : '' }}">
                        <li><a href="{{ Route::has('vendor.orders.index') ? route('vendor.orders.index') : '#' }}"
                                class="dropdown-item {{ request()->routeIs('vendor.orders.index')? 'active':'' }}"><i
                                    class="fas fa-list"></i> {{ __('All Orders') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Withdrawals / Payouts -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Payments') }}</div>
                <a href="{{ Route::has('vendor.withdrawals.index') ? route('vendor.withdrawals.index') : '#' }}"
                    class="nav-item {{ request()->routeIs('vendor.withdrawals.*') ? 'active' : '' }}">
                    <div class="nav-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <span class="nav-text">{{ __('Withdrawals') }}</span>
                </a>
            </div>

            <!-- Notifications / Interests (vendor) -->
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Notifications') }}</div>
                <a href="{{ Route::has('vendor.notifications.index') ? route('vendor.notifications.index') : '#' }}" class="nav-item {{ request()->routeIs('vendor.notifications*') ? 'active' : '' }}">
                    <div class="nav-icon"><i class="fas fa-bell"></i></div>
                    <span class="nav-text">{{ __('Notifications') }}</span>
                </a>
            </div>
            @endif
        </nav>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
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