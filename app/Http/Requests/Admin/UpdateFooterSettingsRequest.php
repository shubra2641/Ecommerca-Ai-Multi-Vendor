<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Using User::isAdmin() helper since hasRole() is not defined
        return (bool) ($this->user()?->isAdmin());
    }

    public function rules(): array
    {
        $langs = cache()->remember('active_lang_codes', 3600, function () {
            try {
                return \DB::table('languages')->where('is_active', 1)->pluck('code')->all();
            } catch (\Throwable $e) {
                return ['en'];
            }
        });
        $textJsonRule = ['nullable', 'array'];
        $rules = [
            'rights_i18n' => $textJsonRule,
            'footer_support_heading' => $textJsonRule,
            'footer_support_subheading' => $textJsonRule,
            'footer_labels' => ['nullable', 'array'],
            'footer_labels.help_center' => ['nullable', 'array'],
            'footer_labels.email_support' => ['nullable', 'array'],
            'footer_labels.phone_support' => ['nullable', 'array'],
            'footer_labels.apps_heading' => ['nullable', 'array'],
            'footer_labels.social_heading' => ['nullable', 'array'],
            'footer_payment_methods' => ['nullable', 'array'],
            'footer_payment_methods.*' => ['string', 'max:40'],
            'sections.support_bar' => ['nullable', 'boolean'],
            'sections.apps' => ['nullable', 'boolean'],
            'sections.social' => ['nullable', 'boolean'],
            'sections.payments' => ['nullable', 'boolean'],
            'app_links' => ['nullable', 'array'],
            'app_links.*.url' => ['nullable', 'url'],
            'app_links.*.enabled' => ['nullable', 'boolean'],
            'app_links.*.order' => ['nullable', 'integer', 'min:0', 'max:50'],
            'app_links.*.image' => ['sometimes', 'file', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:512'],
            'app_links.*.existing_image' => ['nullable', 'string', 'max:255'],
        ];

        foreach ($langs as $code) {
            $rules["rights_i18n.$code"] = ['nullable', 'string', 'max:255'];
            $rules["footer_support_heading.$code"] = ['nullable', 'string', 'max:120'];
            $rules["footer_support_subheading.$code"] = ['nullable', 'string', 'max:180'];
            $rules["footer_labels.help_center.$code"] = ['nullable', 'string', 'max:120'];
            $rules["footer_labels.email_support.$code"] = ['nullable', 'string', 'max:120'];
            $rules["footer_labels.phone_support.$code"] = ['nullable', 'string', 'max:120'];
            $rules["footer_labels.apps_heading.$code"] = ['nullable', 'string', 'max:120'];
            $rules["footer_labels.social_heading.$code"] = ['nullable', 'string', 'max:120'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->footer_payment_methods)) {
            $this->merge([
                'footer_payment_methods' => array_values(
                    array_filter(
                        array_map('trim', preg_split('/[\r\n]+/', $this->footer_payment_methods))
                    )
                ),
            ]);
        }

        // Normalize app link URLs: add https:// if missing scheme
        $appLinks = $this->input('app_links', []);
        $changed = false;
        foreach ($appLinks as $k => $row) {
            if (! empty($row['url']) && is_string($row['url'])) {
                $u = trim($row['url']);
                if ($u !== '' && ! preg_match('~^https?://~i', $u)) {
                    $appLinks[$k]['url'] = 'https://' . $u;
                    $changed = true;
                }
            }
        }
        if ($changed) {
            $this->merge(['app_links' => $appLinks]);
        }
    }
}
