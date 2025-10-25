{{-- All header presentation variables are now provided by HeaderComposer (no inline PHP) --}}

<header class="noon-header" role="banner">
    <div class="noon-header-bar">
        <div class="noon-left">
            <a href="/" class="noon-logo" aria-label="{{ $siteName }}">
                @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                <img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($logoPath) }}" alt="{{ $siteName }}">
                @else
                <span class="txt">{{ $siteName }}</span>
                @endif
            </a>
            {{-- Additional pages dropdown (replaces delivery/ship widget) --}}
            <div class="noon-pages" aria-label="Pages">
                <div class="act act-pages" data-dropdown>
                    <button class="dropdown-trigger" aria-haspopup="true" aria-expanded="false">
                        <span class="txt">{{ __('Pages') }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-panel size-sm" role="menu">
                    </div>
                </div>
            </div>
        </div>
        <div class="noon-search">
            <form action="{{ route('products.index') }}" method="GET" role="search" aria-label="Site search">
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="{{ __('What are you looking for?') }}" />
                <button type="submit" aria-label="{{ __('Search') }}">🔍</button>
            </form>
        </div>
        <div class="noon-actions" aria-label="User tools">
            <!-- Language & Currency -->
            <div class="act act-lang-curr" data-dropdown>
                <button class="dropdown-trigger" aria-haspopup="true" aria-expanded="false">
                    <span class="txt">{{ strtoupper(app()->getLocale()) }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-panel size-sm" role="menu">
                    <div class="panel-section">
                        <div class="panel-title">{{ __('Language') }}</div>
                        @foreach($activeLanguages as $lang)
                        <form method="POST" action="{{ route('language.switch') }}" class="panel-action">@csrf<input
                                type="hidden" name="language" value="{{ $lang->code }}"><button type="submit"
                                @disabled(app()->getLocale()===$lang->code)>{{ $lang->name }}</button></form>
                        @endforeach
                    </div>
                    @if($currencies->count())
                    <div class="panel-section">
                        <div class="panel-title">{{ __('Currency') }}</div>
                        <div class="currency-grid">
                            @foreach($currencies as $cur)
                            <button type="button"
                                class="currency-chip {{ $cur->id==($currentCurrency->id??null)?'is-active':'' }}"
                                data-currency="{{ $cur->code }}">{{ $cur->code }}</button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Account -->
            <div class="act act-account" data-dropdown>
                <button class="dropdown-trigger" aria-haspopup="true" aria-expanded="false">
                    <span class="avatar-circle"
                        aria-hidden="true">{{ $userName ? strtoupper(substr($userName,0,1)) : '👤' }}</span>
                    <span class="txt small">@if($userName) {{ __('Ahlan') }} {{ $userName }}! @else {{ __('Account') }}
                        @endif</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-panel" role="menu">
                    @auth
                    <div class="menu-list">
                        @if(Route::has('user.orders'))
                        <a href="{{ route('user.orders') }}" class="menu-item" role="menuitem">
                            <span class="mi-icon">📄</span><span>{{ __('Orders') }}</span>
                        </a>
                        @endif
                        @if(Route::has('user.addresses'))
                        <a href="{{ route('user.addresses') }}" class="menu-item" role="menuitem">
                            <span class="mi-icon">📦</span><span>{{ __('Addresses') }}</span>
                        </a>
                        @endif
                        @if(Route::has('user.invoices'))
                        <a href="{{ route('user.invoices') }}" class="menu-item" role="menuitem">
                            <span class="mi-icon">💳</span><span>{{ __('Payments') }}</span>
                        </a>
                        @endif
                        @if(Route::has('user.profile'))
                        <a href="{{ route('user.profile') }}" class="menu-item" role="menuitem">
                            <span class="mi-icon">⚙</span><span>{{ __('Profile') }}</span>
                        </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="menu-item" role="none">@csrf<button
                                type="submit" role="menuitem" class="logout-btn"><span class="mi-icon">⏻</span><span>
                                    {{ __('Logout') }}</span></button></form>
                    </div>
                    @else
                    <div class="menu-list">
                        @if(Route::has('login'))<a href="{{ route('login') }}" class="menu-item" role="menuitem">🔐
                            {{ __('Login') }}</a>@endif
                        @if(Route::has('register'))<a href="{{ route('register') }}" class="menu-item" role="menuitem">➕
                            {{ __('Register') }}</a>@endif
                    </div>
                    @endauth
                </div>
            </div>
            <!-- Wishlist -->
            <div class="act act-wishlist">
                <a href="{{ route('wishlist.page') }}" aria-label="{{ __('Wishlist') }}" class="circle-btn icon-btn"
                    data-tooltip="{{ __('Wishlist') }}">
                    <i class="fas fa-heart" aria-hidden="true"></i>
                    <span class="badge" data-wishlist-count>{{ $wishlistCount ?? 0 }}</span>
                </a>
            </div>
            <!-- Compare -->
            <div class="act act-compare">
                <a href="{{ route('compare.page') }}" aria-label="{{ __('Compare') }}" class="circle-btn icon-btn"
                    data-tooltip="{{ __('Compare') }}">
                    <i class="fas fa-chart-bar" aria-hidden="true"></i>
                    <span class="badge" data-compare-count>{{ $compareCount ?? 0 }}</span>
                </a>
            </div>
            <!-- Cart -->
            <div class="act act-cart">
                <a href="{{ route('cart.index') }}" class="circle-btn icon-btn" data-tooltip="{{ __('Cart') }}"
                    aria-label="{{ __('Cart') }}">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                    <span class="badge" aria-live="polite">{{ $cartCount }}</span>
                </a>
            </div>
            <div id="wishlist-config" hidden></div>
        </div>
    </div>
    <nav class="noon-cats" aria-label="Main categories">
        <ul class="cat-list">
            @foreach($rootCats as $cat)
            <li>
                <a href="{{ route('products.category',$cat->slug) }}">{{ $cat->name }}</a>
            </li>
            @endforeach
            <li class="more"><button type="button" aria-label="More">›</button></li>
        </ul>
    </nav>
</header>
<div id="currency-config" data-symbol='{{ e(json_encode($currency_symbol ?? "$", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}' data-default='{{ e(json_encode($defaultCurrency ?? null, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'></div>