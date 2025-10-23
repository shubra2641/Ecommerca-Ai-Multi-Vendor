<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

final class HeaderComposer
{
    public function compose(View $view): void
    {
        $setting = $view->getData()['setting'] ??
            (Schema::hasTable('settings') ? \App\Models\Setting::first() : null);

        $currencies = $this->getCurrencies();
        $currentCurrency = $this->resolveCurrentCurrency($currencies);

        $data = $this->buildData($setting, $currencies, $currentCurrency);

        $view->with($data);

        try {
            $symbol = $currentCurrency?->symbol ?? Currency::defaultSymbol();
            $view->with('currency_symbol', $symbol ?? '$');
            $view->with('defaultCurrency', Currency::getDefault());
            $view->with('currentCurrency', $currentCurrency);
        } catch (\Throwable $e) {
            $view->with('currency_symbol', '$');
        }
    }
    private function getCurrencies()
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
    private function resolveCurrentCurrency($currencies)
    {
        $current = $currencies->firstWhere('is_default', true) ?? $currencies->first();

        $sessionCurrencyId = session('currency_id');
        if (! $sessionCurrencyId) {
            return $current;
        }

        $sc = Currency::find($sessionCurrencyId);
        if (! $sc || ! $currencies->contains('id', $sc->id)) {
            return $current;
        }

        return $sc;
    }

    private function buildData($setting, $currencies, $currentCurrency)
    {
        $isAuthenticatedForWishlist = Auth::check() && Schema::hasTable('wishlist_items');

        $rootCats = Cache::remember('header_root_categories', 1800, function () {
            if (Schema::hasTable('product_categories')) {
                try {
                    return \App\Models\ProductCategory::where('parent_id', null)->active()->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            }
            return collect();
        });

        $activeLanguages = Cache::remember('header_active_languages', 1800, function () {
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
        });

        $cartCount = $this->getCartCount();
        $compareCount = $this->getCompareCount();
        $wishlistCount = $this->getWishlistCount($isAuthenticatedForWishlist);

        return [
            'setting' => $setting,
            'siteName' => $setting->site_name ?? config('app.name'),
            'logoPath' => $setting->logo ?? null,
            'userName' => Auth::check() ? explode(' ', Auth::user()->name)[0] : null,
            'rootCats' => $rootCats,
            'currencies' => $currencies,
            'currentCurrency' => $currentCurrency,
            'cartCount' => $cartCount,
            'compareCount' => $compareCount,
            'wishlistCount' => $wishlistCount,
            'activeLanguages' => $activeLanguages,
        ];
    }

    private function getCartCount()
    {
        $cartSession = session('cart');
        return match (true) {
            is_array($cartSession) => count($cartSession),
            $cartSession instanceof \Countable => count($cartSession),
            default => 0,
        };
    }

    private function getCompareCount()
    {
        $compareSession = session('compare');
        return match (true) {
            is_array($compareSession) => count($compareSession),
            $compareSession instanceof \Countable => count($compareSession),
            default => 0,
        };
    }

    private function getWishlistCount($isAuthenticatedForWishlist)
    {
        if (! $isAuthenticatedForWishlist) {
            $wishlistSession = session('wishlist');
            return match (true) {
                is_array($wishlistSession) => count($wishlistSession),
                $wishlistSession instanceof \Countable => count($wishlistSession),
                default => 0,
            };
        } else {
            try {
                return \App\Models\WishlistItem::where('user_id', Auth::id())->count();
            } catch (\Throwable $e) {
                $wishlistSession = session('wishlist');
                return match (true) {
                    is_array($wishlistSession) => count($wishlistSession),
                    $wishlistSession instanceof \Countable => count($wishlistSession),
                    default => 0,
                };
            }
        }
    }
}
