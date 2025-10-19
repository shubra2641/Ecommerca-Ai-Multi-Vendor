<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class FooterComposer
{
    public function compose(View $view): void
    {
        $setting = $view->getData()['setting'] ?? (Schema::hasTable('settings') ? \App\Models\Setting::first() : null);
        $locale = app()->getLocale();
        $socialLinks = Cache::remember('footer_social_links', 1800, function () {
            if (Schema::hasTable('social_links')) {
                try {
                    return \App\Models\SocialLink::active()->orderBy('order')->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            }

            return collect();
        });
        // Localized headings & labels with graceful fallbacks
        $t = function ($arr, $fallback) use ($locale) {
            return $arr[$locale] ?? ($arr['en'] ?? __($fallback));
        };

        $supportHeading = $t($setting->footer_support_heading ?? [], "We're Always Here To Help");
        $supportSub = $t(
            $setting->footer_support_subheading ?? [],
            'Reach out to us through any of these support channels'
        );
        $rightsLine = $setting->rights_i18n[$locale] ??
            ($setting->rights_i18n['en'] ?? ($setting->rights ?? __('All Rights Reserved')));
        $sections = array_merge([
            'support_bar' => true,
            'apps' => true,
            'social' => true,
            'payments' => true,
        ], $setting->footer_sections_visibility ?? []);
        $paymentList = $setting->footer_payment_methods ?? [];
        if (! count($paymentList)) {
            $paymentList = ['VISA', 'MC', 'CASH'];
        }
        $appLinks = $setting->footer_app_links ?? [];
        $orderedApps = collect($appLinks)
            ->filter(fn($a) => ($a['enabled'] ?? false) && ($a['url'] ?? null))
            ->sortBy('order');
        $labels = $setting->footer_labels ?? [];
        $helpCenterLabel = $labels['help_center'][$locale] ??
            ($labels['help_center']['en'] ?? __('Help Center'));
        $emailSupportLabel = $labels['email_support'][$locale] ??
            ($labels['email_support']['en'] ?? __('Email Support'));
        $phoneSupportLabel = $labels['phone_support'][$locale] ??
            ($labels['phone_support']['en'] ?? __('Phone Support'));
        $appsHeading = $labels['apps_heading'][$locale] ??
            ($labels['apps_heading']['en'] ?? __('Shop on the go'));
        $socialHeading = $labels['social_heading'][$locale] ??
            ($labels['social_heading']['en'] ?? __('Connect with us'));

        $view->with(compact(
            'setting',
            'socialLinks',
            'supportHeading',
            'supportSub',
            'rightsLine',
            'sections',
            'paymentList',
            'orderedApps',
            'helpCenterLabel',
            'emailSupportLabel',
            'phoneSupportLabel',
            'appsHeading',
            'socialHeading'
        ));
    }
}
