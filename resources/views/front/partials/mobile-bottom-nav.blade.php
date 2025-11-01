{{-- Fixed bottom navigation for mobile --}}
<nav class="mobile-bottom-nav" aria-label="Mobile navigation">
    <a href="{{ route('home') }}" class="mbn-item" aria-label="{{ __('Home') }}">
        <i class="fas fa-home" aria-hidden="true"></i>
        <span>{{ __('Home') }}</span>
    </a>
    <a href="{{ route('products.index') }}" class="mbn-item" aria-label="{{ __('Categories') }}">
        <i class="fas fa-th-large" aria-hidden="true"></i>
        <span>{{ __('Categories') }}</span>
    </a>
    <a href="{{ route('wishlist.page') }}" class="mbn-item" aria-label="{{ __('Wishlist') }}">
        <i class="fas fa-heart" aria-hidden="true"></i>
        <span>{{ __('Wishlist') }}</span>
    </a>
    @auth
    <a href="{{ route('user.dashboard') }}" class="mbn-item" aria-label="{{ __('Account') }}">
        <i class="fas fa-user" aria-hidden="true"></i>
        <span>{{ __('Account') }}</span>
    </a>
    @else
    <a href="{{ route('login') }}" class="mbn-item" aria-label="{{ __('Account') }}">
        <i class="fas fa-user" aria-hidden="true"></i>
        <span>{{ __('Account') }}</span>
    </a>
    @endauth
    <form method="POST" action="{{ route('language.switch') }}" class="mbn-item mbn-lang" aria-label="{{ __('Change language') }}">
        @csrf
        <input type="hidden" name="language" value="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}">
        <button type="submit">
            <i class="fas fa-globe" aria-hidden="true"></i>
            <span>{{ __('Language') }}</span>
        </button>
    </form>
</nav>