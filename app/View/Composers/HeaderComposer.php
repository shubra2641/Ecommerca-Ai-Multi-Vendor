<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Currency;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

final class HeaderComposer
{
    public function compose(View $view): void
    {
        $setting = $this->getSetting($view);

        $data = [
            'setting' => $setting,
            ...$this->getBasicSiteData($setting),
            ...$this->getCategoriesAndCurrencies(),
            ...$this->getCartAndWishlistData(),
            ...$this->getLanguages(),
        ];

        $view->with($data);
        $this->addCurrencyData($view);
    }

    private function getSetting(View $view)
    {
        return $view->getData()['setting'] ??
            (Schema::hasTable('settings') ? \App\Models\Setting::first() : null);
    }

    private function getBasicSiteData($setting): array
    {
        return [
            'siteName' => $setting->site_name ?? config('app.name'),
            'logoPath' => $setting->logo ?? null,
            'userName' => Auth::check()
                ? explode(' ', Auth::user()->name)[0]
                : null,
        ];
    }

    private function getCategoriesAndCurrencies(): array
    {
        return [
            'rootCats' => $this->getRootCategories(),
            'currencies' => $this->getActiveCurrencies(),
            'currentCurrency' => $this->getCurrentCurrency(),
        ];
    }

    private function getRootCategories()
    {
        return Cache::remember('root_categories', 3600, function () {
            if (Schema::hasTable('product_categories')) {
                try {
                    return ProductCategory::where('active', 1)
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->take(14)
                        ->get();
                } catch (Throwable $e) {
                    return collect();
                }
            }

            return collect();
        });
    }

    private function getActiveCurrencies()
    {
        return Cache::remember('active_currencies', 3600, function () {
            if (Schema::hasTable('currencies')) {
                try {
                    return Currency::active()->take(4)->get();
                } catch (Throwable $e) {
                    return collect();
                }
            }

            return collect();
        });
    }

    private function getCurrentCurrency()
    {
        $currencies = $this->getActiveCurrencies();
        $currentCurrency = $currencies->firstWhere('is_default', true) ?? $currencies->first();

        try {
            $sessionCurrencyId = session('currency_id');
            if ($sessionCurrencyId) {
                $sc = Currency::find($sessionCurrencyId);
                if ($sc && $currencies->contains('id', $sc->id)) {
                    $currentCurrency = $sc;
                }
            }
        } catch (\Throwable $e) {
            // ignore session/currency read issues
            null;
        }

        return $currentCurrency;
    }

    private function getCartAndWishlistData(): array
    {
        return [
            'cartCount' => $this->getCartCount(),
            'compareCount' => $this->getCompareCount(),
            'wishlistCount' => $this->getWishlistCount(),
        ];
    }

    private function getCartCount(): int
    {
        $cartSession = session('cart');

        if (is_array($cartSession)) {
            return count($cartSession);
        }

        if ($cartSession instanceof \Countable) {
            return count($cartSession);
        }

        return 0;
    }

    private function getCompareCount(): int
    {
        $compareSession = session('compare');

        if (is_array($compareSession)) {
            return count($compareSession);
        }

        if ($compareSession instanceof \Countable) {
            return count($compareSession);
        }

        return 0;
    }

    private function getWishlistCount(): int
    {
        // Try to get authenticated user's wishlist count
        if ($this->isAuthenticatedUserWithWishlistTable()) {
            return $this->getAuthenticatedUserWishlistCount();
        }

        // Fallback to session-based wishlist
        return $this->getSessionWishlistCount();
    }

    private function isAuthenticatedUserWithWishlistTable(): bool
    {
        try {
            return Auth::check() && Schema::hasTable('wishlist_items');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function getAuthenticatedUserWishlistCount(): int
    {
        try {
            return \App\Models\WishlistItem::where('user_id', Auth::id())->count();
        } catch (\Throwable $e) {
            return $this->getSessionWishlistCount();
        }
    }

    private function getSessionWishlistCount(): int
    {
        $wishlistSession = session('wishlist', []);

        return is_array($wishlistSession) ? count($wishlistSession) : 0;
    }

    private function getLanguages()
    {
        return [
            'activeLanguages' => Cache::remember('header_active_languages', 1800, function () {
                if (Schema::hasTable('languages')) {
                    try {
                        return \App\Models\Language::where('is_active', 1)
                            ->orderByDesc('is_default')
                            ->orderBy('name')
                            ->get();
                    } catch (\Throwable $e) {
                        return collect();
                    }
                }

                return collect();
            }),
        ];
    }

    private function addCurrencyData(View $view): void
    {
        try {
            $currentCurrency = $this->getCurrentCurrency();
            $symbol = $currentCurrency?->symbol ?? Currency::defaultSymbol();
            $view->with('currency_symbol', $symbol ?? '$');
            $view->with('defaultCurrency', Currency::getDefault());
            $view->with('currentCurrency', $currentCurrency);
        } catch (\Throwable $e) {
            $view->with('currency_symbol', '$');
        }
    }
}
