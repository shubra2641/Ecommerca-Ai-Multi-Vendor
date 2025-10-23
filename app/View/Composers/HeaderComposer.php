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
        $currentCurrency = $current;
        $sessionCurrencyId = session('currency_id');
        if ($sessionCurrencyId) {
            $sc = Currency::find($sessionCurrencyId);
            if ($sc && $currencies->contains('id', $sc->id)) {
                $currentCurrency = $sc;
            }
        }
        return $currentCurrency;
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

    private function getCounts()
    {
        $isAuthenticatedForWishlist = Auth::check() && Schema::hasTable('wishlist_items');

        $cartCount = match (true) {
            is_array(session('cart')) => count(session('cart')),
            session('cart') instanceof \Countable => count(session('cart')),
            default => 0,
        };
        $compareCount = match (true) {
            is_array(session('compare')) => count(session('compare')),
            session('compare') instanceof \Countable => count(session('compare')),
            default => 0,
        };
        $wishlistCount = $isAuthenticatedForWishlist ? \App\Models\WishlistItem::where('user_id', Auth::id())->count() : match (true) {
            is_array(session('wishlist')) => count(session('wishlist')),
            session('wishlist') instanceof \Countable => count(session('wishlist')),
            default => 0,
        };

        return [
            'cartCount' => $cartCount,
            'compareCount' => $compareCount,
            'wishlistCount' => $wishlistCount,
        ];
    }
}
