<?php

declare(strict_types=1);

namespace App\Concerns;

trait TranslatableTrait
{
    /**
     * Override getAttribute to inject translation resolution for configured attributes.
     */
    public function getAttribute($key)
    {
        if (! isset($this->translatable) || ! in_array($key, $this->translatable, true)) {
            return parent::getAttribute($key);
        }

        $translations = parent::getAttribute($key . '_translations');
        if (! is_array($translations)) {
            return parent::getAttribute($key);
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale');
        return $translations[$locale] ?? ($fallback ? $translations[$fallback] ?? parent::getAttribute($key) : parent::getAttribute($key));
    }

    /**
     * Helper manual translation fetch if needed in code.
     */
    public function translate(string $field, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale');
        $translations = parent::getAttribute($field . '_translations');
        if (is_array($translations)) {
            return $translations[$locale] ?? ($fallback ? $translations[$fallback] ?? $this->getAttribute($field) : $this->getAttribute($field));
        }
        return $this->getAttribute($field);
    }
}
