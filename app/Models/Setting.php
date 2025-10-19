<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'site_name',
        'logo',
        'seo_description',
        'contact_email',
        'contact_phone',
        'custom_css',
        'custom_js',
        'rights',
        'rights_i18n',
        'maintenance_enabled',
        'maintenance_message',
        'maintenance_reopen_at',
        'min_withdrawal_amount',
        'withdrawal_gateways',
        'withdrawal_commission_enabled',
        'withdrawal_commission_rate',
        'commission_mode',
        'commission_flat_rate',
        'font_family',
        'auto_publish_reviews',
        // Footer
        'footer_app_links',
        'footer_support_heading',
        'footer_support_subheading',
        'footer_sections_visibility',
        'footer_payment_methods',
        'footer_labels',
        // AI Assist
        'ai_enabled',
        'ai_provider',
        'ai_openai_api_key',
        // External Payment
        'enable_external_payment_redirect',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'maintenance_enabled' => 'boolean',
        'maintenance_message' => 'array',
        'maintenance_reopen_at' => 'datetime',
        'withdrawal_gateways' => 'array',
        'min_withdrawal_amount' => 'decimal:2',
        'withdrawal_commission_enabled' => 'boolean',
        'withdrawal_commission_rate' => 'decimal:2',
        'commission_flat_rate' => 'decimal:2',
        'commission_mode' => 'string',
        'footer_app_links' => 'array',
        'footer_support_heading' => 'array',
        'footer_support_subheading' => 'array',
        'footer_sections_visibility' => 'array',
        'footer_payment_methods' => 'array',
        'rights_i18n' => 'array',
        'footer_labels' => 'array',
        'ai_enabled' => 'boolean',
        'enable_external_payment_redirect' => 'boolean',
    ];

    /**
     * Get the site name attribute with XSS protection.
     */
    protected function siteName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? htmlspecialchars_decode($value, ENT_QUOTES) : null,
            set: fn (?string $value) => $value ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : null,
        );
    }

    /**
     * Get the SEO description attribute with XSS protection.
     */
    protected function seoDescription(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? htmlspecialchars_decode($value, ENT_QUOTES) : null,
            set: fn (?string $value) => $value ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : null,
        );
    }


    /**
     * Get the font family attribute with validation.
     */
    protected function fontFamily(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ?: 'Inter',
            set: function (?string $value) {
                $allowedFonts = [
                    // Latin Fonts
                    'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat',
                    'Source Sans Pro', 'Oswald', 'Raleway', 'PT Sans', 'Lora',
                    'Nunito', 'Poppins', 'Playfair Display', 'Merriweather', 'Ubuntu',
                    'Crimson Text', 'Work Sans', 'Fira Sans', 'Noto Sans', 'Dancing Script',
                    // Additional Latin Fonts
                    'Roboto Slab', 'Source Serif Pro', 'Libre Baskerville', 'Quicksand',
                    'Rubik', 'Barlow', 'DM Sans', 'Manrope', 'Space Grotesk', 'Plus Jakarta Sans',
                    // Arabic Fonts
                    'Noto Sans Arabic', 'Cairo', 'Tajawal', 'Almarai', 'Amiri',
                    'Scheherazade New', 'Markazi Text', 'Reem Kufi', 'IBM Plex Sans Arabic',
                    'Changa', 'El Messiri', 'Harmattan', 'Lateef', 'Aref Ruqaa',
                    'Katibeh', 'Lalezar', 'Mirza',
                ];

                return $value && in_array($value, $allowedFonts, true) ? $value : 'Inter';
            },
        );
    }

    /**
     * Validate and sanitize URL fields.
     */
    private function sanitizeUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $sanitized = filter_var($value, FILTER_SANITIZE_URL);

        return filter_var($sanitized, FILTER_VALIDATE_URL) ? $sanitized : null;
    }

    // Removed legacy social media attribute mutators (now handled by SocialLink model)

    /**
     * Rights / copyright footer line.
     */
    protected function rights(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? htmlspecialchars_decode($value, ENT_QUOTES) : null,
            set: fn (?string $value) => $value ? htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8') : null,
        );
    }

    /**
     * Decrypt AI OpenAI API key transparently when accessing.
     */
    protected function aiOpenaiApiKey(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }
                try {
                    // Attempt decrypt; if it fails assume already plain
                    return decrypt($value);
                } catch (\Throwable $e) {
                    return $value;
                }
            },
            set: fn ($value) => $value // set handled in controller (encryption)
        );
    }

    /**
     * Safe accessor for enable_external_payment_redirect when the column may be missing.
     * Returns boolean false if attribute is not present in model attributes array.
     */
    protected function enableExternalPaymentRedirect(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->attributes['enable_external_payment_redirect']) ? (bool) $value : false,
            set: fn ($value) => (bool) $value
        );
    }
}
