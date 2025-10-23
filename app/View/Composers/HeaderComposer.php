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
        if ($sessionCurrencyId) {
            $sc = Currency::find($sessionCurrencyId);
            if ($sc && $currencies->contains('id', $sc->id)) {
                $current = $sc;
            }
        }

        return $current;
    }

    private function buildData($setting, $currencies, $currentCurrency)
    {
        $isAuthenticatedForWishlist = Auth::check() && Schema::hasTable('wishlist_items');

        return [
            'setting' => $setting,
            'siteName' => $setting->site_name ?? config('app.name'),
            'logoPath' => $setting->logo ?? null,
            'userName' => Auth::check() ? explode(' ', Auth::user()->name)[0] : null,
            'rootCats' => $this->getRootCategories(),
            'currencies' => $currencies,
            'currentCurrency' => $currentCurrency,
            'cartCount' => $this->getCartCount(),
            'compareCount' => $this->getCompareCount(),
            'wishlistCount' => $this->getWishlistCount($isAuthenticatedForWishlist),
            'activeLanguages' => $this->getActiveLanguages(),
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

    private function getCartCount(): int
    {
        $session = session('cart');
        return match (true) {
            is_array($session) => count($session),
            $session instanceof \Countable => count($session),
            default => 0,
        };
    }

    private function getCompareCount(): int
    {
        $session = session('compare');
        return match (true) {
            is_array($session) => count($session),
            $session instanceof \Countable => count($session),
            default => 0,
        };
    }

    private function getWishlistCount(bool $isAuthenticatedForWishlist): int
    {
        if (! $isAuthenticatedForWishlist) {
            $session = session('wishlist');
            return match (true) {
                is_array($session) => count($session),
                $session instanceof \Countable => count($session),
                default => 0,
            };
        }
        try {
            return \App\Models\WishlistItem::where('user_id', Auth::id())->count();
        } catch (\Throwable $e) {
            $session = session('wishlist');
            return match (true) {
                is_array($session) => count($session),
                $session instanceof \Countable => count($session),
                default => 0,
            };
        }
    }

    private function getActiveLanguages()
    {
        return Cache::remember('header_active_languages', 1800, function () {
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
    }
}
