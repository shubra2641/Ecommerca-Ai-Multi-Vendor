<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFooterSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FooterSettingsController extends Controller
{
    public function edit(): View
    {
        $setting = Setting::first();
        $activeLanguages = $this->activeLanguages();
        $appLinks = $this->normalizeAppLinks(
            $setting->footer_app_links ?? [],
            $setting,
            app(UpdateFooterSettingsRequest::class)
        );
        // Sections visibility defaults
        $sections = array_merge([
            'support_bar' => true,
            'apps' => true,
            'social' => true,
            'pages' => true,
            'payments' => true,
        ], $setting->footer_sections_visibility ?? []);

        // Add empty pages array and footerPageTitles to prevent undefined key errors
        $pages = collect();
        $footerPageTitles = [];

        return view('admin.footer.settings', compact(
            'setting',
            'activeLanguages',
            'appLinks',
            'sections',
            'pages',
            'footerPageTitles'
        ));
    }

    public function update(
        UpdateFooterSettingsRequest $request
    ): RedirectResponse {
        $setting = Setting::first() ?? new Setting();
        $data = $request->validated();

        // Snapshot original values (only fields we may touch)
        $originalSnapshot = [
            'footer_app_links' => $setting->footer_app_links,
            'footer_support_heading' => $setting->footer_support_heading,
            'footer_support_subheading' => $setting->footer_support_subheading,
            'rights_i18n' => $setting->rights_i18n,
            'footer_labels' => $setting->footer_labels,
            'footer_sections_visibility' => $setting->footer_sections_visibility,
            'footer_payment_methods' => $setting->footer_payment_methods,
            'rights' => $setting->rights,
        ];

        // Normalize app links
        $appLinks = $this->normalizeAppLinks($data['app_links'] ?? [], $setting, $request);

        // Sections visibility (explicit each submit)
        $sections = collect(['support_bar', 'apps', 'social', 'pages', 'payments'])
            ->mapWithKeys(fn ($sec) => [
                $sec => (bool) ($data['sections'][$sec] ?? false),
            ])
            ->toArray();

        $payload = [];
        $payload['footer_app_links'] = $appLinks; // replace set
        $payload['footer_support_heading'] = $this->mergeLang(
            $setting->footer_support_heading,
            $data['footer_support_heading'] ?? []
        );
        $payload['footer_support_subheading'] = $this->mergeLang(
            $setting->footer_support_subheading,
            $data['footer_support_subheading'] ?? []
        );
        $payload['rights_i18n'] = $this->mergeLang($setting->rights_i18n, $data['rights_i18n'] ?? []);

        if (array_key_exists('footer_labels', $data)) {
            $existingLabels = $setting->footer_labels ?? [];
            foreach ($data['footer_labels'] as $labelKey => $langs) {
                $existingLabels[$labelKey] = $this->mergeLang($existingLabels[$labelKey] ?? [], $langs ?? []);
            }
            $payload['footer_labels'] = $existingLabels;
        }
        $payload['footer_sections_visibility'] = $sections;
        if (array_key_exists('footer_payment_methods', $data)) {
            $payload['footer_payment_methods'] = $data['footer_payment_methods'];
        }
        if (array_key_exists('footer_pages', $data)) {
            $payload['footer_pages'] = $data['footer_pages'] ?? [];
        }

        // Update rights plain field from default language if available
        $defaultLang = DB::table('languages')->where('is_default', 1)->value('code');
        if ($defaultLang && isset($payload['rights_i18n'][$defaultLang])) {
            $payload['rights'] = $payload['rights_i18n'][$defaultLang];
        }

        $setting->fill($payload);

        // Determine changed keys
        $changedKeys = $this->detectChangedKeys($originalSnapshot, $setting);

        if (empty($changedKeys)) {
            return back()->with('info', __('No changes to update.'));
        }

        $setting->save();
        Cache::forget('site_settings');

        return back()->with('success', __('Footer settings updated.'));
    }

    /**
     * Fetch active languages (cached) falling back to English only if table missing.
     */
    protected function activeLanguages()
    {
        return Cache::remember('active_languages_full', 3600, function () {
            try {
                return DB::table('languages')->where('is_active', 1)->orderBy('is_default', 'desc')->get();
            } catch (\Throwable $e) {
                return collect([(object) ['code' => 'en', 'is_default' => 1]]);
            }
        });
    }

    /**
     * Merge i18n arrays allowing deletion via empty string.
     */
    protected function mergeLang(?array $existing, ?array $incoming): array
    {
        $existing = $existing ?? [];
        foreach (($incoming ?? []) as $lang => $val) {
            if ($val === null) {
                continue;
            } // ignore nulls
            if ($val === '') {
                unset($existing[$lang]);

                continue;
            }
            $existing[$lang] = $val; // overwrite
        }

        return $existing;
    }

    /**
     * Normalize incoming app links structure & handle image uploads.
     */
    protected function normalizeAppLinks(array $incoming, Setting $setting, UpdateFooterSettingsRequest $request): array
    {
        $defaultAppLinks = [
            'apple' => ['enabled' => true, 'url' => null, 'image' => null, 'order' => 1],
            'google' => ['enabled' => true, 'url' => null, 'image' => null, 'order' => 2],
            'huawei' => ['enabled' => false, 'url' => null, 'image' => null, 'order' => 3],
        ];
        $result = array_replace_recursive($defaultAppLinks, $incoming);
        foreach ($result as $key => &$link) {
            if ($request->hasFile("app_links.{$key}.image")) {
                $file = $request->file("app_links.{$key}.image");
                $filename = 'app_badge_' . $key . '_' . time() . '_' .
                    uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/footer', $filename, 'public');
                $link['image'] = $path;
            } else {
                $link['image'] = $link['existing_image'] ??
                    ($setting->footer_app_links[$key]['image'] ?? null);
            }
            $link['enabled'] = (bool) ($link['enabled'] ?? false);
            $link['order'] = $link['order'] ?? 0;
            unset($link['existing_image']);
        }
        unset($link);

        return $result;
    }

    /**
     * Determine which shallow keys changed (arrays compared via JSON encoding).
     */
    protected function detectChangedKeys(array $original, Setting $setting): array
    {
        $changed = [];
        foreach ($original as $k => $before) {
            $after = $setting->{$k};
            if (is_array($before) || is_array($after)) {
                if (json_encode($before, JSON_UNESCAPED_UNICODE) !== json_encode($after, JSON_UNESCAPED_UNICODE)) {
                    $changed[] = $k;
                }
            } elseif ($before !== $after) {
                $changed[] = $k;
            }
        }

        return $changed;
    }
}
