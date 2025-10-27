        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            </div>

            <div class="header-right">

                <!-- Notifications -->
                <div class="header-item dropdown">
                    <button id="adminNotificationsBtn" class="header-btn notification-btn dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @if($adminUnreadCount > 0)
                        <span class="notification-badge">{{ $adminUnreadCount }}</span>
                        @endif
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
                        @if($adminNotifications->count() > 0)
                        @foreach($adminNotifications as $notification)
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-bell"></i>
                                {{ $notification->data['title'] ?? 'Notification' }}
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </a>
                        </li>
                        @endforeach
                        @else
                        <li>
                            <div class="px-3 py-2 text-muted">لا توجد إشعارات</div>
                        </li>
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="btn btn-sm btn-outline-secondary w-100"
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
                        @foreach($dashboardAdminLanguages as $lang)
                        <li>
                            <form method="POST" action="{{ route('language.switch') }}" style="display: inline;">
                                @csrf
                                <input type="hidden" name="language" value="{{ $lang->code }}">
                                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left; padding: 0.375rem 1.5rem;">
                                    {{ $lang->name }}
                                </button>
                            </form>
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
                                <i class="fas fa-user-circle"></i>
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