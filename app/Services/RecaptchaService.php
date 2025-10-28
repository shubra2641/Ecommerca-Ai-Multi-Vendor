<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Verify reCAPTCHA response
     */
    public function verify(string $response, ?string $remoteIp = null): bool
    {
        $setting = Setting::first();

        $secretKey = $setting?->recaptcha_secret_key ?: config('services.recaptcha.secret_key');

        if (!$setting || !$setting->recaptcha_enabled || !$secretKey) {
            return true; // If reCAPTCHA is disabled, always return true
        }

        try {
            $result = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $response,
                'remoteip' => $remoteIp,
            ]);

            $data = $result->json();

            return isset($data['success']) && $data['success'] === true;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if reCAPTCHA is enabled
     */
    public function isEnabled(): bool
    {
        $setting = Setting::first();
        $siteKey = $setting?->recaptcha_site_key ?: config('services.recaptcha.site_key');
        return $setting && $setting->recaptcha_enabled && $siteKey;
    }

    /**
     * Get site key for frontend
     */
    public function getSiteKey(): ?string
    {
        $setting = Setting::first();
        return $setting?->recaptcha_site_key ?: config('services.recaptcha.site_key');
    }
}
