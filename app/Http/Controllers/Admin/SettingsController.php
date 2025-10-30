<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * List of allowed Google Fonts for security validation (including Arabic support).
     */
    private const ALLOWED_FONTS = [
        // Latin Fonts
        'Inter',
        'Roboto',
        'Open Sans',
        'Lato',
        'Montserrat',
        'Source Sans Pro',
        'Oswald',
        'Raleway',
        'PT Sans',
        'Lora',
        'Nunito',
        'Poppins',
        'Playfair Display',
        'Merriweather',
        'Ubuntu',
        'Crimson Text',
        'Work Sans',
        'Fira Sans',
        'Noto Sans',
        'Dancing Script',
        // Additional Latin Fonts
        'Roboto Slab',
        'Source Serif Pro',
        'Libre Baskerville',
        'Quicksand',
        'Rubik',
        'Barlow',
        'DM Sans',
        'Manrope',
        'Space Grotesk',
        'Plus Jakarta Sans',
        // Arabic Fonts
        'Noto Sans Arabic',
        'Cairo',
        'Tajawal',
        'Almarai',
        'Amiri',
        'Scheherazade New',
        'Markazi Text',
        'Reem Kufi',
        'IBM Plex Sans Arabic',
        'Changa',
        'El Messiri',
        'Harmattan',
        'Lateef',
        'Aref Ruqaa',
        'Katibeh',
        'Lalezar',
        'Mirza',
    ];

    /**
     * Display the settings form.
     */
    public function index(Request $request): View
    {
        // Handle refresh parameter
        if ($request->has('refresh') && $request->get('refresh') === '1') {
            // Clear any cached settings data
            cache()->forget('settings.font_family');
            cache()->forget('settings.maintenance_enabled');
            cache()->forget('settings.maintenance_reopen_at');
            cache()->forget('maintenance_settings');
            // Clear view cache
            Artisan::call('view:clear');
        }

        $setting = Setting::first();
        if (! $setting) {
            // provide an empty Setting instance to avoid null property access in views
            $setting = new Setting();
        }
        // Auto-heal legacy double-encoded withdrawal_gateways like ["[\"Bank Transfer\",\"PayPal\"]"]
        if (! empty($setting->withdrawal_gateways)) {
            $normalized = $this->normalizeWithdrawalGateways($setting->withdrawal_gateways);
            if ($normalized !== $setting->withdrawal_gateways) {
                $setting->withdrawal_gateways = $normalized;
                // Silent save to clean once; ignore failures
                try {
                    $setting->save();
                } catch (\Throwable $e) {
                    // ignore save failures
                    null;
                }
            }
        }

        return view('admin.profile.settings', compact('setting'));
    }

    /**
     * Update the settings.
     *
     * @throws ValidationException
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Normalize maintenance message array to JSON string for storage
        if (isset($data['maintenance_message']) && is_array($data['maintenance_message'])) {
            $data['maintenance_message'] = json_encode($data['maintenance_message'], JSON_UNESCAPED_UNICODE);
        }

        // Normalize withdrawal gateways input (handles newline, comma, JSON, double-JSON)
        if (array_key_exists('withdrawal_gateways', $data)) {
            $data['withdrawal_gateways'] = $this->normalizeWithdrawalGateways($data['withdrawal_gateways']);
        }

        $setting = Setting::first();
        if (! $setting) {
            $setting = new Setting();
        }

        // Handle logo upload with enhanced security
        if ($request->hasFile('logo')) {
            $logoPath = $this->handleLogoUpload($request->file('logo'), $setting->logo);
            if ($logoPath) {
                $data['logo'] = $logoPath;
            }
        }

        // Only update fields that have actually changed
        $changedData = [];
        foreach ($data as $key => $value) {
            if ($setting->$key !== $value) {
                $changedData[$key] = $value;
            }
        }

        // Fill only changed attributes
        $setting->fill($changedData);
        // AI settings handling - only update if changed
        if ($request->has('ai_enabled')) {
            $newAiEnabled = (bool) $request->input('ai_enabled');
            if ($setting->ai_enabled !== $newAiEnabled) {
                $setting->ai_enabled = $newAiEnabled;
            }
        }
        if ($request->filled('ai_provider')) {
            $newProvider = $request->input('ai_provider');
            if ($setting->ai_provider !== $newProvider) {
                $setting->ai_provider = $newProvider;
            }
        } elseif ($request->has('ai_provider') && $request->input('ai_provider') === '') {
            if ($setting->ai_provider !== null) {
                $setting->ai_provider = null;
            }
        }
        // API key: only update if new key provided (not masked placeholder)
        if ($request->has('ai_openai_api_key')) {
            $rawKey = trim((string) $request->input('ai_openai_api_key'));
            if ($rawKey !== '' && ! str_starts_with($rawKey, '••••') && $setting->ai_openai_api_key !== $rawKey) {
                // Store as plain text (not encrypted) for easier management
                $setting->ai_openai_api_key = $rawKey;
            }
        }
        $setting->save();

        // Persist font family to a simple cache key for fast layout access
        if (isset($data['font_family'])) {
            cache()->put('settings.font_family', $data['font_family'], 86400);
        }

        // Persist maintenance state to cache for fast checks
        if (array_key_exists('maintenance_enabled', $data)) {
            cache()->put('settings.maintenance_enabled', (bool) $data['maintenance_enabled'], 3600);
            Cache::forget('maintenance_settings'); // invalidate cached Setting used by CheckMaintenanceMode
        }
        if (isset($data['maintenance_reopen_at'])) {
            cache()->put('settings.maintenance_reopen_at', $data['maintenance_reopen_at'], 3600);
            Cache::forget('maintenance_settings');
        }

        // Dispatch event for runtime adjustments (optional listener can recompile CSS, etc.)
        if (function_exists('event')) {
            event('settings.updated', ['font_family' => $data['font_family'] ?? null]);
        }

        return back()->with('success', __('Settings updated successfully.'));
    }

    /**
     * Normalize withdrawal gateways from mixed legacy formats to a clean string[] list.
     * Accepts: newline separated string, comma separated, JSON array string, double-encoded JSON, or already array.
     */
    private function normalizeWithdrawalGateways($value): array
    {
        // If already an array, check for single JSON string element (double-encoded case)
        if (is_array($value)) {
            if (count($value) === 1 && is_string($value[0]) && $this->looksLikeJsonArray($value[0])) {
                $decoded = json_decode($value[0], true);
                if (is_array($decoded)) {
                    return $this->normalizeWithdrawalGateways($decoded); // recurse
                }
            }
            // Flatten any nested arrays, trim strings
            $flat = [];
            array_walk_recursive($value, function ($item) use (&$flat): void {
                if (is_string($item)) {
                    $item = trim($item);
                    if ($item !== '') {
                        $flat[] = $item;
                    }
                }
            });

            // De-duplicate preserving order
            return array_values(array_unique($flat));
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return [];
            }
            if ($this->looksLikeJsonArray($value)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    return $this->normalizeWithdrawalGateways($decoded);
                }
            }
            // Split on newlines or commas
            $parts = preg_split('/[\r\n,]+/', $value);
            $parts = array_filter(array_map('trim', $parts));

            return array_values(array_unique($parts));
        }

        return [];
    }

    private function looksLikeJsonArray(string $value): bool
    {
        return str_starts_with($value, '[') && str_ends_with($value, ']');
    }
}
