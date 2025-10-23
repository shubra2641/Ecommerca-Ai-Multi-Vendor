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
        $setting = $this->getSetting($view);
        $currencies = $this->getCurrencies();
        $currentCurrency = $this->resolveCurrentCurrency($currencies);

        $data = [
            'setting' => $setting,
            'siteName' => $setting->site_name ?? config('app.name'),
            'logoPath' => $setting->logo ?? null,
            'userName' => Auth::check() ? explode(' ', Auth::user()->name)[0] : null,
            'rootCats' => $this->getRootCategories(),
            'currencies' => $currencies,
            'currentCurrency' => $currentCurrency,
            ...$this->getCounts(),
            'activeLanguages' => $this->getActiveLanguages(),
        ];

        $view->with($data);
        $view->with($this->getCurrencyData($currentCurrency));
    }

    private function getSetting(View $view)
    {
        return $view->getData()['setting'] ??
            (Schema::hasTable('settings') ? \App\Models\Setting::first() : null);
    }

    private function getCurrencyData($currentCurrency): array
    {
        try {
            $symbol = $currentCurrency?->symbol ?? Currency::defaultSymbol();
            return [
                'currency_symbol' => $symbol ?? '$',
                'defaultCurrency' => Currency::getDefault(),
                'currentCurrency' => $currentCurrency,
            ];
        } catch (\Throwable $e) {
            return ['currency_symbol' => '$'];
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

    private function getRootCategories()
    {
        return Cache::remember('header_root_categories', 1800, function () {
            if (Schema::hasTable('product_categories')) {
                try {
                    return \App\Models\ProductCategory::where('parent_id', null)->active()->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            }
            return collect();
        });
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

    private function getCounts(): array
    {
        return [
            'cartCount' => $this->getCartCount(),
            'compareCount' => $this->getCompareCount(),
            'wishlistCount' => $this->getWishlistCount(),
        ];
    }

    private function getCartCount(): int
    {
        $cart = session('cart');

        return match (true) {
            is_array($cart) => count($cart),
            $cart instanceof \Countable => count($cart),
            default => 0,
        };
    }

    private function getCompareCount(): int
    {
        $compare = session('compare');

        return match (true) {
            is_array($compare) => count($compare),
            $compare instanceof \Countable => count($compare),
            default => 0,
        };
    }

    private function getWishlistCount(): int
    {
        if (Auth::check() && Schema::hasTable('wishlist_items')) {
            return \App\Models\WishlistItem::where('user_id', Auth::id())->count();
        }

        $wishlist = session('wishlist');

        return match (true) {
            is_array($wishlist) => count($wishlist),
            $wishlist instanceof \Countable => count($wishlist),
            default => 0,
        };
    }
}
