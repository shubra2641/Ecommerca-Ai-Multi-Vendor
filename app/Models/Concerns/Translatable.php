<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Trait Translatable
 *
 * Assumes model has one or more base attributes (e.g. name, description) and
 * corresponding JSON columns named <attribute>_translations that store a map:
 *   { "en": "Name", "ar": "..." }
 * When accessing $model->name (attribute get) the current locale value will be returned
 * automatically if available. Fallback order:
 *   1. translations[current_locale]
 *   2. translations[app.fallback_locale]
 *   3. base attribute value
 *
 * To enable on a model, add:
 *   use Translatable;
 *   protected array $translatable = ['name','description'];
 */
trait Translatable
{
    /**
     * Override getAttribute to inject translation resolution for configured attributes.
     */
    public function getAttribute($key)
    {
        // if key is explicitly requested translations array, return normal behavior
        if (isset($this->translatable) && in_array($key, $this->translatable, true)) {
            $translationsKey = $key.'_translations';
            $raw = parent::getAttribute($key); // base stored value
            $translations = parent::getAttribute($translationsKey);
            if (is_array($translations)) {
                $locale = app()->getLocale();
                $fallback = config('app.fallback_locale');
                if (isset($translations[$locale]) && $translations[$locale] !== '') {
                    return $translations[$locale];
                }
                if ($fallback && isset($translations[$fallback]) && $translations[$fallback] !== '') {
                    return $translations[$fallback];
                }
            }

            return $raw; // fallback to raw column value
        }

        return parent::getAttribute($key);
    }

    /**
     * Helper manual translation fetch if needed in code.
     */
    public function translate(string $field, ?string $locale = null)
    {
        if (! isset($this->translatable) || ! in_array($field, $this->translatable, true)) {
            return $this->getAttribute($field);
        }
        $translations = parent::getAttribute($field.'_translations');
        $locale = $locale ? $locale : app()->getLocale();
        $fallback = config('app.fallback_locale');
        if (is_array($translations)) {
            if (isset($translations[$locale]) && $translations[$locale] !== '') {
                return $translations[$locale];
            }
            if ($fallback && isset($translations[$fallback]) && $translations[$fallback] !== '') {
                return $translations[$fallback];
            }
        }

        return parent::getAttribute($field);
    }
}
