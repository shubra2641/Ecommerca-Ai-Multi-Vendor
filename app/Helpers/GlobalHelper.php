<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Currency;

class GlobalHelper
{
    /**
     * Format a numeric price with currency symbol.
     */
    public static function formatPrice($amount, $decimals = 2): string
    {
        $symbol = self::getCurrentCurrencySymbol();
        return $symbol . ' ' . number_format((float) $amount, $decimals, '.', ',');
    }

    /**
     * Get current currency symbol from session or default.
     */
    public static function getCurrentCurrencySymbol(): string
    {
        return session('currency_symbol') ??
            config('app.currency_symbol') ??
            Currency::defaultSymbol() ??
            '$';
    }

    /**
     * Convert amount between currencies.
     */
    public static function convertCurrency($amount, $fromCurrency = null, $toCurrency = null, $decimals = 2)
    {
        $fromCurrency ??= Currency::getDefault();
        $toCurrency ??= session('currency_id') ? Currency::find(session('currency_id')) : Currency::getDefault();

        if ($fromCurrency && $toCurrency && $fromCurrency->id !== $toCurrency->id) {
            return $fromCurrency->convertTo($amount, $toCurrency, $decimals);
        }

        return round($amount, $decimals);
    }

    /**
     * Get safe image URL for storage paths.
     */
    public static function storageImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Return absolute URLs as-is
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Use Laravel's asset helper for storage paths
        return asset(str_starts_with($path, 'storage/') ? $path : 'storage/' . ltrim($path, '/'));
    }

    /**
     * Get file modification time for cache busting.
     */
    public static function assetModifiedTime(string $path): int
    {
        $fullPath = public_path($path);
        return is_file($fullPath) ? (int) filemtime($fullPath) : time();
    }

    /**
     * Backwards compatibility alias.
     */
    public static function currencyFormat($amount, $decimals = 2): string
    {
        return self::formatPrice($amount, $decimals);
    }
}
