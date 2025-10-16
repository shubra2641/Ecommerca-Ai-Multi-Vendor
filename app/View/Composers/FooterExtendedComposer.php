<?php

namespace App\View\Composers;

use App\Models\Setting;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FooterExtendedComposer
{
    public function compose(View $view): void
    {
        $cacheKey = 'footer_extended_data_' . app()->getLocale();
        $data = Cache::remember($cacheKey, 900, function () {
            $locale = app()->getLocale();
            $setting = Setting::first();
            $socialLinks = collect();
            try {
                $socialLinks = SocialLink::active()->orderBy('order')->get();
            } catch (\Throwable $e) {
                $socialLinks = collect();
            }
            $supportHeading = $setting->footer_support_heading[$locale] ?? ($setting->footer_support_heading['en'] ?? __("We're Always Here To Help"));
            $supportSub = $setting->footer_support_subheading[$locale] ?? ($setting->footer_support_subheading['en'] ?? __('Reach out to us through any of these support channels'));
            $rightsLine = $setting->rights_i18n[$locale] ?? ($setting->rights_i18n['en'] ?? ($setting->rights ?? __('All Rights Reserved')));
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
            $orderedApps = collect($appLinks)->filter(fn ($a) => ($a['enabled'] ?? false) && ($a['url'] ?? null))->sortBy('order');
            $labels = $setting->footer_labels ?? [];
            $translate = function ($key, $fallback) use ($labels, $locale) {
                return $labels[$key][$locale] ?? ($labels[$key]['en'] ?? __($fallback));
            };

            return [
                'setting' => $setting,
                'socialLinks' => $socialLinks,
                'supportHeading' => $supportHeading,
                'supportSub' => $supportSub,
                'rightsLine' => $rightsLine,
                'sections' => $sections,
                'paymentList' => $paymentList,
                'orderedApps' => $orderedApps,
                'helpCenterLabel' => $translate('help_center', 'Help Center'),
                'emailSupportLabel' => $translate('email_support', 'Email Support'),
                'phoneSupportLabel' => $translate('phone_support', 'Phone Support'),
                'appsHeading' => $translate('apps_heading', 'Shop on the go'),
                'socialHeading' => $translate('social_heading', 'Connect with us'),
            ];
        });

        $view->with($data);
    }
}
