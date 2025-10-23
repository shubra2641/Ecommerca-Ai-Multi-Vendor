<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class FooterComposer
{
    public function compose(View $view): void
    {
        $setting = $this->getSetting($view);
        $locale = app()->getLocale();

        $data = [
            'setting' => $setting,
            'socialLinks' => $this->getSocialLinks(),
            ...$this->getLocalizedContent($setting, $locale),
            ...$this->getFooterConfiguration($setting),
        ];

        $view->with($data);
    }

    private function getSetting(View $view)
    {
        return $view->getData()['setting'] ??
            (Schema::hasTable('settings') ? \App\Models\Setting::first() : null);
    }

    private function getSocialLinks()
    {
        return Cache::remember('footer_social_links', 1800, function () {
            if (Schema::hasTable('social_links')) {
                try {
                    return \App\Models\SocialLink::active()->orderBy('order')->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            }

            return collect();
        });
    }

    private function getLocalizedContent($setting, string $locale): array
    {
        $t = fn ($arr, $fallback) => $arr[$locale] ?? ($arr['en'] ?? __($fallback));

        return [
            'supportHeading' => $t($setting->footer_support_heading ?? [], "We're Always Here To Help"),
            'supportSub' => $t($setting->footer_support_subheading ?? [], 'Reach out to us through any of these support channels'),
            'rightsLine' => $setting->rights_i18n[$locale] ?? ($setting->rights_i18n['en'] ?? ($setting->rights ?? __('All Rights Reserved'))),
        ];
    }

    private function getFooterConfiguration($setting): array
    {
        $locale = app()->getLocale();

        return [
            'sections' => array_merge([
                'support_bar' => true,
                'apps' => true,
                'social' => true,
                'payments' => true,
            ], $setting->footer_sections_visibility ?? []),
            'paymentList' => $setting->footer_payment_methods ?? ['VISA', 'MC', 'CASH'],
            'orderedApps' => $this->getOrderedApps($setting),
            ...$this->getFooterLabels($setting, $locale),
        ];
    }

    private function getOrderedApps($setting): \Illuminate\Support\Collection
    {
        $appLinks = $setting->footer_app_links ?? [];

        return collect($appLinks)
            ->filter(fn ($a) => ($a['enabled'] ?? false) && ($a['url'] ?? null))
            ->sortBy('order');
    }

    private function getFooterLabels($setting, string $locale): array
    {
        $labels = $setting->footer_labels ?? [];

        return [
            'helpCenterLabel' => $labels['help_center'][$locale] ?? ($labels['help_center']['en'] ?? __('Help Center')),
            'emailSupportLabel' => $labels['email_support'][$locale] ?? ($labels['email_support']['en'] ?? __('Email Support')),
            'phoneSupportLabel' => $labels['phone_support'][$locale] ?? ($labels['phone_support']['en'] ?? __('Phone Support')),
            'appsHeading' => $labels['apps_heading'][$locale] ?? ($labels['apps_heading']['en'] ?? __('Shop on the go')),
            'socialHeading' => $labels['social_heading'][$locale] ?? ($labels['social_heading']['en'] ?? __('Connect with us')),
        ];
    }
}
