<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MaintenanceSettingsController extends Controller
{
    public function edit(): View
    {
        $setting = Setting::first();
        $rawMessages = $setting?->maintenance_message ?? [];
        if (is_string($rawMessages)) {
            $decoded = json_decode($rawMessages, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $rawMessages = $decoded;
            }
        }
        $activeLanguages = Cache::remember('active_languages_full', 3600, function () {
            try {
                $rows = DB::table('languages')->where('is_active', 1)->orderBy('is_default', 'desc')->get(['code', 'name', 'is_default']);
                if ($rows->count()) {
                    return $rows;
                }
            } catch (\Throwable $e) {
            }
            $configured = config('app.locales') ?? [config('app.locale', 'en')];

            return collect(array_map(function ($code) {
                return (object) ['code' => $code, 'name' => strtoupper($code), 'is_default' => $code === config('app.locale')];
            }, $configured));
        });

        return view('admin.maintenance.settings', [
            'setting' => $setting,
            'messages' => $rawMessages,
            'activeLanguages' => $activeLanguages,
        ]);
    }

    public function update(Request $request, \App\Services\HtmlSanitizer $sanitizer): RedirectResponse
    {
        $data = $request->validate([
            'maintenance_enabled' => ['nullable', 'boolean'],
            'maintenance_reopen_at' => ['nullable', 'date'],
            'maintenance_message' => ['nullable', 'array'],
            'maintenance_message.*' => ['nullable', 'string', 'max:255'],
        ]);

        $setting = Setting::first() ?? new Setting();

        $setting->maintenance_enabled = (bool) ($data['maintenance_enabled'] ?? false);
        $setting->maintenance_reopen_at = $data['maintenance_reopen_at'] ?? null;
        if (isset($data['maintenance_message']) && is_array($data['maintenance_message'])) {
            // sanitize each message value
            foreach ($data['maintenance_message'] as $lc => $v) {
                $data['maintenance_message'][$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
            // Keep same storage format as original SettingsController
            $setting->maintenance_message = $data['maintenance_message'];
        }

        $setting->save();
        Cache::forget('site_settings');
        Cache::forget('maintenance_settings'); // ensure middleware re-fetches fresh values immediately
        cache()->put('settings.maintenance_enabled', $setting->maintenance_enabled, 3600);
        if ($setting->maintenance_reopen_at) {
            cache()->put('settings.maintenance_reopen_at', $setting->maintenance_reopen_at, 3600);
        }

        return back()->with('success', __('Maintenance settings updated.'));
    }

    public function preview(): View
    {
        $setting = Setting::first();
        $messages = $setting?->maintenance_message ?? [];
        if (is_string($messages)) {
            $decoded = json_decode($messages, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $messages = $decoded;
            }
        }
        $defaultLang = config('app.locale', 'en');
        $currentMessage = $messages[$defaultLang] ?? (is_array($messages) ? reset($messages) : null);
        $reopenAt = $setting?->maintenance_reopen_at;

        return view('front.maintenance', [
            'message' => $currentMessage,
            'allMessages' => $messages,
            'reopenAt' => $reopenAt,
            'isPreview' => true,
        ]);
    }
}
