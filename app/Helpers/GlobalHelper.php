<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Currency;
use Throwable;

class GlobalHelper
{
    /**
     * Format a numeric price according to current/default currency symbol.
     */
    public static function formatPrice($amount, $decimals = 2)
    {
        try {
            $symbol = session('currency_symbol') ?? (config('app.currency_symbol') ?? null);
            if (! $symbol) {
                // Try to get default from Currency model if available
                if (class_exists(Currency::class)) {
                    $symbol = Currency::defaultSymbol() ?? '$';
                } else {
                    $symbol = '$';
                }
            }
        } catch (Throwable $e) {
            $symbol = '$';
        }

        // Use number_format for a simple, safe formatting
        $formatted = number_format((float) $amount, $decimals, '.', ',');

        return $symbol . ' ' . $formatted;
    }

    /**
     * Backwards-compatible alias: currency_format -> format_price
     */
    public static function currencyFormat($amount, $decimals = 2)
    {
        return self::formatPrice($amount, $decimals);
    }

    /**
     * Resolve an image path or URL stored in DB to a safe absolute URL for use in <img> or <a>.
     * - If $path is already an absolute URL (http/https) return it as-is.
     * - If it already contains 'storage/' prefix, pass it to asset() directly.
     * - Otherwise assume it's a storage path and prefix with 'storage/'.
     */
    public static function storageImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        $path = trim($path);
        // If already absolute URL keep as-is
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        try {
            // If the path already starts with storage/, strip it for disk lookup
            $candidate = str_starts_with($path, 'storage/') ? substr($path, strlen('storage/')) : ltrim($path, '/');
            // If file exists in the public disk, use Storage API to build the URL
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($candidate);
            }
        } catch (\Throwable $e) {
            // Fall back - intentionally empty
            null;
        }

        // Fallback behavior - preserve previous semantics
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Return file modification timestamp for a public asset (relative path) used for cache-busting.
     * Silently returns current time if file not found to avoid breaking views.
     */
    public static function assetModifiedTime(string $publicRelativePath): int
    {
        try {
            $full = public_path($publicRelativePath);
            if (is_file($full)) {
                return (int) filemtime($full);
            }
        } catch (Throwable $e) {
            // Ignore - intentionally empty
            null;
        }

        return time();
    }

    /**
     * Get the current currency symbol from session or default.
     */
    public static function getCurrentCurrencySymbol(): string
    {
        try {
            $currentCurrency = session('currency_id') ? Currency::find(session('currency_id')) : Currency::getDefault();
            return $currentCurrency?->symbol ?? Currency::defaultSymbol();
        } catch (Throwable $e) {
            return '$';
        }
    }

    /**
     * Convert amount between currencies.
     */
    public static function convertCurrency($amount, $fromCurrency = null, $toCurrency = null, $decimals = 2)
    {
        try {
            $fromCurrency = $fromCurrency ?: Currency::getDefault();
            $toCurrency = $toCurrency ?: (session('currency_id') ? Currency::find(session('currency_id')) : Currency::getDefault());

            if ($fromCurrency && $toCurrency && $fromCurrency->id !== $toCurrency->id) {
                return $fromCurrency->convertTo($amount, $toCurrency, $decimals);
            }

            return round($amount, $decimals);
        } catch (Throwable $e) {
            return round($amount, $decimals);
        }
    }
}
