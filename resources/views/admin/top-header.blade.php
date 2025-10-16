        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="breadcrumb-container">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-home"></i>
                                </a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="header-right">

                <!-- Notifications -->
                <div class="header-item dropdown">
                    <button id="adminNotificationsBtn" class="header-btn notification-btn dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span id="adminNotificationBadge" class="notification-badge envato-hidden">0</span>
                    </button>
                    <ul id="adminNotificationsMenu" class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">
                            <h6 class="mb-0">{{ __('notifications.title') }}</h6>
                        </li>
                        <li class="px-3 py-2">
                            <button id="adminMarkAllReadBtn"
                                class="btn btn-sm btn-outline-secondary w-100">{{ __('notifications.mark_all_read') }}</button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li id="adminNotificationsPlaceholder">
                            <div class="px-3 py-2 text-muted">{{ __('notifications.loading') }}</div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a id="adminNotificationsViewAll" class="dropdown-item text-center"
                                href="{{ route('admin.notifications.index') ?? '#' }}">
                                {{ __('notifications.view_all') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Language Switcher -->
                <div class="header-item dropdown">
                    <button class="header-btn dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-globe"></i>
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                    </button>
                    <ul class="dropdown-menu">
                        @foreach(($dashboardAdminLanguages ?? []) as $lang)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.language.switch', [], false) }}?language={{ urlencode($lang->code) }}" role="button">
                                    @if(!empty($lang->flag))
                                        <i class="flag-icon flag-icon-{{ e($lang->flag) }}"></i>
                                    @endif
                                    {{ $lang->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="header-item dropdown">
                    <button class="user-menu-btn dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=007bff&color=fff"
                            alt="User" class="user-avatar">
                        <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                <i class="fas fa-user"></i>
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i>
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>