        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <line x1="3" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <line x1="3" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            </div>

            <div class="header-right">
                <!-- Notifications (vendor) -->
                <div class="header-item dropdown">
                    <button id="vendorNotificationsBtn" class="header-btn notification-btn dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span id="vendorNotificationBadge" class="notification-badge {{ $unreadNotificationsCount > 0 ? '' : 'envato-hidden' }}">{{ $unreadNotificationsCount }}</span>
                    </button>
                    <ul id="vendorNotificationsMenu" class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">
                            <h6 class="mb-0">{{ __('notifications.title') }}</h6>
                        </li>
                        <li class="px-3 py-2">
                            <form method="POST" action="{{ route('vendor.notifications.markAll') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">{{ __('notifications.mark_all_read') }}</button>
                            </form>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @if($recentNotifications->count() > 0)
                        @foreach($recentNotifications as $notification)
                        <li class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                            <div class="notification-content">
                                <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                <div class="notification-message">{{ $notification->data['message'] ?? '' }}</div>
                                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$notification->read_at)
                            <form method="POST" action="{{ route('vendor.notifications.read', $notification->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link p-0 ms-2">
                                    <i class="fas fa-check text-success"></i>
                                </button>
                            </form>
                            @endif
                        </li>
                        @endforeach
                        @else
                        <li class="px-3 py-2 text-muted">{{ __('notifications.no_notifications') }}</li>
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a id="vendorNotificationsViewAll" class="dropdown-item text-center"
                                href="{{ Route::has('vendor.notifications.index') ? route('vendor.notifications.index') : '#' }}">
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
                        {{-- Languages: prefer $vendorLanguages (legacy), fallback to $languages provided globally --}}
                        @if(isset($vendorLanguages) && count($vendorLanguages))
                        @php($__langs = $vendorLanguages)
                        @else
                        @php($__langs = $languages ?? collect())
                        @endif
                        @foreach($__langs as $lang)
                        <li>
                            <a class="dropdown-item" href="{{ url('/vendor/language') }}?language={{ urlencode($lang->code) }}">
                                @if(!empty($lang->flag))
                                <i class="flag-icon flag-icon-{{ e($lang->flag) }}"></i>
                                @endif
                                {{ $lang->name }}
                            </a>
                        </li>
                        @endforeach
                        @if(collect($__langs)->isEmpty())
                        <li class="dropdown-item text-muted small">{{ __('No languages configured') }}</li>
                        @endif
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
                            <a class="dropdown-item" href="{{ route('user.profile') }}">
                                <i class="fas fa-user"></i>
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
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