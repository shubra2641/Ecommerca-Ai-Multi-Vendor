{{-- All header presentation variables are now provided by HeaderComposer (no inline PHP) --}}

<header class="noon-header" role="banner">
    <div class="noon-header-bar">
        <div class="noon-left">
            <a href="/" class="noon-logo" aria-label="{{ $siteName }}">
                @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                    <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $siteName }}">
                @else
                    <span class="txt">{{ $siteName }}</span>
                @endif
            </a>
            {{-- Additional pages dropdown (replaces delivery/ship widget) --}}
            <div class="noon-pages" aria-label="Pages">
                <div class="act act-pages" data-dropdown>
                    <button class="dropdown-trigger" aria-haspopup="true" aria-expanded="false">
                        <span class="txt">{{ __('Pages') }}</span>
                        <svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true">
                            <path fill="currentColor" d="M1.2 3.2 5 7l3.8-3.8-.9-.9L5 5.2 2.1 2.3z" />
                        </svg>
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
                    <svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true">
                        <path fill="currentColor" d="M1.2 3.2 5 7l3.8-3.8-.9-.9L5 5.2 2.1 2.3z" />
                    </svg>
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
                    <svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true">
                        <path fill="currentColor" d="M1.2 3.2 5 7l3.8-3.8-.9-.9L5 5.2 2.1 2.3z" />
                    </svg>
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
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor"
                            d="M12 21s-1-.55-1-1.24S12 17 12 17s1 1.76 1 2.76S12 21 12 21m6.5-9.24c-1.21 0-2.87 2.4-3.5 3.37.19.31.5.87.5 1.63 0 .34-.05.66-.13.97 1.09-.32 2.36-.86 3.56-1.64 1.64-1.04 2.57-2.36 2.57-3.72 0-1.88-1.53-3.61-3.5-3.61m-13 0c-1.97 0-3.5 1.73-3.5 3.61 0 1.36.93 2.68 2.57 3.72 1.2.78 2.47 1.32 3.56 1.64-.08-.31-.13-.63-.13-.97 0-.76.31-1.32.5-1.63-.63-.97-2.29-3.37-3.5-3.37M17.5 6c-2.05 0-3.72 1.25-4.5 3.09C12.22 7.25 10.55 6 8.5 6A4.49 4.49 0 0 0 4 10.5c0 2.57 2.2 4.67 5.5 7.86l1.5 1.39 1.5-1.39C17.8 15.17 20 13.07 20 10.5 20 7.99 18.5 6 17.5 6Z" />
                    </svg>
                    <span class="badge" data-wishlist-count>{{ $wishlistCount ?? 0 }}</span>
                </a>
            </div>
            <!-- Compare -->
            <div class="act act-compare">
                <a href="{{ route('compare.page') }}" aria-label="{{ __('Compare') }}" class="circle-btn icon-btn"
                    data-tooltip="{{ __('Compare') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M3 5h2v14H3zm4 0h2v10H7zm4 0h2v6h-2zm4 0h2v12h-2zm4 0h2v8h-2z" />
                    </svg>
                    <span class="badge" data-compare-count>{{ $compareCount ?? 0 }}</span>
                </a>
            </div>
            <!-- Cart -->
            <div class="act act-cart">
                <a href="{{ route('cart.index') }}" class="circle-btn icon-btn" data-tooltip="{{ __('Cart') }}"
                    aria-label="{{ __('Cart') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor"
                            d="M17 18a2 2 0 0 1 2 2 2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-8 0a2 2 0 0 1 2 2 2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m11.22-13a1 1 0 0 1 1 .78l2.25 10A1 1 0 0 1 22.5 17H7.21l.54 2.36A1 1 0 0 1 6.79 21H4a1 1 0 0 1 0-2h1.24L3 5H2a1 1 0 0 1 0-2h3a1 1 0 0 1 1 .78L6.62 6h13.6m-1.09 2H7.38l1.07 5h11.18Z" />
                    </svg>
                    <span class="badge" aria-live="polite">{{ $cartCount }}</span>
                </a>
            </div>
            <div id="wishlist-config" hidden></div>
        </div>
    </div>
    <nav class="noon-cats" aria-label="Main categories">
        <ul class="cat-list">
            @foreach($rootCats as $cat)
            <li><a href="{{ route('products.category',$cat->slug) }}">{{ $cat->name }}</a></li>
            @endforeach
            <li class="more"><button type="button" aria-label="More">›</button></li>
        </ul>
    </nav>
</header>
<div id="currency-config" data-symbol='{{ e(json_encode($currency_symbol ?? "$", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}' data-default='{{ e(json_encode($defaultCurrency ?? null, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'></div>
<script src="{{ asset('front/js/header-inline.js') }}" defer></script>