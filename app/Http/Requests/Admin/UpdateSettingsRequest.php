<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    private const ALLOWED_FONTS = [
        'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat',
        'Source Sans Pro', 'Oswald', 'Raleway', 'PT Sans', 'Lora',
        'Nunito', 'Poppins', 'Playfair Display', 'Merriweather', 'Ubuntu',
        'Crimson Text', 'Work Sans', 'Fira Sans', 'Noto Sans', 'Dancing Script',
        'Roboto Slab', 'Source Serif Pro', 'Libre Baskerville', 'Quicksand',
        'Rubik', 'Barlow', 'DM Sans', 'Manrope', 'Space Grotesk', 'Plus Jakarta Sans',
        'Noto Sans Arabic', 'Cairo', 'Tajawal', 'Almarai', 'Amiri',
        'Scheherazade New', 'Markazi Text', 'Reem Kufi', 'IBM Plex Sans Arabic',
        'Changa', 'El Messiri', 'Harmattan', 'Lateef', 'Aref Ruqaa',
        'Katibeh', 'Lalezar', 'Mirza',
    ];

    public function authorize(): bool
    {
        // Authorization is handled by middleware / policies elsewhere
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}\s\-\.\(\)]+$/u',
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,svg',
                'max:2048',
                'dimensions:max_width=2000,max_height=2000',
            ],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'custom_css' => ['nullable', 'string', 'max:10000'],
            'custom_js' => ['nullable', 'string', 'max:10000'],
            'rights' => ['nullable', 'string', 'max:255'],
            'font_family' => ['nullable', 'string', 'max:100'],
            'auto_publish_reviews' => ['nullable', 'in:0,1'],
            'maintenance_enabled' => ['nullable', 'in:0,1'],
            'maintenance_reopen_at' => ['nullable', 'date'],
            'maintenance_message' => ['nullable', 'array'],
            'min_withdrawal_amount' => ['nullable', 'numeric', 'min:0'],
            'withdrawal_gateways' => ['nullable', 'string', 'max:2000'],
            'withdrawal_commission_enabled' => ['nullable', 'in:0,1'],
            'withdrawal_commission_rate' => ['nullable', 'numeric', 'min:0'],
            'commission_mode' => ['nullable', 'in:flat,category'],
            'commission_flat_rate' => ['nullable', 'numeric', 'min:0'],
            // AI settings
            'ai_enabled' => ['nullable', 'in:0,1'],
            'ai_provider' => ['nullable', 'string', 'in:openai'],
            // Accept typical key length; don't force pattern yet to allow different formats
            'ai_openai_api_key' => ['nullable', 'string', 'max:255'],
            // External Payment settings
            'enable_external_payment_redirect' => ['nullable', 'in:0,1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate font family against allowed list
            $value = $this->input('font_family');
            if ($value && ! in_array($value, self::ALLOWED_FONTS, true)) {
                $validator->errors()->add('font_family', __('The selected font family is not allowed.'));
            }

            // Custom code safety checks for CSS/JS
            $this->validateCustomCode($validator, $this->input('custom_css', ''), 'custom_css');
            $this->validateCustomCode($validator, $this->input('custom_js', ''), 'custom_js');
        });
    }

    private function validateCustomCode($validator, ?string $code, string $field): void
    {
        if ($code === null || $code === '') {
            return;
        }

        $dangerousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
            '/onmouseover=/i',
            '/eval\s*\(/i',
            '/document\.write/i',
            '/innerHTML/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $code)) {
                $validator->errors()->add(
                    $field,
                    __('The :attribute contains potentially dangerous code.', ['attribute' => $field])
                );
                break;
            }
        }
    }
}
