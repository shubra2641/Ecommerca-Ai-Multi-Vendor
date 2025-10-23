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
        $currentCurrency = $this->resolveCurrentCurrency($currencies);

        $data = [
            'setting' => $setting,
            'siteName' => $setting->site_name ?? config('app.name'),
            'logoPath' => $setting->logo ?? null,
            'userName' => Auth::check() ? explode(' ', Auth::user()->name)[0] : null,
            'rootCats' => Cache::remember('root_categories', 3600, function () {
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
            }),
            'currencies' => $currencies,
            'currentCurrency' => $currentCurrency,
            'cartCount' => $this->getSessionCount('cart'),
            'compareCount' => $this->getSessionCount('compare'),
            'wishlistCount' => $this->getWishlistCount(),
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

    private function getSetting(View $view)
    {
        return $view->getData()['setting'] ??
            (Schema::hasTable('settings') ? \App\Models\Setting::first() : null);
    }

    private function resolveCurrentCurrency($currencies)
    {
        $current = $currencies->firstWhere('is_default', true) ?? $currencies->first();

        $sessionCurrencyId = session('currency_id');
        if (!$sessionCurrencyId) {
            return $current;
        }

        $sc = Currency::find($sessionCurrencyId);
        return (!$sc || !$currencies->contains('id', $sc->id)) ? $current : $sc;
    }

    private function getSessionCount(string $key): int
    {
        $session = session($key);

        return match (true) {
            is_array($session) => count($session),
            $session instanceof \Countable => count($session),
            default => 0,
        };
    }

    private function getWishlistCount(): int
    {
        if (!$this->isAuthenticatedUserWithWishlistTable()) {
            return $this->getSessionCount('wishlist');
        }

        try {
            return \App\Models\WishlistItem::where('user_id', Auth::id())->count();
        } catch (\Throwable $e) {
            return $this->getSessionCount('wishlist');
        }
    }

    private function isAuthenticatedUserWithWishlistTable(): bool
    {
        try {
            return Auth::check() && Schema::hasTable('wishlist_items');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
