<aside class="account-sidebar">
    <div class="account-user-card">
        <div class="avatar-circle">{{ strtoupper(substr($user->name ?? 'U',0,2)) }}</div>
        <div class="meta">
            <div class="greet">{{ __('Hala :name!', ['name'=>strtok($user->name,' ')]) }}</div>
            <div class="email">{{ $user->email }}</div>
            <div class="progress-wrapper">
                <div class="progress-bar"><span data-progress="{{ $completion }}"></span></div>
                <div class="progress-label">{{ __('Profile Completion') }} <strong>{{ $completion }}%</strong></div>
            </div>
        </div>
    </div>
    <nav class="account-nav-groups">
        <div class="nav-group">
            <div class="group-title">{{ __('MAIN') }}</div>
            <a href="{{ url('/account') }}"
                class="nav-link {{ request()->is('account')? 'active':'' }}">{{ __('Dashboard') }}</a>
            <a href="{{ route('user.orders') }}"
                class="nav-link {{ request()->routeIs('user.orders*')? 'active':'' }}">{{ __('Orders') }}</a>
            <a href="{{ route('user.returns.index') }}"
                class="nav-link {{ request()->routeIs('user.returns*')? 'active':'' }}">{{ __('returns.title') }}</a>
            <a href="{{ route('wishlist.page') }}" class="nav-link nav-link-badge"> <span>{{ __('Wishlist') }}</span>
                <span class="badge yellow">{{ $wishlistCount }}</span></a>
            <a href="{{ route('compare.page') }}" class="nav-link nav-link-badge"> <span>{{ __('Compare') }}</span>
                <span class="badge muted">{{ $compareCount }}</span></a>
        </div>
        <div class="nav-group">
            <div class="group-title">{{ __('MY ACCOUNT') }}</div>
            <a href="{{ route('user.profile') }}"
                class="nav-link {{ request()->routeIs('user.profile')? 'active':'' }}">{{ __('Profile') }}</a>
            <a href="{{ route('user.addresses') }}"
                class="nav-link {{ request()->routeIs('user.addresses*')? 'active':'' }}">{{ __('Addresses') }}</a>
            <a href="{{ route('user.invoices') }}"
                class="nav-link {{ request()->routeIs('user.invoices')? 'active':'' }}">{{ __('Payments') }}</a>
        </div>
        <div class="nav-group">
            <div class="group-title">{{ __('OTHERS') }}</div>
            <a href="{{ route('logout') }}" class="nav-link"
                data-logout="logout-form">{{ __('Logout') }}</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </nav>
</aside>