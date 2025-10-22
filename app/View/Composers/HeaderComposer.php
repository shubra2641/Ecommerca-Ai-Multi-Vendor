<?php

namespace App\View\Composers;

use App\Models\Currency;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class HeaderComposer
{
    public function compose(View $view): void
    {
        $setting = $view->getData()['setting'] ?? (Schema::hasTable('settings') ? \App\Models\Setting::first() : null);
        // Basic site meta for header (avoid inline @php)
        $siteName = $setting->site_name ?? config('app.name');
        $logoPath = $setting->logo ?? null;
        $userName = auth()->check()
            ? explode(' ', auth()->user()->name)[0]
            : null;
        $rootCats = Cache::remember('root_categories', 3600, function () {
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
        $currencies = Cache::remember('active_currencies', 3600, function () {
            if (Schema::hasTable('currencies')) {
                try {
                    return Currency::active()->take(4)->get();
                } catch (Throwable $e) {
                    return collect();
                }
            }

            return collect();
        });
        // Respect a session selected currency if present, otherwise pick default/first
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
        }
        $cartSession = session('cart');
        $cartCount = 0;
        if (is_array($cartSession)) {
            $cartCount = count($cartSession);
        } elseif ($cartSession instanceof \Countable) {
            $cartCount = count($cartSession);
        }

        // Compare list (session key 'compare') simple count
        $compareSession = session('compare');
        $compareCount = 0;
        if (is_array($compareSession)) {
            $compareCount = count($compareSession);
        } elseif ($compareSession instanceof \Countable) {
            $compareCount = count($compareSession);
        }

        $wishlistCount = 0;
        try {
            if (auth()->check() && \Schema::hasTable('wishlist_items')) {
                $wishlistCount = \App\Models\WishlistItem::where('user_id', auth()->id())->count();
            } else {
                $wishlistSession = session('wishlist', []);
                $wishlistCount = is_array($wishlistSession)
                    ? count($wishlistSession)
                    : 0;
            }
        } catch (\Throwable $e) {
            $wishlistSession = session('wishlist', []);
            $wishlistCount = is_array($wishlistSession)
                ? count($wishlistSession)
                : 0;
        }

        // Active languages (cached)
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

        $view->with(compact(
            'setting',
            'rootCats',
            'currencies',
            'currentCurrency',
            'cartCount',
            'compareCount',
            'wishlistCount',
            'siteName',
            'logoPath',
            'userName',
            'activeLanguages'
        ));

        // Also share a currency symbol and current/default currency object for front-end scripts
        try {
            $symbol = $currentCurrency?->symbol ?? Currency::defaultSymbol();
            $view->with('currency_symbol', $symbol ?? '$');
            $view->with('defaultCurrency', Currency::getDefault());
            // expose the current currency object (may be session-selected)
            $view->with('currentCurrency', $currentCurrency);
        } catch (\Throwable $e) {
            $view->with('currency_symbol', '$');
        }
    }
}
