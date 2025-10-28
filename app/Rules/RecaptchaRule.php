<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RecaptchaRule implements ValidationRule
{
    public function __construct(
        private RecaptchaService $recaptchaService
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->recaptchaService->isEnabled()) {
            return; // Skip validation if reCAPTCHA is disabled
        }

        if (!$value) {
            $fail(__('The :attribute field is required.', ['attribute' => $attribute]));
            return;
        }

        if (!$this->recaptchaService->verify($value, request()->ip())) {
            $fail(__('The :attribute verification failed. Please try again.', ['attribute' => $attribute]));
        }
    }
}
