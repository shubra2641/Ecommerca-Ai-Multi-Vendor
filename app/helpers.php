<?php

declare(strict_types=1);

use App\Models\Currency;

if (! function_exists('format_price')) {
    /**
     * Format a numeric price according to current/default currency symbol.
     */
    function format_price($amount, $decimals = 2)
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
}

if (! function_exists('currency_format')) {
    /**
     * Backwards-compatible alias: currency_format -> format_price
     */
    function currency_format($amount, $decimals = 2)
    {
        return format_price($amount, $decimals);
    }
}

if (! function_exists('storage_image_url')) {
    /**
     * Resolve an image path or URL stored in DB to a safe absolute URL for use in <img> or <a>.
     * - If $path is already an absolute URL (http/https) return it as-is.
     * - If it already contains 'storage/' prefix, pass it to asset() directly.
     * - Otherwise assume it's a storage path and prefix with 'storage/'.
     */
    function storage_image_url(?string $path): ?string
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
        }

        // Fallback behavior - preserve previous semantics
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}

if (! function_exists('asset_modified_time')) {
    /**
     * Return file modification timestamp for a public asset (relative path) used for cache-busting.
     * Silently returns current time if file not found to avoid breaking views.
     */
    function asset_modified_time(string $publicRelativePath): int
    {
        try {
            $full = public_path($publicRelativePath);
            if (is_file($full)) {
                return (int) filemtime($full);
            }
        } catch (Throwable $e) {
            // Ignore - intentionally empty
        }

        return time();
    }
}
